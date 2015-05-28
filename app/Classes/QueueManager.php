<?php

namespace App\Classes;

use Core\App;
use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

class QueueManager
{

    private $connectionData;

    private $pheanstalk;

    private $watchDelay = PheanstalkInterface::DEFAULT_DELAY;

    /**
     * Проверка Tube на валидность
     *
     * @param string $name
     * @return bool
     */
    private function validateTube($name)
    {

        if (!isset($this->connectionData['tube'][$name]))
            return false;

        return true;
    }

    public function __construct()
    {

        $this->connectionData = App::getParams('beanstalkd');

        $this->pheanstalk = new Pheanstalk($this->connectionData['host'], $this->connectionData['port']);
    }


    /**
     * Установка задержки на выполнения проверки активных заданий
     *
     * @param int $delay
     * @return self
     */
    public function setWatchDelay($delay)
    {

        $this->watchDelay = $delay * 1;

        return $this;
    }

    /**
     * Получение текущей задержки на выполнения проверки активных заданий
     *
     * @return int $delay
     */
    public function getWatchDelay()
    {

        return $this->watchDelay;
    }

    /**
     * Выполнять $closure пока соединение открыто.
     *
     * @param callable $closure
     * @return void
     */
    public function loop(callable $closure)
    {

        while ($this->pheanstalk->getConnection()->isServiceListening()) {

            $closure($this->pheanstalk, $this->connectionData);
        }
    }

    /**
     * Проверка наличия активных заданий, если есть, выполнить $closure.
     *
     * @param string $tube
     * @param callable $closure
     * @return void
     */
    public function watch($tube, callable $closure)
    {

        if (!$this->validateTube($tube))
            throw new \RuntimeException('Tube not found.');

        if ($job = $this->pheanstalk->watchOnly($this->connectionData['tube'][$tube])->reserve($this->getWatchDelay())) {

            $data = $job->getData();

            $this->pheanstalk->delete($job);

            $closure($data);
        }
    }

    /**
     * Проверка наличия активных заданий, если есть, выполнить $closure.
     *
     * @param string $tube
     * @param callable $closure
     * @param callable $callback
     * @return void
     */
    public function watchCallback($tube, callable $closure, callable $callback)
    {

        if (!$this->validateTube($tube))
            throw new \RuntimeException('Tube not found.');

        $ready = false;

        if ($job = $this->pheanstalk->watchOnly($this->connectionData['tube'][$tube])->reserve($this->getWatchDelay())) {

            $data = $job->getData();

            $this->pheanstalk->delete($job);

            $closure($data);

            $ready = true;
        }

        $callback($tube, $ready);
    }

    /**
     * Добавление задания в очередь
     *
     * @param string $tube
     * @param string $data
     * @param int $priority
     * @param int $delay
     * @return int $jobID
     */
    public function push($tube, $data, $priority = PheanstalkInterface::DEFAULT_PRIORITY, $delay = PheanstalkInterface::DEFAULT_DELAY)
    {

        if (!$this->validateTube($tube))
            throw new \RuntimeException('Tube not found.');

        return $this->pheanstalk->putInTube($this->connectionData['tube'][$tube], $data, $priority, $delay);
    }
}
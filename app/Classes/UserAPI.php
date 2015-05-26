<?php
namespace App\Classes;

use Core\App;

class UserAPI
{

    private $lastRecipients = [];

    /**
     * Метод для записи последних получателей уведомления
     *
     * @param \App\Entity\User[] $users
     * @return \App\Classes\UserAPI
     */
    private function setLastRecipients(array $users)
    {

        $this->lastRecipients = $users;

        return $this;
    }

    /**
     * Поличение списка последних людей, которым было отправдело уведомление
     *
     * @return \App\Entity\User[]
     */
    public function getLastRecipients()
    {

        return $this->lastRecipients;
    }

    /**
     * Поиск пользователей по массиву идентификаторов
     *
     * @param array $ids
     * @param integer $limit
     * @return \App\Entity\User[]|null
     */
    protected function findUsersByIds(array $ids, $limit = 100)
    {

        return App::getDI()->get('entityManager')
            ->getRepository('Entity:User')
            ->findById($ids, null, $limit);
    }

    /**
     * Счетчик вызова метода за указанное время
     *
     * @param string $sessionName
     * @param float $timeLimit
     * @param integer $requestLimit
     * @return bool
     */
    protected function accessCounter($sessionName, $timeLimit = 1, $requestLimit = 3)
    {

        $cacheDriver = App::getDI()->get('cache');

        $counter = unserialize($cacheDriver->fetch($sessionName));

        $currentTimestamp = microtime(true);

        $firstRequestTime = $currentTimestamp - (float)$counter['timestamp'];

        if ($firstRequestTime < $timeLimit && $counter['operations'] < $requestLimit) {

            ++$counter['operations'];

        } elseif ($firstRequestTime > $timeLimit) {

            $counter['operations'] = 1;

            $counter['timestamp'] = $currentTimestamp;
        } else {

            return false;
        }

        $cacheDriver->save($sessionName, serialize($counter));

        return true;
    }


    /**
     * Метод отправки данных в некую API
     *
     * @param \App\Entity\User[] $users
     * @param string $text
     * @return bool
     */
    protected function sendToApi(array $users, $text)
    {

        return true;
    }


    /**
     * Метод отправки сообщений пользователям из списка ( задание )
     *
     * @param array $ids
     * @param string $text
     * @return bool
     */
    public function sendPushNotification(array $ids, $text)
    {

        if ($this->accessCounter('push-notification-counter') && $users = $this->findUsersByIds($ids)) {

            $this->sendToApi($users, $text);

            $this->setLastRecipients($users);

            return true;
        }

        return false;
    }
}
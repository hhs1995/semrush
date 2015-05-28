<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command;
use App\Classes\QueueManager;

class NotificationsWorkerPushCommand extends Command
{
    private $apiUrl = 'http://api.blabla.ru/pushNotification';

    protected function configure()
    {
        $this
            ->setName('notifications:worker:push')
            ->setDescription('Отправка сообщений в некую API');
    }

    protected function execute()
    {

        $QueueManager = new QueueManager;

        $QueueManager->loop(function() use($QueueManager){

            $QueueManager->watch('notifications:push',

                function($data){

                    // Тут логика отправки куда-то, может быть все, что угодно
                    /*
                    $client = new \GuzzleHttp\Client();

                    $res = $client->putAsync($this->apiUrl,[
                            'json' => json_decode($data, true)
                        ]);
                    */
                    echo $data;
                });
        });
    }
}
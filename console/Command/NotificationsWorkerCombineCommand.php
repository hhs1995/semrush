<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use App\Classes\UserAPI;
use App\Classes\QueueManager;

class NotificationsWorkerCombineCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('notifications:worker:combine')
            ->setDescription('Компановка сообщений за определенный период и отправка')
            ->addArgument(
                'delay',
                InputArgument::OPTIONAL,
                'Установка периода времени, за который проверять сообщения и выставлять в очередь на отправку',
                1
            );
    }

    protected function execute(InputInterface $input)
    {
        $delay = $input->getArgument('delay');

        $api = new UserAPI();

        $QueueManager = new QueueManager;

        $QueueManager->loop(function() use($QueueManager, $api, $delay){

            $QueueManager->setWatchDelay($delay);

            $QueueManager->watchCallback('notifications:combine',

                function() use($QueueManager, $api){

                    if ($notifications = $api->getActiveNotifications()) {

                        $api->clearActiveNotifications();

                        $ids = [];

                        array_walk($notifications, function ($item) use (&$ids) {

                            $ids = array_merge($ids, $item['ids']);
                        });

                        $ids = array_unique($ids);

                        $users = $api->findUsersByIds($ids, null, \Doctrine\ORM\Query::HYDRATE_ARRAY);

                        foreach ($notifications as $item) {

                            foreach ($item['ids'] as $id) {

                                if (isset($users[$id]))
                                    $api->pushNotification($id, $users[$id], $item['text']);
                            }
                        }
                    }
                },
                function($tube, $ready) use($QueueManager){

                    if(!$ready)
                        $QueueManager->push('notifications:combine', null);
                });
        });
    }
}
<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command;
use Pheanstalk\Pheanstalk;
use App\Classes\UserAPI;

class NotificationsWorkerCommand extends Command
{

    const TIMEOUT = 3;

    protected function configure()
    {
        $this
            ->setName('notifications:worker')
            ->setDescription('Воркер для отправки сообщений');
    }

    protected function execute()
    {

        $pheanstalk = new Pheanstalk('127.0.0.1', 11300);

        $api = new UserAPI();

        while ($pheanstalk->getConnection()->isServiceListening()) {

            if ($job = $pheanstalk->watchOnly('notifications')->reserve(self::TIMEOUT)) {

                $pheanstalk->delete($job);

                if ($notifications = $api->getActiveNotifications()) {

                    $api->clearActiveNotifications();

                    $ids = [];

                    foreach ($notifications as $v) {
                        foreach ($v['ids'] as $vv)
                            $ids[$vv] = $vv;
                    }

                    $users = $api->findUsersByIds($ids, null, \Doctrine\ORM\Query::HYDRATE_ARRAY);

                    foreach ($notifications as $v) {

                        foreach ($v['ids'] as $vv) {

                            if (isset($users[$vv])) {

                                echo $api->prepareNotificationText($users[$vv], $v['text']);
                            }
                        }
                    }
                }

            } else {

                $pheanstalk->putInTube('notifications', null);
            }
        }
    }
}
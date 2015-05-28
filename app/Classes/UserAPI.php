<?php
namespace App\Classes;

use Core\App;

class UserAPI
{

    const COUNTER_CACHE_KEY = 'push-notification-counter';
    const NOTIFICATIONS_CACHE_KEY = 'notifications';

    /**
     * Поиск пользователей по массиву идентификаторов
     *
     * @param array $ids
     * @param integer $limit
     * @return \App\Entity\User[]|null
     */
    public function findUsersByIds(array $ids, $limit = null, $hydrationMode = null)
    {

        $query = App::getDI()->get('entityManager')
            ->getRepository('Entity:User')
            ->createQueryBuilder('e')
            ->andWhere('e.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->setDQL(str_replace('WHERE', 'INDEX BY e.id WHERE', $query->getDQL()))
            ->getResult($hydrationMode);
    }

    /**
     * Счетчик вызова метода за указанное время
     *
     * @param string $sessionName
     * @param float $timeLimit
     * @param integer $requestLimit
     * @return bool
     */
    protected function accessCounter($timeLimit = 1, $requestLimit = 3)
    {

        $cacheDriver = App::getDI()->get('cache');

        $counter = unserialize($cacheDriver->fetch(self::COUNTER_CACHE_KEY));

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

        $cacheDriver->save(self::COUNTER_CACHE_KEY, serialize($counter));

        return true;
    }


    /**
     * Добавление сообщений в список на отправку
     *
     * @param array $newNotifications
     * @return bool
     */
    public function addActiveNotifications(array $newNotifications)
    {

        $cacheDriver = App::getDI()->get('cache');

        $notifications = unserialize($cacheDriver->fetch(self::NOTIFICATIONS_CACHE_KEY));

        $notifications = $notifications ? $notifications : [];

        $notifications[] = $newNotifications;

        return $cacheDriver->save(self::NOTIFICATIONS_CACHE_KEY, serialize($notifications));
    }


    /**
     * Получение списка сообщений на отправку
     *
     * @return array $notifications
     */
    public function getActiveNotifications()
    {

        $cacheDriver = App::getDI()->get('cache');

        $notifications = unserialize($cacheDriver->fetch(self::NOTIFICATIONS_CACHE_KEY));

        return $notifications;
    }


    /**
     * Удаления списка сообщений на отправку
     *
     * @return bool
     */
    public function clearActiveNotifications()
    {

        $cacheDriver = App::getDI()->get('cache');

        return $cacheDriver->delete(self::NOTIFICATIONS_CACHE_KEY);
    }


    /**
     * Метод подготовки текста сообщения к отправке (замена спец. слов в шаблоне реальными значениями)
     *
     * @param array $replace
     * @param string $text
     * @return string $text
     */
    public function prepareNotificationText(array $replace, $text)
    {

        return preg_replace_callback(
            '/(%)([a-zA-Z]{1,})(%)/',
            function ($matches) use ($replace) {
                return isset($replace[$matches[2]]) ? $replace[$matches[2]] : '';
            },
            $text
        );
    }


    /**
     * Метод подготовки сообщения и отправки его в очередь
     *
     * @param integer $ids
     * @param array $userData
     * @param string $text
     * @return integer $jobId
     */
    public function pushNotification($id, array $userData, $text)
    {

        $data = [
            'id' => $id,
            'text' => $this->prepareNotificationText($userData, $text)
        ];

        $QueueManager = new QueueManager;

        return $QueueManager->push('notifications:push', json_encode($data));
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

        if (100 < count($ids)) {

            throw new \RuntimeException('Too much ids.');
        } elseif (!$this->accessCounter()) {

            throw new \RuntimeException('Too much requests.');
        } else {

            return $this->addActiveNotifications([
                'ids' => $ids,
                'text' => $text
            ]);
        }
    }
}
<?php

namespace Core;

class App
{
    private static $container;

    /**
     * Метод установки DI контейнера
     *
     * @param \DI\ContainerInterface $id
     * @return void
     */
    public static function setDI(\DI\ContainerInterface $di)
    {
        self::$container = $di;
    }


    /**
     * Метод получения DI контейнера
     *
     * @return \DI\ContainerInterface
     */
    public static function getDI()
    {

        return self::$container;
    }
}
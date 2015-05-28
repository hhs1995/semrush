<?php

namespace Core;

class App
{
    private static $container;

    private static $params;

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


    /**
     * Установка параметров приложения
     *
     * @param array $params
     * @return void
     */
    public static function setParams(array $params)
    {

        self::$params = $params;
    }

    /**
     * Получение параметров приложения
     *
     * @return array $params
     */
    public static function getParams($name = null)
    {

        if ($name)
            return isset(self::$params[$name]) ? self::$params[$name] : [];

        return self::$params;
    }
}
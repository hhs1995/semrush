<?php

namespace Core;

class App
{
    private static $container;

    public static function setDI(\DI\ContainerInterface $di)
    {

        self::$container = $di;
    }

    public static function getDI(){

        return self::$container;
    }
}
<?php

namespace Fbartz\BrowserGames\Repository;

trait SingletonRepository
{

    private static $instance;

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        if(method_exists(self::$instance,'initialize')){
            self::$instance->initialize();
        }

        return self::$instance;
    }

}
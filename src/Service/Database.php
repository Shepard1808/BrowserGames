<?php

namespace Fbartz\BrowserGames\Service;

use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;

class Database
{

    private static Database $instance;

    protected static ConnectionInterface $con;
    protected static array $dbCredentials = [];

    protected function __construct()
    {
    }

    public static function getConnection(): ConnectionInterface
    {
        return self::$con;
    }

    protected function __clone()
    {
    }

    public static function getInstance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        if (method_exists(self::$instance, 'initialize')) {
            self::$instance->initialize();
        }

        return self::$instance;
    }

    private static function setCredentials(string $name, string $password, string $dbName = "Party", string $dbUrl = "127.0.0.1")
    {
        self::$dbCredentials = ['username' => $name, "password" => $password, 'dbname' => $dbName, "dbOrigin" => $dbUrl];

    }

    public static function initConnection(array $credentials): void
    {
        self::setCredentials($credentials["dbUsername"], $credentials["dbPassword"], $credentials["dbName"], $credentials["dbURL"]);
        $factory = new Factory();
        self::$con = $factory->createLazyConnection(
            self::$dbCredentials['username'] . ':' . self::$dbCredentials['password'] . '@' .
            self::$dbCredentials['dbOrigin'] . '/' . self::$dbCredentials['dbname']);
    }


}
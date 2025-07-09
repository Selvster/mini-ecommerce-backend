<?php
namespace App\Database;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

class Connection
{
    private static ?DBALConnection $instance = null;

    private function __construct() {} // Private constructor to enforce singleton

    public static function getInstance(): DBALConnection
    {
        if (self::$instance === null) {
            if (!isset($_ENV['DB_HOST'])) {
                 $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
                 $dotenv->load();
            }

            $connectionParams = [
                'dbname' => $_ENV['DB_DATABASE'],
                'user' => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'driver' => 'pdo_mysql',
            ];

            self::$instance = DriverManager::getConnection($connectionParams);
        }
        return self::$instance;
    }
}
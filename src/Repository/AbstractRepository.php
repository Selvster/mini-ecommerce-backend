<?php

namespace App\Repository;

use App\Database\Connection;
use Doctrine\DBAL\Connection as DBALConnection;

abstract class AbstractRepository
{
    protected DBALConnection $dbConnection;

    public function __construct()
    {
        $this->dbConnection = Connection::getInstance();
    }
}
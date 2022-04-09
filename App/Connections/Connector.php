<?php

namespace App\Connections;

use App\Drivers\ConnectionInterface;
use App\Drivers\PdoConnectionDriver;
use PDOException;

abstract class Connector implements ConnectorInterface
{
    public function getConnection(): ConnectionInterface
    {
        try{
            $dbh = PdoConnectionDriver::getInstance(
                $this->getDsn(),
                $this->getUserName(),
                $this->getPassword(),
                $this->getOptions()
            );
        } catch (PDOException $e) {
            print $e->getMessage();
            die();
        }
        return $dbh;
    }

    abstract public function getDsn(): string;
    abstract public function getUserName(): string;
    abstract public function getPassword(): string;
    abstract public function getOptions(): array;
}
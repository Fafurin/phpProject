<?php

namespace App\Connections;

use App\Drivers\ConnectionInterface;

interface ConnectorInterface
{
    public function getConnection(): ConnectionInterface;
}
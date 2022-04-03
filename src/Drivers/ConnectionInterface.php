<?php

namespace App\Drivers;

interface ConnectionInterface
{
    public function executeQuery(string $query, array $params);
}
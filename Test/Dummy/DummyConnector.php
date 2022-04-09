<?php

namespace Test\Dummy;

use App\Drivers\ConnectionInterface;

class DummyConnector extends \PDO implements ConnectionInterface
{
    public function executeQuery(string $query, array $params)
    {
        // TODO: Implement executeQuery() method.
    }
}
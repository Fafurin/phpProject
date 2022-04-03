<?php

namespace App\Repositories;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Drivers\ConnectionInterface;
use App\Entities\EntityInterface;

abstract class EntityRepository implements EntityRepositoryInterface
{
    protected ConnectionInterface $connection;
    private ?ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqLiteConnector();
        $this->connection = $this->connector->getConnection();
    }

    abstract public function get(int $id): EntityInterface;

}
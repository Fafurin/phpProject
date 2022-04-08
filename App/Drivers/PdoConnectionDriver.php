<?php

namespace App\Drivers;

class PdoConnectionDriver extends \PDO implements ConnectionInterface
{
    protected static array $instances = [];

    public function __construct(string $dsn, string $userName = null, string $password = null, array $options = null)
    {
        parent::__construct($dsn, $userName, $password, $options);
    }

    public static function getInstance(string $dsn, ?string $userName = null, ?string $password = null, array $options = null): self{
        $class = static::class;
        if (!isset(self::$instances[$class])){
            self::$instances[$class] = new static($dsn, $userName, $password, $options);
        }
        return self::$instances[$class];
    }

    public function executeQuery(string $query, array $params)
    {
        $this->prepare($query)->execute($params);
    }
}
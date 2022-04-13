<?php

namespace Test\Repositories;

use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Test\Dummy\DummyConnector;

class UserRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $userRepository = new UserRepository($this->getConnection());

        $this->expectException(UserNotFoundException::class);

        $this->expectExceptionMessage('User not found');

        $userRepository->getUser($statementStub);
    }

    private function getConnection(): ConnectionInterface{
        return $this->getContainer()->get(ConnectionInterface::class);
    }

    private function getContainer(): ContainerInterface {
        $container = DIContainer::getInstance();

        $container->bind(
            ConnectionInterface::class,
            new DummyConnector(SqLiteConfig::DSN)
        );
        return $container;
    }
}
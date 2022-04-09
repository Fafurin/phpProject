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
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class UserRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $userRepository = new UserRepository($this->getConnection(), $this->getLogger());

        $this->expectException(UserNotFoundException::class);

        $this->expectExceptionMessage('User not found');

        $userRepository->getUser($statementStub);
    }

    private function getLogger(): LoggerInterface{
        return $this->getContainer()->get(LoggerInterface::class);
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

        $container->bind(
            LoggerInterface::class,
            new DummyLogger()
        );
        return $container;
    }
}
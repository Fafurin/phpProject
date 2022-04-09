<?php

namespace Test\Commands;

use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteUserCommandHandler;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class DeleteUserCommandTest extends TestCase
{

    public function testItRemovesUserFromDatabase(): void
    {

        $userRepository = new UserRepository($this->getConnection(), $this->getLogger());

        $deleteUserCommandHandler = new DeleteUserCommandHandler($userRepository, $this->getConnection(), $this->getLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $command = new DeleteEntityCommand(404);

        $deleteUserCommandHandler->handle($command);
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
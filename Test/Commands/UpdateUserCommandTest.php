<?php

namespace Test\Commands;

use App\Commands\UpdateEntityCommand;
use App\Commands\UpdateUserCommandHandler;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class UpdateUserCommandTest extends TestCase
{

    public function testItChangesUserInDatabase(): void
    {

        $userRepository = new UserRepository($this->getConnection(), $this->getLogger());

        $updateUserCommandHandler = new UpdateUserCommandHandler($userRepository, $this->getConnection(), $this->getLogger());

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $this->expectException(UserEmailExistException::class);
        $this->expectExceptionMessage("A user with this email already exists");

        $command = new UpdateEntityCommand(
            new User(
                'TestUpdateUserFirstName',
                'TestUpdateUserLastName',
                'testUpdate@user.ru'
            ), 404
        );
        $updateUserCommandHandler->handle($command);
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
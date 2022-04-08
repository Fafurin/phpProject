<?php

namespace Test\Commands;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateUserCommandHandler;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $repository = new UserRepository($connectionStub);

        $createUserCommandHandler = new CreateUserCommandHandler($repository);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $command = new CreateEntityCommand(
            new User(
                'TestUserFirstName',
                'TestUserLastName',
                'test@user.ru'));

        $createUserCommandHandler->handle($command);
    }
}
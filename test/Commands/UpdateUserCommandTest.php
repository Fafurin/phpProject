<?php

namespace Test\Commands;

use App\Commands\UpdateEntityCommand;
use App\Commands\UpdateUserCommandHandler;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;

class UpdateUserCommandTest extends TestCase
{

    public function testItChangesUserInDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $userRepository = new UserRepository($connectionStub);

        $updateUserCommandHandler = new UpdateUserCommandHandler($userRepository, $connectionStub);

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
}
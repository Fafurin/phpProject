<?php

namespace Test\Commands;

use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteUserCommandHandler;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;

class DeleteUserCommandTest extends TestCase
{

    public function testItRemovesUserFromDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $userRepository = new UserRepository($connectionStub);

        $deleteUserCommandHandler = new DeleteUserCommandHandler($userRepository, $connectionStub);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");

        $command = new DeleteEntityCommand(404);

        $deleteUserCommandHandler->handle($command);
    }
}
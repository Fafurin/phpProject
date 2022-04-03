<?php

namespace Test\Repositories;

use App\Connections\SqLiteConnector;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $sqLiteConnectorStub = $this->createStub(SqLiteConnector::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $userRepository = new UserRepository($sqLiteConnectorStub);

        $this->expectException(UserNotFoundException::class);

        $this->expectExceptionMessage('User not found');

        $userRepository->getUser($statementStub);

    }
}
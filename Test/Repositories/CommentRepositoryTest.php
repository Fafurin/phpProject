<?php

namespace Test\Repositories;

use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Test\Dummy\DummyConnector;

class CommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound()
    {
        $connectionStub = $this->createStub(PdoConnectionDriver::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $this->assertInstanceOf(PdoConnectionDriver::class, $connectionStub);

        $userRepository = new UserRepository($this->getConnection());

        $commentRepository = new CommentRepository($this->getConnection(), $userRepository);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);

        $this->assertInstanceOf(PDOStatement::class, $statementStub);
        $commentRepository->getComment($statementStub);
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
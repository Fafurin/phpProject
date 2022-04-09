<?php

namespace Test\Repositories;

use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class CommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound()
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $this->assertInstanceOf(SqLiteConnector::class, $connectionStub);

        $commentRepository = new CommentRepository($this->getConnection(), $this->getLogger());

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);

        $this->assertInstanceOf(PDOStatement::class, $statementStub);
        $commentRepository->getComment($statementStub);
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
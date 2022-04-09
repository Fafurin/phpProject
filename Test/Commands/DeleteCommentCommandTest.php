<?php

namespace Test\Commands;

use App\Commands\DeleteCommentCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class DeleteCommentCommandTest extends TestCase
{
    public function testItRemovesCommentFromDatabase(): void{

        $commentRepository = new CommentRepository($this->getConnection(), $this->getLogger());

        $deleteCommentCommandHandler = new DeleteCommentCommandHandler($commentRepository, $this->getConnection(), $this->getLogger());

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $command = new DeleteEntityCommand(56);

        $deleteCommentCommandHandler->handle($command);
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
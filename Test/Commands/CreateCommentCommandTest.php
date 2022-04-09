<?php

namespace Test\Commands;

use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class CreateCommentCommandTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $createCommentCommandHandler = new CreateCommentCommandHandler($this->getConnection(), $this->getLogger());

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $command = new CreateEntityCommand(
            new Comment(
                10,
                33,
                33,
                'testItSavesCommentToDatabase!'
            )
        );
        $createCommentCommandHandler->handle($command);
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
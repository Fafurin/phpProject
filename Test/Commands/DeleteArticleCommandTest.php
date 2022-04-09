<?php

namespace Test\Commands;

use App\Commands\DeleteArticleCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class DeleteArticleCommandTest extends TestCase
{
    public function testItRemovesArticleFromDatabase(): void{

        $articleRepository = new ArticleRepository($this->getConnection(), $this->getLogger());

        $deleteArticleCommandHandler = new DeleteArticleCommandHandler($articleRepository, $this->getConnection(), $this->getLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $command = new DeleteEntityCommand(6);

        $deleteArticleCommandHandler->handle($command);
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
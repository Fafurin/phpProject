<?php

namespace Test\Commands;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateEntityCommand;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class CreateArticleCommandTest extends TestCase
{
    public function testItSavesArticleToDatabase(): void
    {

        $repository = new ArticleRepository($this->getConnection(),$this->getLogger());

        $createArticleCommandHandler = new CreateArticleCommandHandler($repository, $this->getConnection(), $this->getLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $command = new CreateEntityCommand(
            new Article(
                33,
                'testItSavesArticleToDatabase',
                'testItSavesArticleToDatabase')
        );

        $createArticleCommandHandler->handle($command);
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
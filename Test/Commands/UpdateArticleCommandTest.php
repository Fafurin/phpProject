<?php

namespace Test\Commands;

use App\Commands\UpdateArticleCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\ArticleTitleExistException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class UpdateArticleCommandTest extends TestCase
{
    public function testItChangesArticleInDatabase(): void
    {

        $articleRepository = new ArticleRepository($this->getConnection(), $this->getLogger());

        $updateArticleCommandHandler = new UpdateArticleCommandHandler($articleRepository, $this->getConnection(), $this->getLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $this->expectException(ArticleTitleExistException::class);
        $this->expectExceptionMessage("An article with this title already exists");

        $command = new UpdateEntityCommand(
            new Article(
                3,
                'Test title',
                'Test text'
            ), 19
        );
        $updateArticleCommandHandler->handle($command);
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
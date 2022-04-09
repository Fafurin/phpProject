<?php

namespace Test\Repositories;

use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Test\Dummy\DummyConnector;
use Test\Dummy\DummyLogger;

class ArticleRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenArticleNotFound()
    {

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $articleRepository = new ArticleRepository($this->getConnection(), $this->getLogger());

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $articleRepository->getArticle($statementStub);
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
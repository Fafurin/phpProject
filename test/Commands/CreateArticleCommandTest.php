<?php

namespace Test\Commands;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;

class CreateArticleCommandTest extends TestCase
{
    public function testItSavesArticleToDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $repository = new ArticleRepository($connectionStub);

        $createArticleCommandHandler = new CreateArticleCommandHandler($repository);

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
}
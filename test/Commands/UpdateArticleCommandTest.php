<?php

namespace Test\Commands;

use App\Commands\UpdateArticleCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\ArticleTitleExistException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;

class UpdateArticleCommandTest extends TestCase
{
    public function testItChangesArticleInDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $articleRepository = new ArticleRepository($connectionStub);

        $updateArticleCommandHandler = new UpdateArticleCommandHandler($articleRepository, $connectionStub);

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
}
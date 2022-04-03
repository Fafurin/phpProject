<?php

namespace Test\Commands;

use App\Commands\DeleteArticleCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use PHPUnit\Framework\TestCase;

class DeleteArticleCommandTest extends TestCase
{
    public function testItRemovesArticleFromDatabase(): void{
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $articleRepository = new ArticleRepository($connectionStub);

        $deleteArticleCommandHandler = new DeleteArticleCommandHandler($articleRepository, $connectionStub);

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $command = new DeleteEntityCommand(6);

        $deleteArticleCommandHandler->handle($command);
    }
}
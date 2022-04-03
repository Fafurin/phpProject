<?php

namespace Test\Commands;

use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use PHPUnit\Framework\TestCase;

class CreateCommentCommandTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $createCommentCommandHandler = new CreateCommentCommandHandler($connectionStub);

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
}
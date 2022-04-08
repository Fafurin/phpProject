<?php

namespace Test\Commands;

use App\Commands\UpdateCommentCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use PHPUnit\Framework\TestCase;

class UpdateCommentCommandTest extends TestCase
{
    public function testItChangesCommentInDatabase(): void
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $commentRepository = new CommentRepository($connectionStub);

        $updateCommentCommandHandler = new UpdateCommentCommandHandler($commentRepository, $connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $command = new UpdateEntityCommand(
            new Comment(
                3,
                404,
                404,
                'Test text!'
            ), 7
        );
        $updateCommentCommandHandler->handle($command);
    }
}
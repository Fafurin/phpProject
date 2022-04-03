<?php

namespace Test\Commands;

use App\Commands\DeleteCommentCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use PHPUnit\Framework\TestCase;

class DeleteCommentCommandTest extends TestCase
{
    public function testItRemovesCommentFromDatabase(): void{
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $commentRepository = new CommentRepository($connectionStub);

        $deleteCommentCommandHandler = new DeleteCommentCommandHandler($commentRepository, $connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $command = new DeleteEntityCommand(56);

        $deleteCommentCommandHandler->handle($command);
    }
}
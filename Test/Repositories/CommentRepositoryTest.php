<?php

namespace Test\Repositories;

use App\config\SqLiteConfig;
use App\Connections\SqLiteConnector;
use App\Drivers\PdoConnectionDriver;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class CommentRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound()
    {
        $connectionStub = $this->createStub(SqLiteConnector::class);
        $connectionStub->method('getConnection')
            ->willReturn(new PdoConnectionDriver(SqLiteConfig::DSN));

        $this->assertInstanceOf(SqLiteConnector::class, $connectionStub);

        $commentRepository = new CommentRepository($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Cannot find comment");

        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);

        $this->assertInstanceOf(PDOStatement::class, $statementStub);
        $commentRepository->getComment($statementStub);

    }


}
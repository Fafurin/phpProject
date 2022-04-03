<?php

namespace Test\Repositories;

use App\Connections\SqLiteConnector;
use App\Repositories\ArticleRepository;
use App\Exceptions\ArticleNotFoundException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class ArticleRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenArticleNotFound()
    {
        $sqLiteConnectorStub = $this->createStub(SqLiteConnector::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $articleRepository = new ArticleRepository($sqLiteConnectorStub);

        $this->expectException(ArticleNotFoundException::class);
        $this->expectExceptionMessage("Cannot find article");

        $articleRepository->getArticle($statementStub);
    }
 }
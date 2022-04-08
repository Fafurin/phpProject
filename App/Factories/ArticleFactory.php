<?php

namespace App\Factories;

use App\Decorators\ArticleDecorator;
use App\Entities\Article\Article;
use App\Entities\Article\ArticleInterface;

class ArticleFactory implements ArticleFactoryInterface
{

    public function create(ArticleDecorator $articleDecorator): ArticleInterface
    {
        return new Article(
            $articleDecorator->authorId,
            $articleDecorator->title,
            $articleDecorator->text,
        );
    }
}
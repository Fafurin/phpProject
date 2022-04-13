<?php

namespace App\Factories;

use App\Decorators\ArticleDecorator;
use App\Entities\Article\Article;
use App\Entities\Article\ArticleInterface;
use App\Entities\User\UserInterface;

class ArticleFactory implements ArticleFactoryInterface
{

    public function create(ArticleDecorator $articleDecorator, ?UserInterface $user = null): ArticleInterface
    {
        return new Article(
            $user,
            $articleDecorator->title,
            $articleDecorator->text,
        );
    }
}
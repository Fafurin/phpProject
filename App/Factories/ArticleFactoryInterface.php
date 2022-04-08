<?php

namespace App\Factories;

use App\Decorators\ArticleDecorator;
use App\Entities\Article\ArticleInterface;

interface ArticleFactoryInterface extends FactoryInterface
{
    public function create(ArticleDecorator $articleDecorator): ArticleInterface;
}
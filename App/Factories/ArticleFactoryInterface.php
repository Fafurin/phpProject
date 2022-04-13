<?php

namespace App\Factories;

use App\Decorators\ArticleDecorator;
use App\Decorators\UserDecorator;
use App\Entities\Article\ArticleInterface;
use App\Entities\User\User;
use App\Entities\User\UserInterface;

interface ArticleFactoryInterface extends FactoryInterface
{
    public function create(ArticleDecorator $articleDecorator, UserInterface $user): ArticleInterface;
}
<?php

namespace App\Factories;

use App\Decorators\CommentDecorator;
use App\Entities\Comment\CommentInterface;
use App\Entities\User\UserInterface;

interface CommentFactoryInterface extends FactoryInterface
{
    public function create(CommentDecorator $commentDecorator, UserInterface $user): CommentInterface;
}
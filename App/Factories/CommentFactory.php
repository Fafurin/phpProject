<?php

namespace App\Factories;

use App\Decorators\CommentDecorator;
use App\Entities\Comment\Comment;
use App\Entities\Comment\CommentInterface;
use App\Entities\User\UserInterface;

class CommentFactory implements CommentFactoryInterface
{

    public function create(CommentDecorator $commentDecorator, ?UserInterface $user = null): CommentInterface
    {
        return new Comment(
            $user,
            $commentDecorator->articleId,
            $commentDecorator->text,
        );
    }
}
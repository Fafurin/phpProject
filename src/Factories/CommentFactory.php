<?php

namespace App\Factories;

use App\Decorators\CommentDecorator;
use App\Entities\Comment\Comment;
use App\Entities\Comment\CommentInterface;

class CommentFactory implements CommentFactoryInterface
{

    public function create(CommentDecorator $commentDecorator): CommentInterface
    {
        return new Comment(
            $commentDecorator->authorId,
            $commentDecorator->articleId,
            $commentDecorator->text,
        );
    }
}
<?php

namespace App\Entities\Comment;

use App\Entities\EntityInterface;
use App\Entities\User\User;

interface CommentInterface extends EntityInterface
{
    public function getArticleId(): int;
    public function getText(): string;
    public function getAuthor(): User;
}
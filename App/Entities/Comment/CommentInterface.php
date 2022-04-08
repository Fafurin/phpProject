<?php

namespace App\Entities\Comment;

use App\Entities\EntityInterface;

interface CommentInterface extends EntityInterface
{
    public function getId(): ?int;
    public function getAuthorId(): int;
    public function getArticleId(): int;
    public function getText(): string;
}
<?php

namespace App\Entities\Article;

use App\Entities\EntityInterface;

interface ArticleInterface extends EntityInterface
{
    public function getAuthorId(): int;
    public function getTitle(): string;
    public function getText(): string;
}

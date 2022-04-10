<?php

namespace App\Entities\Article;

use App\Entities\EntityInterface;
use App\Entities\User\User;

interface ArticleInterface extends EntityInterface
{
    public function getTitle(): string;
    public function getText(): string;
    public function getAuthor(): User;
}

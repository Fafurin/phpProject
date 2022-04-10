<?php

namespace App\Repositories;

use App\Entities\Article\Article;

interface ArticleRepositoryInterface
{
    public function getArticleByTitle(string $title): Article;
}
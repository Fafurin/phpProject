<?php

namespace App\Repositories;

use App\Entities\Article\ArticleInterface;

interface ArticleRepositoryInterface extends EntityRepositoryInterface
{
    public function findById(int $id): ArticleInterface;
}
<?php

namespace App\Factories;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Entities\User\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\EntityRepositoryInterface;
use App\Repositories\UserRepository;

class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        private ?ConnectionInterface $connection = null){}

    public function create(string $entityType): EntityRepositoryInterface
    {
        return match($entityType){
            User::class => new UserRepository($this->connection),
            Article::class => new ArticleRepository($this->connection),
            Comment::class => new CommentRepository($this->connection),
        };
    }
}
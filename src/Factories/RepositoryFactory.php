<?php

namespace App\Factories;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
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
    protected ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqLiteConnector();
    }

    public function create(string $entityType): EntityRepositoryInterface
    {
        return match($entityType){
            User::class => new UserRepository($this->connector),
            Article::class => new ArticleRepository($this->connector),
            Comment::class => new CommentRepository($this->connector),
        };
    }
}
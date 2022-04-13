<?php

namespace App\Factories\Commands;

use App\Commands\CommandHandlerInterface;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateUserCommandHandler;
use App\Commands\DeleteArticleCommandHandler;
use App\Commands\DeleteCommentCommandHandler;
use App\Commands\DeleteUserCommandHandler;
use App\Commands\UpdateArticleCommandHandler;
use App\Commands\UpdateCommentCommandHandler;
use App\Commands\UpdateUserCommandHandler;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Entities\User\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CommentRepository;
use App\Repositories\UserRepository;

class CommandHandlerFactory implements CommandHandlerFactoryInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private ArticleRepository $articleRepository,
        private CommentRepository $commentRepository,
        private ConnectionInterface $connection){}

    public function create(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New CreateUserCommandHandler($this->connection),
            Article::class => New CreateArticleCommandHandler($this->connection),
            Comment::class => New CreateCommentCommandHandler($this->connection)
        };
    }

    public function delete(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New DeleteUserCommandHandler($this->connection),
            Article::class => New DeleteArticleCommandHandler($this->connection),
            Comment::class => New DeleteCommentCommandHandler($this->connection)
        };
    }

    public function update(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New UpdateUserCommandHandler($this->connection),
            Article::class => New UpdateArticleCommandHandler($this->connection),
            Comment::class => New UpdateCommentCommandHandler($this->connection)
        };
    }
}
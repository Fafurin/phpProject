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
use App\Connections\SqLiteConnector;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Entities\User\User;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;

class CommandHandlerFactory implements CommandHandlerFactoryInterface
{
    public function create(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New CreateUserCommandHandler(new UserRepository(new SqLiteConnector())),
            Article::class => New CreateArticleCommandHandler(new ArticleRepository(new SqLiteConnector())),
            Comment::class => New CreateCommentCommandHandler()
        };
    }

    public function delete(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New DeleteUserCommandHandler(),
            Article::class => New DeleteArticleCommandHandler(),
            Comment::class => New DeleteCommentCommandHandler()
        };
    }

    public function update(string $entityType): CommandHandlerInterface
    {
        return match ($entityType) {
            User::class => New UpdateUserCommandHandler(),
            Article::class => New UpdateArticleCommandHandler(),
            Comment::class => New UpdateCommentCommandHandler()
        };
    }
}
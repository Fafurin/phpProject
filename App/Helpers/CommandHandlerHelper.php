<?php

namespace App\Helpers;

use App\Commands\CommandHandlerInterface;
use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateUserCommandHandler;
use App\Container\DIContainer;
use App\Entities\User\User;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Entities\EntityInterface;
use App\Exceptions\NotFoundException;

class CommandHandlerHelper
{
    /**
     * @throws NotFoundException
     */
    public function getCommandHandlerByEntity(
        EntityInterface $entity,
        DIContainer $container
    ): object
    {
        /**
         * @var CommandHandlerInterface $commandHandler
         */
        return  match ($entity::class)
        {
            User::class => $container->get(CreateUserCommandHandler::class),
            Article::class => $container->get(CreateArticleCommandHandler::class),
            Comment::class => $container->get(CreateCommentCommandHandler::class)
        };
    }
}
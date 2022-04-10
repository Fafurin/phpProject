<?php

namespace App\Http\Actions;

use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Container\DIContainer;
use App\Entities\Comment\Comment;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\Auth\AuthenticationInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private AuthenticationInterface $authentication
    ){}

    public function handle(Request $request): Response
    {
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        try {
            $comment = new Comment(
                $this->authentication->getUser($request),
                $request->jsonBodyField('articleId'),
                $request->jsonBodyField('text'),
            );
            $this->createCommentCommandHandler->handle(new CreateEntityCommand($comment));
        } catch (HttpException|ArticleNotFoundException|UserNotFoundException $e) {
            $logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $data = [
            'authorId' => $comment->getAuthor()->getId(),
            'title' => $comment->getArticleId(),
            'text' => $comment->getText()
        ];

        $logger->info('Created new comment', $data);

        return new SuccessfulResponse($data);

    }
}
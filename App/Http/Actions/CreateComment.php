<?php

namespace App\Http\Actions;

use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Entities\Comment\Comment;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $comment = new Comment(
                $request->jsonBodyField('id'),
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('articleId'),
                $request->jsonBodyField('text'),
            );
            $this->createCommentCommandHandler->handle(new CreateEntityCommand($comment));
        } catch (HttpException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'text' => $comment->getText()
        ]);
    }
}
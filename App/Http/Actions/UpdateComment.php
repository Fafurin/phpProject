<?php

namespace App\Http\Actions;

use App\Commands\UpdateCommentCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class UpdateComment implements ActionInterface
{
    public function __construct(
        private UpdateCommentCommandHandler $updateCommentCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $comment = new Comment(
                $id,
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('text'),
            );
            $this->updateCommentCommandHandler->handle(new UpdateEntityCommand($comment, $id));
        } catch (HttpException|CommentNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'text' => $comment->getText()
        ]);
    }
}
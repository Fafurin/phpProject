<?php

namespace App\Http\Actions;

use App\Commands\DeleteCommentCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeleteComment implements ActionInterface
{
    public function __construct(
        private DeleteCommentCommandHandler $deleteCommentCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $this->deleteCommentCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException|CommentNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Removed comment with id ' => $id,
        ]);
    }
}

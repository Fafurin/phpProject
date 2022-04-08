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

class DeleteComment implements ActionInterface
{
    public function __construct(
        private ?DeleteCommentCommandHandler $deleteCommentCommandHandler = null)
    {
        $this->deleteCommentCommandHandler = $this->deleteCommentCommandHandler ?? new DeleteCommentCommandHandler();
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $this->deleteCommentCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException|CommentNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'Removed comment with id ' => $id,
        ]);
    }
}

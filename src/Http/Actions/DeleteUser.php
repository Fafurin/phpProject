<?php

namespace App\Http\Actions;

use App\Commands\DeleteEntityCommand;
use App\Commands\DeleteUserCommandHandler;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private ?DeleteUserCommandHandler $deleteUserCommandHandler = null
    )
    {
        $this->deleteUserCommandHandler = $this->deleteUserCommandHandler ?? new DeleteUserCommandHandler();
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $this->deleteUserCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException|UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'Удален пользователь с id' => $id,
        ]);
    }
}
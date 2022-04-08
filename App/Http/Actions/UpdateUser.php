<?php

namespace App\Http\Actions;

use App\Commands\UpdateEntityCommand;
use App\Commands\UpdateUserCommandHandler;
use App\Entities\User\User;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;

class UpdateUser implements ActionInterface
{

    public function __construct(
        private ?UpdateUserCommandHandler $updateUserCommandHandler = null
    )
    {
        $this->updateUserCommandHandler = $this->updateUserCommandHandler ?? new UpdateUserCommandHandler();
    }

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $user = new User(
                $request->jsonBodyField('firstName'),
                $request->jsonBodyField('lastName'),
                $request->jsonBodyField('email'),
            );
            $this->updateUserCommandHandler->handle(new UpdateEntityCommand($user, $id));
        } catch (HttpException|UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'name' => $user->getFirstName() . ' ' . $user->getLastName(),
            'email' => $user->getEmail()
        ]);
    }
}
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
use Psr\Log\LoggerInterface;

class DeleteUser implements ActionInterface
{
    public function __construct(
        private DeleteUserCommandHandler $deleteUserCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $this->deleteUserCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException|UserNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Deleted user with id ' => $id,
        ]);
    }
}
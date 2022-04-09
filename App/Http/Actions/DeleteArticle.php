<?php

namespace App\Http\Actions;

use App\Commands\DeleteArticleCommandHandler;
use App\Commands\DeleteEntityCommand;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeleteArticle implements ActionInterface
{
    public function __construct(
        private DeleteArticleCommandHandler $deleteArticleCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $this->deleteArticleCommandHandler->handle(new DeleteEntityCommand($id));
        } catch (HttpException|ArticleNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Deleted article with id ' => $id,
        ]);
    }
}
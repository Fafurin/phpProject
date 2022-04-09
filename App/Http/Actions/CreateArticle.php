<?php

namespace App\Http\Actions;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Entities\Article\Article;
use App\Exceptions\ArticleTitleExistException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateArticle implements ActionInterface
{

    public function __construct(
        private CreateArticleCommandHandler $createArticleCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $article = new Article(
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
            $this->createArticleCommandHandler->handle(new CreateEntityCommand($article));
        } catch (HttpException|ArticleTitleExistException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $article->getTitle()
        ]);
    }
}
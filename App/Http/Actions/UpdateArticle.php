<?php

namespace App\Http\Actions;

use App\Commands\UpdateArticleCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class UpdateArticle implements ActionInterface
{

    public function __construct(
        private UpdateArticleCommandHandler $updateArticleCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $id = $request->jsonBodyField('id');
            $article = new Article(
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
            $this->updateArticleCommandHandler->handle(new UpdateEntityCommand($article, $id));
        } catch (HttpException|ArticleNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $article->getTitle(),
            'text' => $article->getText()
        ]);
    }
}
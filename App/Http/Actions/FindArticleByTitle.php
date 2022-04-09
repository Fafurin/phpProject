<?php

namespace App\Http\Actions;

use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;

class FindArticleByTitle implements ActionInterface
{

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $title = $request->query('title');
        } catch (HttpException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        try {
            $article = $this->articleRepository->getArticleByTitle($title);
        } catch (ArticleNotFoundException $e) {
            $this->logger->error("Article not found");
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'title' => $article->getTitle(),
            'text' => $article->getText()
        ]);
    }
}
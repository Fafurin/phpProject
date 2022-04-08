<?php

namespace App\Http\Actions;

use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;

class FindArticleByTitle implements ActionInterface
{

    public function __construct(private ?ArticleRepositoryInterface $articleRepository = null)
    {
        $this->articleRepository = $this->articleRepository ?? new ArticleRepository();
    }

    public function handle(Request $request): Response
    {
        try {
            $title = $request->query('title');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $article = $this->articleRepository->getArticleByTitle($title);

        } catch (ArticleNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'title' => $article->getTitle(),
            'text' => $article->getText()
        ]);
    }
}
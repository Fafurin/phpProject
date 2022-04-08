<?php

namespace App\Http\Actions;

use App\Commands\UpdateArticleCommandHandler;
use App\Commands\UpdateEntityCommand;
use App\Commands\UpdateUserCommandHandler;
use App\Entities\Article\Article;
use App\Entities\User\User;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;

class UpdateArticle implements ActionInterface
{

    public function __construct(
        private ?UpdateArticleCommandHandler $updateArticleCommandHandler = null
    )
    {
        $this->updateArticleCommandHandler = $this->updateArticleCommandHandler ?? new UpdateArticleCommandHandler();
    }

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
        } catch (HttpException|ArticleNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'title' => $article->getTitle(),
            'text' => $article->getText()
        ]);
    }
}
<?php

namespace App\Http\Actions;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Exceptions\ArticleTitleExistException;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\Auth\AuthenticationInterface;
use App\Http\Auth\IdentificationInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateArticle implements ActionInterface
{
    public function __construct(
        private CreateArticleCommandHandler $createArticleCommandHandler,
        private AuthenticationInterface $authentication
    ){}

    public function handle(Request $request): Response
    {
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);
        try {
            $article = new Article(
                $this->authentication->getUser($request),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
            $this->createArticleCommandHandler->handle(new CreateEntityCommand($article));
        } catch (HttpException|ArticleTitleExistException|UserNotFoundException $e) {
            $logger->error($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $data = [
            'authorId' => $article->getAuthor()->getId(),
            'title' => $article->getTitle(),
            'text' => $article->getText()
        ];

        $logger->info('Created new article', $data);

        return new SuccessfulResponse($data);
    }
}
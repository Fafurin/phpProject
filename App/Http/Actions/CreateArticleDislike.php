<?php

namespace App\Http\Actions;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Container\DIContainer;
use App\Entities\Article\Article;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\DislikeNotFoundException;
use App\Exceptions\HttpException;
use App\Exceptions\UserNotFoundException;
use App\Http\Auth\AuthenticationInterface;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateArticleDislike implements ActionInterface
{
    public function __construct(
        private CreateEvaluationCommandHandler $createEvaluationCommandHandler,
        private AuthenticationInterface $authentication
    ){}

    public function handle(Request $request): Response
    {
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        try {
            $evaluation = new Evaluation(
                $this->authentication->getUser($request),
                $request->jsonBodyField('entityId'),
                Evaluation::DISLIKE_TYPE,
                Article::ENTITY_TYPE
            );
            $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
        } catch (HttpException|DislikeNotFoundException|UserNotFoundException $e) {
            $logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $data = [
            'authorId' => $evaluation->getAuthor()->getId(),
            'articleId' => $evaluation->getEntityId()
        ];

        $logger->info('The article was disliked', $data);

        return new SuccessfulResponse($data);
    }
}

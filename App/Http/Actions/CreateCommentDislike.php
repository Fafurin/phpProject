<?php

namespace App\Http\Actions;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Entities\Comment\Comment;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\DislikeNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateCommentDislike implements ActionInterface
{

    public function __construct(
        private CreateEvaluationCommandHandler $createEvaluationCommandHandler,
        private LoggerInterface $logger
    ){}

    public function handle(Request $request): Response
    {
        try {
            $evaluation = new Evaluation(
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('entityId'),
                Evaluation::DISLIKE_TYPE,
                Comment::ENTITY_TYPE
            );
            $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
        } catch (HttpException|DislikeNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Disliked the comment with id ' => $evaluation->getEntityId()
        ]);
    }
}


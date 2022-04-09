<?php

namespace App\Http\Actions;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Entities\Comment\Comment;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\HttpException;
use App\Exceptions\LikeNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateCommentLike implements ActionInterface
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
                Evaluation::LIKE_TYPE,
                Comment::ENTITY_TYPE
            );
            $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
        } catch (HttpException|LikeNotFoundException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'Liked the comment with id ' => $evaluation->getEntityId()
        ]);
    }
}


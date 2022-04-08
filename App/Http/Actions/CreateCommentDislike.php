<?php

namespace App\Http\Actions;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\DislikeNotFoundException;
use App\Exceptions\HttpException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;

class CreateCommentDislike implements ActionInterface
{

    public function __construct(?CreateEvaluationCommandHandler $createEvaluationCommandHandler = null)
    {
        $this->createEvaluationCommandHandler = $createEvaluationCommandHandler;
    }

    public function handle(Request $request): Response
    {
        try {
            $evaluation = new Evaluation(
                $request->jsonBodyField('authorId'),
                $request->jsonBodyField('entityId'),
                2,
                2
            );
            $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
        } catch (HttpException|DislikeNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'Disliked the comment with id ' => $evaluation->getEntityId()
        ]);
    }
}


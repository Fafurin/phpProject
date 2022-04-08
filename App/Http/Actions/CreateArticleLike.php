<?php

namespace App\Http\Actions;

use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\HttpException;
use App\Exceptions\LikeNotFoundException;
use App\Http\ErrorResponse;
use App\Http\Request;
use App\Http\Response;
use App\Http\SuccessfulResponse;

class CreateArticleLike implements ActionInterface
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
                1,
                1
            );
            $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
        } catch (HttpException|LikeNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        return new SuccessfulResponse([
            'Liked the article with id ' => $evaluation->getEntityId()
        ]);
    }
}

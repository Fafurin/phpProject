<?php

namespace App\Entities\Evaluation;

use App\Entities\User\User;
use App\Traits\Author;
use App\Traits\Id;

class Evaluation implements EvaluationInterface
{
    use Id;
    use Author;

    public const TABLE_NAME = 'evaluations';
    public const LIKE_TYPE = '1';
    public const DISLIKE_TYPE = '2';

    private int $entityId;
    private int $evaluationType;
    private int $entityType;

    public function __construct(
        User $author,
        int $entityId,
        int $evaluationType,
        int $entityType
    ){
        $this->author =$author;
        $this->entityId = $entityId;
        $this->evaluationType = $evaluationType;
        $this->entityType = $entityType;
    }

    public function getEntityId(): int{
        return $this->entityId;
    }

    public function getEvaluationType(): int{
        return $this->evaluationType;
    }

    public function getEntityType(): int{
        return $this->entityType;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }
}
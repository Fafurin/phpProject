<?php

namespace App\Entities\Evaluation;

class Evaluation implements EvaluationInterface
{
    public const TABLE_NAME = 'evaluations';

    private ?int $id = null;

    public function __construct(
        private int $authorId,
        private int $entityId,
        private ?int $evaluationType = null,
        private ?int $entityType = null
    ){}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): int{
        return $this->authorId;
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
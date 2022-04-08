<?php

namespace App\Entities\Evaluation;

use App\Entities\EntityInterface;

interface EvaluationInterface extends EntityInterface
{
    public function getId(): ?int;
    public function getTableName(): string;

}
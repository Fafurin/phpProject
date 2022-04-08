<?php

namespace App\Managers;

use App\Entities\EntityInterface;

interface EntityManagerInterface
{
    public function create(EntityInterface $entity): void;
    public function delete(string $entityType, int $id): void;
    public function update(EntityInterface $entity, int $id): void;
}
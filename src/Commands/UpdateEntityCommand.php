<?php

namespace App\Commands;

use App\Entities\EntityInterface;

class UpdateEntityCommand implements CommandInterface
{
    public function __construct(private EntityInterface $entity, private int $id){}

    public function getId(): int{
        return $this->id;
    }

    public function getEntity(): EntityInterface{
            return $this->entity;
    }
}
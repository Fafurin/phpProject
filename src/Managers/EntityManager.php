<?php

namespace App\Managers;

use App\Commands\CreateEntityCommand;
use App\Commands\DeleteEntityCommand;
use App\Commands\UpdateEntityCommand;
use App\Entities\EntityInterface;
use App\Factories\Commands\CommandHandlerFactory;
use App\Factories\Commands\CommandHandlerFactoryInterface;

class EntityManager implements EntityManagerInterface
{
    private CommandHandlerFactoryInterface $commandHandlerFactory;

    public function __construct(CommandHandlerFactoryInterface $commandHandlerFactory = null)
    {
        $this->commandHandlerFactory = $commandHandlerFactory ?? new CommandHandlerFactory();
    }

    public function create(EntityInterface $entity): void
    {
        $commandHandler = $this->commandHandlerFactory->create($entity::class);
        $commandHandler->handle(new CreateEntityCommand($entity));
    }

    public function delete(string $entityType, int $id): void
    {
        $commandHandler = $this->commandHandlerFactory->delete($entityType);
        $commandHandler->handle(new DeleteEntityCommand($id));
    }

    public function update(EntityInterface $entity, int $id): void
    {
        $commandHandler = $this->commandHandlerFactory->update($entity::class);
        $commandHandler->handle(new UpdateEntityCommand($entity, $id));
    }
}
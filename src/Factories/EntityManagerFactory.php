<?php

namespace App\Factories;

use App\Entities\EntityInterface;
use App\Managers\EntityManager;
use App\Managers\EntityManagerInterface;
use App\Repositories\EntityRepositoryInterface;

class EntityManagerFactory implements EntityManagerFactoryInterface
{

    private ?EntityFactoryInterface $entityFactory;
    private ?RepositoryFactoryInterface $repositoryFactory;

    public function __construct(
        EntityFactoryInterface $entityFactory = null,
        RepositoryFactoryInterface $repositoryFactory = null
    )
    {
        $this->entityFactory = $entityFactory ?? new EntityFactory();
        $this->repositoryFactory = $repositoryFactory ?? new RepositoryFactory();
    }

    public function createEntityByInputArgs(array $arguments): EntityInterface{
        return $this->createEntity($arguments[1], array_slice($arguments, 2));
    }

    public function createEntity(string $entityType, array $arguments): EntityInterface
    {
        return $this->entityFactory->create($entityType, $arguments);
    }

    public function getRepositoryByInputArguments(array $arguments): EntityRepositoryInterface
    {
        return $this->getRepository($this->createEntityByInputArgs($arguments)::class);
    }

    public function getRepository(string $entityType): EntityRepositoryInterface
    {
        return $this->repositoryFactory->create($entityType);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return new EntityManager();
    }
}
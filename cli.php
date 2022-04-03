<?php

use App\Entities\User\User;
use App\Enums\Argument;
use App\Exceptions\NotFoundException;
use App\Factories\EntityManagerFactory;
use App\Factories\EntityManagerFactoryInterface;
use App\Repositories\UserRepositoryInterface;

try {
    if(count($argv) < 2)
    {
        throw new NotFoundException('404');
    }

    if(!in_array($argv[1], Argument::getArgumentValues()))
    {
        throw new NotFoundException('404');
    }
    /**
     * @var EntityManagerFactoryInterface $entityMangerFactory
     */
    $entityMangerFactory = new EntityManagerFactory();
    $entity = $entityMangerFactory->createEntityByInputArgs($argv);
    if($entity instanceof User)
    {
        /**
         * @var UserRepositoryInterface $repository
         */
        $repository = $entityMangerFactory->getRepository($entity::class);

        if(!$repository->getUserByEmail($entity->getEmail()))
        {
            $entityMangerFactory->getEntityManager()->create($entity);
        }
    }
}catch (Exception $exception)
{
    echo $exception->getMessage().PHP_EOL;
    http_response_code(404);
}

$object = new class
{
    public function sayHello(string $name)
    {
        echo "Hello, $name";
    }
};
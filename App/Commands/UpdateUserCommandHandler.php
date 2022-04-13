<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ConnectionInterface $connection,
        private ?UserRepositoryInterface $userRepository = null){}

    /**
     * @throws UserNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Update article command started');

        /**
         * @var CreateEntityCommand $command
         */
        $user = $command->getEntity();

        /**
         * @var User $user
         */

        $id = $user->getId();

        if($this->isUserExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id,
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail()
            ]);
        }else{
            $logger->warning("The user with this id: $id not found");
            throw new UserNotFoundException();
        }
    }

    public function isUserExists(int $id): bool{
        try{
            $this->userRepository->findById($id);
        } catch (UserNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "UPDATE users 
                SET first_name = :firstName, last_name = :lastName, email = :email 
                WHERE id = :id";
    }
}
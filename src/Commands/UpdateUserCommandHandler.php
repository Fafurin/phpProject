<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $statement;

    public function __construct(
        private ?UserRepositoryInterface $userRepository = null,
        private ?ConnectorInterface $connector = null)
    {
        $this->userRepository = $this->userRepository ?? new UserRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->statement =$this->connector->getConnection()->prepare($this->getSql());
    }

    public function handle(CommandInterface $command): void{
        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $id = $command->getId();

        if($this->isUserExists($id)){
            $this->statement->execute([
                ':id' => (string)$id,
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $user->getEmail()
            ]);
        }else{
            throw new UserNotFoundException();
        }

    }

    public function isUserExists(int $id): bool{
        try{
            $this->userRepository->getUserById($id);
        } catch (UserNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "UPDATE " . User::TABLE_NAME . " 
                SET first_name = :firstName, last_name = :lastName, email = :email 
                WHERE id = :id";
    }
}
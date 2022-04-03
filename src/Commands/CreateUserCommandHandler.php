<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
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

    public function handle(CommandInterface $command): void
    {
        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $email = $user->getEmail();

        if(!$this->isUserExists($email)){
            $this->statement->execute([
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $email
            ]);
        }else{
            throw new UserEmailExistException();
        }
    }

    public function isUserExists(string $email): bool{
        try{
            $this->userRepository->getUserByEmail($email);
        } catch (UserNotFoundException){
            return false;
        }
        return true;
    }

    public function getSql(): string
    {
        return "INSERT INTO " . User::TABLE_NAME ." (first_name, last_name, email) 
                 VALUES (:firstName, :lastName, :email)";
    }
}
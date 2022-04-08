<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
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
        private ConnectionInterface $connection)
    {
        $this->userRepository = $this->userRepository ?? new UserRepository();
        $this->statement = $connection->prepare($this->getSql());
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
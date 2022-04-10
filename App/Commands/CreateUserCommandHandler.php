<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ConnectionInterface $connection,
        private LoggerInterface $logger){}

    /**
     * @throws UserEmailExistException
     */
    public function handle(CommandInterface $command): void
    {

        $this->logger->info('Create user command started');

        /**
         * @var User $user
         */
        $user = $command->getEntity();
        $email = $user->getEmail();

        if(!$this->isUserExists($email)){
            $this->connection->prepare($this->getSql())->execute([
                ':firstName' => $user->getFirstName(),
                ':lastName' => $user->getLastName(),
                ':email' => $email,
                ':password' => $user->setPassword($user->getPassword()),
            ]);

            $this->logger->info("User created with the email: $email");

        }else{
            $this->logger->warning("The user with this email: $email already exists");
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
        return "INSERT INTO " . User::TABLE_NAME ." (first_name, last_name, email, password) 
                 VALUES (:firstName, :lastName, :email, :password)";
    }
}
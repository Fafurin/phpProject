<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserEmailExistException;
use App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private ?UserRepositoryInterface $userRepository = null){}

    /**
     * @throws UserEmailExistException
     */
    public function handle(CommandInterface $command): User
    {
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Create user command started');

        /**
         * @var CreateEntityCommand $command
         */
        $user = $command->getEntity();

        /**
         * @var User $user
         */
        $email = $user->getEmail();

        $this->connection->prepare($this->getSql())->execute([
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':email' => $email,
            ':password' => $user->setPassword($user->getPassword()),
        ]);

        $logger->info("User created with the email: $email");

        return $this->userRepository->findById($this->connection->lastInsertId());
    }

    public function getSql(): string
    {
        return "INSERT INTO users (first_name, last_name, email, password) 
                VALUES (:firstName, :lastName, :email, :password)
                ON CONFLICT (email) DO UPDATE SET
                first_name = :firstName,
                last_name = :lastName";
    }
}
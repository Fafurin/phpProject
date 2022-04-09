<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ?UserRepositoryInterface $userRepository = null,
        private ConnectionInterface $connection,
        private LoggerInterface $logger)
    {}

    /**
     * @throws UserNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $this->logger->info('Delete user command started');

        /**
         * @var User $user
         */

        $id = $command->getId();

        if($this->isUserExists($id)) {
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id
            ]);
        }else{
            $this->logger->warning("The user with this id: $id not found");
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
        return "DELETE FROM " . User::TABLE_NAME . " WHERE id = :id";
    }
}
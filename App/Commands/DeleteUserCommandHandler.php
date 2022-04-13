<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ConnectionInterface $connection,
        private ?UserRepositoryInterface $userRepository = null)
    {}

    /**
     * @throws UserNotFoundException
     */
    public function handle(CommandInterface $command): void{
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Delete user command started');

        /**
         * @var User $user
         */

        $id = $command->getId();

        if($this->isUserExists($id)) {
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id
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
        return "DELETE FROM users WHERE id = :id";
    }
}
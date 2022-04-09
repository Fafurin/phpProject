<?php

namespace App\Repositories;

use App\Drivers\ConnectionInterface;
use App\Entities\User\User;
use App\Exceptions\UserNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;


class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    public function __construct(
        ConnectionInterface $connection,
        private LoggerInterface $logger)
    {
        parent::__construct($connection);
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(int $id): User
    {
        $statement = $this->connection->prepare('SELECT * FROM ' . USER::TABLE_NAME . ' WHERE id = :id');
        $statement->execute([
            ':id' => (string)$id,
        ]);
        return $this->getUser($statement);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUser(PDOStatement $statement): User
    {
        $userData = $statement->fetch(PDO::FETCH_OBJ);

        if (!$userData) {
            $this->logger->error("User not found");
            throw new UserNotFoundException("User not found");
        }

        return new User(
            $userData->first_name,
            $userData->last_name,
            $userData->email
        );
    }

    public function getUserByEmail(string $email): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . USER::TABLE_NAME . ' WHERE email = :email'
        );

        $statement->execute([
           ':email' => $email,
        ]);
        return $this->getUser($statement);
    }

    public function getUserById(int $id): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . USER::TABLE_NAME . ' WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id,
        ]);
        return $this->getUser($statement);
    }
}
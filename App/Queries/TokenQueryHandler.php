<?php

namespace App\Queries;

use App\Drivers\ConnectionInterface;
use App\Entities\Token\AuthToken;
use App\Exceptions\AuthTokenRepositoryException;
use App\Repositories\UserRepositoryInterface;
use DateTimeImmutable;
use Exception;
use PDO;
use PDOException;

class TokenQueryHandler implements TokenQueryHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private UserRepositoryInterface $userRepository
    ){}

    /**
     * @return AuthToken[]
     * @throws AuthTokenRepositoryException
     */
    public function handle(): array
    {
        try {
            $statement = $this->connection->prepare($this->getSQL());
            $statement->execute();

            $tokensData = $statement->fetchAll(PDO::FETCH_OBJ);

            try {
                $tokens = [];

                foreach ($tokensData as $tokenData)
                {
                    $tokens[$tokenData->token] = new AuthToken(
                        $tokenData->token,
                        $this->userRepository->findById($tokenData->user_id),
                        new DateTimeImmutable($tokenData->expires_on)
                    );
                }

                return $tokens;

            } catch (Exception $e) {
                throw new AuthTokenRepositoryException(
                    $e->getMessage(), $e->getCode(), $e
                );
            }

        }catch (PDOException $e)
        {
            throw new AuthTokenRepositoryException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getSQL(): string
    {
        return "SELECT * FROM Token";
    }
}
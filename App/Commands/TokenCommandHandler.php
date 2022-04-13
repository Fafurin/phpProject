<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Token\AuthToken;
use App\Exceptions\AuthTokenRepositoryException;
use DateTimeInterface;
use PDOException;

class TokenCommandHandler implements TokenCommandHandlerInterface
{
    public function __construct(private ConnectionInterface $connection){}

    public function handle(AuthToken $authToken): void
    {
        try {
            $this->connection
                ->prepare($this->getSQL())
                ->execute(
                    [
                        ':token' => $authToken->getToken(),
                        ':user_id' => $authToken->getUser()->getId(),
                        ':expires_on' => $authToken->getExpiresOn()
                            ->format(DateTimeInterface::ATOM),
                    ]
                );
        } catch (PDOException $e) {
            throw new AuthTokenRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }

    public function getSQL(): string
    {
        return "INSERT INTO tokens (
                token,
               user_id,
               expires_on
           ) VALUES (
               :token,
               :user_id,
               :expires_on
           )
           ON CONFLICT (token) DO UPDATE SET
               expires_on = :expires_on";
    }
}
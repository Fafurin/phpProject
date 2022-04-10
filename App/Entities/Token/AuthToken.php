<?php

namespace App\Entities\Token;

use DateTimeImmutable;

class AuthToken
{

    public function __construct(
        private string $token,
        private int $userId,
        private DateTimeImmutable $expiresOn
    ){}

    public function token(): string{
        return $this->token;
    }

    public function getUserId(): int{
        return $this->userId;
    }

    public function expiresOn(): DateTimeImmutable{
        return $this->expiresOn;
    }
}
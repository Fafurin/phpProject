<?php

namespace App\Repositories;

use App\Entities\Token\AuthToken;

class AuthTokenRepository implements AuthTokenRepositoryInterface
{

    public function save(AuthToken $authToken): void
    {
        // TODO: Implement save() method.
    }

    public function get(string $token): AuthToken
    {
        // TODO: Implement get() method.
    }
}
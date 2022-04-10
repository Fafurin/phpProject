<?php

namespace App\Repositories;

use App\Entities\Token\AuthToken;

interface AuthTokenRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
}
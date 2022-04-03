<?php

namespace App\Factories;

use App\Decorators\UserDecorator;
use App\Entities\User\User;
use App\Entities\User\UserInterface;

class UserFactory implements UserFactoryInterface
{
    public function create(UserDecorator $userDecorator): UserInterface
    {
        return new User(
            $userDecorator->firstName,
            $userDecorator->lastName,
            $userDecorator->email,
        );
    }
}
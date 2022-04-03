<?php

namespace App\Factories;

use App\Decorators\UserDecorator;
use App\Entities\User\UserInterface;

interface UserFactoryInterface extends FactoryInterface
{
    public function create(UserDecorator $userDecorator): UserInterface;
}
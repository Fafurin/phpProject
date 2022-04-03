<?php

namespace App\Decorators;

use App\Enums\User;

class UserDecorator extends Decorator implements DecoratorInterface
{
    public ?string $id;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $email;

    public function __construct(array $arguments)
    {
        parent::__construct($arguments);

        $userFieldData = $this->getFieldData();

        $this->id = $userFieldData->get(User::ID->value) ?? null;
        $this->firstName = $userFieldData->get(User::FIRST_NAME->value) ?? null;
        $this->lastName = $userFieldData->get(User::LAST_NAME->value) ?? null;
        $this->email = $userFieldData->get(User::EMAIL->value) ?? null;
    }

    public function getRequiredFields(): array
    {
        return User::getRequiredFields();
    }
}
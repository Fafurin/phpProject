<?php

namespace App\Entities\User;

class User implements UserInterface
{
    public const TABLE_NAME = 'users';

    private ?int $id = null;

    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email
    ){}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string{
        return $this->firstName;
    }

    public function getLastName(): string{
        return $this->lastName;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }
}
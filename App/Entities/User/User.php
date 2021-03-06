<?php

namespace App\Entities\User;

use App\Traits\Id;

class User implements UserInterface
{
    use Id;

    public const TABLE_NAME = 'users';

    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email,
        private string $password
    ){}

    public function getFirstName(): string{
        return $this->firstName;
    }

    public function getLastName(): string{
        return $this->lastName;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function hash(string $password, string $email): string{
        return hash('sha256', $email.$password);
    }

    public function checkPassword(string $password): bool{
        return $this->password === self::hash($password, $this->getEmail());
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password):string
    {
        $this->password = self::hash($password, $this->getEmail());
        return $this->password;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
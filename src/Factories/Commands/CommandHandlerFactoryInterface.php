<?php

namespace App\Factories\Commands;

use App\Commands\CommandHandlerInterface;

interface CommandHandlerFactoryInterface
{
    public function create(string $entityType): CommandHandlerInterface;
    public function delete(string $entityType): CommandHandlerInterface;
    public function update(string $entityType): CommandHandlerInterface;
}
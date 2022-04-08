<?php

namespace App\Commands;

interface CommandHandlerInterface
{
    public function getSQL(): string;
}
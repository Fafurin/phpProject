<?php

namespace App\Services;

use App\Classes\Argument;

interface ArgumentParserServiceInterface
{
    public function parseRawInput(
        array $rawInput,
        array $scheme = []
    ): Argument;
}
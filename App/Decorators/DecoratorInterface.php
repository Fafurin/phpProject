<?php

namespace App\Decorators;

use App\Classes\Argument;

interface DecoratorInterface
{
    public function getFieldData(): Argument;
    public function getRequiredFields();
}
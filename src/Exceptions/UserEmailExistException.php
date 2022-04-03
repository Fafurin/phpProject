<?php

namespace App\Exceptions;

class UserEmailExistException extends \Exception
{
    protected $message = 'A user with this email already exists';
}
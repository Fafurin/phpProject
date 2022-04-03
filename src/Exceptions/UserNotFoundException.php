<?php

namespace App\Exceptions;

class UserNotFoundException extends \Exception{
    protected $message = 'User not found';
}
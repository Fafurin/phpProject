<?php

namespace App\Exceptions;

class CommentNotFoundException extends \Exception
{
    protected $message = "Cannot find comment";
}
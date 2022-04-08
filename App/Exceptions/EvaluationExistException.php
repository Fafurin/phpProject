<?php

namespace App\Exceptions;

class EvaluationExistException extends \Exception
{
    protected $message = 'The user has already rated this article';

}
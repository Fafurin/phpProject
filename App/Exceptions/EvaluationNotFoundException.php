<?php

namespace App\Exceptions;

class EvaluationNotFoundException extends \Exception
{
    protected $message = 'Evaluation not found';

}
<?php

namespace App\Exceptions;

class ArticleTitleExistException extends \Exception
{
    protected $message = 'An article with this title already exists';

}
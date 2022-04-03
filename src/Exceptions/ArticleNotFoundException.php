<?php

namespace App\Exceptions;

class ArticleNotFoundException extends \Exception
{
    protected $message = "Cannot find article";
}
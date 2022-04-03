<?php

namespace App\Decorators;

use App\Classes\Argument;
use App\Services\ArgumentParserServiceInterface;
use App\Services\ArgumentParserService;

abstract class Decorator implements DecoratorInterface
{
    protected array $arguments = [];
    private ArgumentParserServiceInterface $argumentParserService;

    public function __construct(array $arguments, ArgumentParserServiceInterface $argumentParserService = null)
    {
        $this->arguments = $arguments;
        $this->argumentParserService = $argumentParserService ?? New ArgumentParserService();
    }

    public function getFieldData(): Argument
    {
        return $this->argumentParserService->parseRawInput($this->arguments, $this->getRequiredFields());
    }

    abstract public function getRequiredFields(): array;

}
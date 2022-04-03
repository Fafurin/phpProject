<?php

namespace App\Decorators;

use App\Enums\Article;

class ArticleDecorator extends Decorator implements DecoratorInterface
{
    public ?string $id;
    public ?string $authorId;
    public ?string $title;
    public ?string $text;

    public function __construct(array $arguments)
    {
        parent::__construct($arguments);

        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(Article::ID->value) ?? null;
        $this->authorId = $articleFieldData->get(Article::AUTHOR_ID->value) ?? null;
        $this->title = $articleFieldData->get(Article::TITLE->value) ?? null;
        $this->text = $articleFieldData->get(Article::TEXT->value) ?? null;
    }

    public function getRequiredFields(): array
    {
        return Article::getRequiredFields();
    }
}
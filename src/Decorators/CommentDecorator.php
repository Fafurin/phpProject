<?php

namespace App\Decorators;

use App\Enums\Comment;

class CommentDecorator extends Decorator implements DecoratorInterface
{
    public ?string $id;
    public ?string $authorId;
    public ?string $articleId;
    public ?string $text;

    public function __construct(array $arguments)
    {
        parent::__construct($arguments);

        $articleFieldData = $this->getFieldData();

        $this->id = $articleFieldData->get(Comment::ID->value) ?? null;
        $this->authorId = $articleFieldData->get(Comment::AUTHOR_ID->value) ?? null;
        $this->articleId = $articleFieldData->get(Comment::ARTICLE_ID->value) ?? null;
        $this->text = $articleFieldData->get(Comment::TEXT->value) ?? null;
    }

    public function getRequiredFields(): array
    {
        return Comment::getRequiredFields();
    }
}
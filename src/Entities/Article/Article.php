<?php

namespace App\Entities\Article;

class Article implements ArticleInterface
{
    public const TABLE_NAME = 'articles';

    private ?int $id = null;

    public function __construct(
        private int $authorId,
        private string $title,
        private string $text
    ){}

    public function getId():?int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string{
        return $this->text;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }
}
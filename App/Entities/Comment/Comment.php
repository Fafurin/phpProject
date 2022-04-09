<?php

namespace App\Entities\Comment;

class Comment implements CommentInterface
{
    public const TABLE_NAME = 'comments';
    public const ENTITY_TYPE = 2;

    public function __construct(
        private int $id,
        private int $authorId,
        private int $articleId,
        private string $text
    ){}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }

    public function getEntityType(): string{
        return static::ENTITY_TYPE;
    }
}
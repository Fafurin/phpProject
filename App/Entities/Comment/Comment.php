<?php

namespace App\Entities\Comment;

use App\Entities\User\User;
use App\Traits\Author;
use App\Traits\Id;
use App\Traits\Text;

class Comment implements CommentInterface
{
    use Id;
    use Author;
    use Text;

    public const TABLE_NAME = 'comments';
    public const ENTITY_TYPE = 2;
    private int $articleId;

    public function __construct(User $author, int $articleId, string $text)
    {
        $this->author = $author;
        $this->articleId = $articleId;
        $this->text = $text;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getTableName(): string{
        return static::TABLE_NAME;
    }

    public function getEntityType(): string{
        return static::ENTITY_TYPE;
    }
}
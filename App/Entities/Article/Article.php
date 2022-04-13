<?php

namespace App\Entities\Article;

use App\Entities\User\User;
use App\Traits\Author;
use App\Traits\Id;
use App\Traits\Text;
use App\Traits\Title;

class Article implements ArticleInterface
{
    use Id;
    use Title;
    use Text;
    use Author;

    public const TABLE_NAME = 'articles';
    public const ENTITY_TYPE = 1;

    public function __construct(
        private User $author,
        private string $title,
        private string $text) {}

    public function getTableName(): string{
        return static::TABLE_NAME;
    }

    public function getEntityType(): string{
        return static::ENTITY_TYPE;
    }
}
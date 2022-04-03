<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\Comment\Comment;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $statement;

    public function __construct(
        private ?ConnectorInterface $connector = null)
    {
        $this->connector = $connector ?? new SqLiteConnector();
        $this->statement =$this->connector->getConnection()->prepare($this->getSql());
    }

    public function handle(CommandInterface $command): void
    {
        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();

        $this->statement->execute([
            ':authorId' => $comment->getAuthorId(),
            ':articleId' => $comment->getArticleId(),
            ':text' => $comment->getText()
        ]);
    }

    public function getSql(): string
    {
        return " INSERT INTO " . Comment::TABLE_NAME . " (author_id, article_id, text) 
                 VALUES (:authorId, :articleId, :text)";
    }
}
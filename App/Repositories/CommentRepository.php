<?php

namespace App\Repositories;

use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use PDO;
use Psr\Log\LoggerInterface;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{

    public function __construct(
        ConnectionInterface $connection,
        private LoggerInterface $logger)
    {
        parent::__construct($connection);
    }

    /**
     * @throws CommentNotFoundException
     */
    public function get(int $id): Comment
    {
        $statement = $this->connection->prepare('SELECT * FROM ' . COMMENT::TABLE_NAME . ' WHERE id = :id');
        $statement->execute([
            ':id' => (string)$id
        ]);
        return $this->getComment($statement);
    }

    /**
     * @throws CommentNotFoundException
     */
    public function getComment(\PDOStatement $statement): Comment {
        $commentData = $statement->fetch(PDO::FETCH_OBJ);

        if(!$commentData){
            $this->logger->error("Comment not found");
            throw new CommentNotFoundException("Comment not found");
        }

        return new Comment(
            $commentData->id,
            $commentData->author_id,
            $commentData->article_id,
            $commentData->text
        );
    }

    /**
     * @throws CommentNotFoundException
     */
    public function getCommentById(int $id): Comment{
        $statement = $this->connection->prepare(
            'SELECT * FROM ' . COMMENT::TABLE_NAME . ' WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id
        ]);

        return $this->getComment($statement);
    }
}
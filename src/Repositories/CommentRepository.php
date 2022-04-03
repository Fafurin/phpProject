<?php

namespace App\Repositories;

use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use PDO;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{

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
            throw new CommentNotFoundException("Cannot find comment");
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
<?php

namespace App\Repositories;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use PDO;
use Psr\Log\LoggerInterface;

class CommentRepository extends EntityRepository implements CommentRepositoryInterface
{

    public function __construct(
        ConnectionInterface $connection,
        private ?UserRepositoryInterface $userRepository = null)
    {
        parent::__construct($connection);
    }

    /**
     * @throws CommentNotFoundException
     */
    public function findById(int $id): Comment
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

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $commentData = $statement->fetch(PDO::FETCH_OBJ);

        if(!$commentData){
            $logger->error("Comment not found");
            throw new CommentNotFoundException("Comment not found");
        }

        $comment = new Comment(
            $this->userRepository->findById($commentData->author_id),
            $commentData->article_id,
            $commentData->text
        );

        $comment->setId($commentData->id);
        return $comment;
    }
}
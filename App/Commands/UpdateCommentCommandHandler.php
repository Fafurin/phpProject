<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepository;
use App\Repositories\CommentRepositoryInterface;

class UpdateCommentCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $statement;

    public function __construct(
        private ?CommentRepositoryInterface $commentRepository = null,
        private ?ConnectorInterface $connector = null)
    {
        $this->commentRepository = $this->commentRepository ?? new CommentRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->statement =$this->connector->getConnection()->prepare($this->getSql());
    }

    /**
     * @throws CommentNotFoundException
     */
    public function handle(CommandInterface $command): void{
        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();
        $id = $command->getId();

        if($this->isCommentExists($id)){
            $this->statement->execute([
                ':id' => (string)$id,
                ':authorId' => $comment->getAuthorId(),
                ':articleId' => $comment->getArticleId(),
                ':text' => $comment->getText()
            ]);
        } else {
            throw new CommentNotFoundException;
        }
    }

    public function isCommentExists(int $id): bool{
        try{
            $this->commentRepository->getCommentById($id);
        } catch (CommentNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "UPDATE " . Comment::TABLE_NAME . " 
                SET author_id = :authorId, article_id = :articleId, text = :text 
                WHERE id = :id";
    }
}
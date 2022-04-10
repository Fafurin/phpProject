<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateCommentCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ?CommentRepositoryInterface $commentRepository = null,
        private ConnectionInterface $connection,
        private LoggerInterface $logger)
    {}

    /**
     * @throws CommentNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $this->logger->info('Update comment command started');

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();
        $id = $command->getId();

        if($this->isCommentExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id,
                ':authorId' => $comment->getAuthorId(),
                ':articleId' => $comment->getArticleId(),
                ':text' => $comment->getText()
            ]);
        } else {
            $this->logger->warning("The comment with this id: $id not found");
            throw new CommentNotFoundException;
        }
    }

    public function isCommentExists(int $id): bool{
        try{
            $this->commentRepository->findById($id);
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
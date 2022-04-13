<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Exceptions\CommentNotFoundException;
use App\Repositories\CommentRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateCommentCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ConnectionInterface $connection,
        private ?CommentRepositoryInterface $commentRepository = null){}

    /**
     * @throws CommentNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Update comment command started');

        /**
         * @var CreateEntityCommand $command
         */

        $comment = $command->getEntity();

        /**
         * @var Comment $comment
         */
        $id = $comment->getId();

        if($this->isCommentExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id,
                ':authorId' => $comment->getAuthor()->getId(),
                ':articleId' => $comment->getArticleId(),
                ':text' => $comment->getText()
            ]);
        } else {
            $logger->warning("The comment with this id: $id not found");
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
        return "UPDATE comments 
                SET author_id = :authorId, article_id = :articleId, text = :text 
                WHERE id = :id";
    }
}
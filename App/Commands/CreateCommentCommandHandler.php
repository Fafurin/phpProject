<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use App\Entities\EntityInterface;
use App\Repositories\CommentRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private ?CommentRepositoryInterface $commentRepository = null
    ){}

    public function handle(CommandInterface $command): EntityInterface
    {
        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Create comment command started');

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();

        $this->connection->prepare($this->getSql())->execute([
            ':authorId' => $comment->getAuthor()->getId(),
            ':articleId' => $comment->getArticleId(),
            ':text' => $comment->getText()
        ]);

        $logger->info("Comment created");

        return $this->commentRepository->findById($this->connection->lastInsertId());
    }

    public function getSql(): string
    {
        return " INSERT INTO comments (author_id, article_id, text) 
                 VALUES (:authorId, :articleId, :text)";
    }
}
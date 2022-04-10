<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Comment\Comment;
use Psr\Log\LoggerInterface;

class CreateCommentCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private LoggerInterface $logger){}

    public function handle(CommandInterface $command): void
    {

        $this->logger->info('Create comment command started');

        /**
         * @var Comment $comment
         */
        $comment = $command->getEntity();

        $this->connection->prepare($this->getSql())->execute([
            ':authorId' => $comment->getAuthor()->getId(),
            ':articleId' => $comment->getArticleId(),
            ':text' => $comment->getText()
        ]);

        $this->logger->info("Comment created");
    }

    public function getSql(): string
    {
        return " INSERT INTO " . Comment::TABLE_NAME . " (author_id, article_id, text) 
                 VALUES (:authorId, :articleId, :text)";
    }
}
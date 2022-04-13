<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private ?ArticleRepositoryInterface $articleRepository = null
    ){}

    /**
     * @var CreateEntityCommand $command
     */
    public function handle(CommandInterface $command): Article
    {

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Create article command started');

        /**
         * @var Article $article
         */
        $article = $command->getEntity();

        $this->connection->prepare($this->getSQL())->execute(
                [
                    ':author_id' => $article->getAuthor()->getId(),
                    ':title' => $article->getTitle(),
                    ':text' => $article->getText(),
                ]
            );
        $logger->info("Article created");

        return $this->articleRepository->findById($this->connection->lastInsertId());
    }

    public function getSQL(): string
    {
        return "INSERT INTO articles (author_id, title, text) 
        VALUES (:author_id, :title, :text)";
    }
}
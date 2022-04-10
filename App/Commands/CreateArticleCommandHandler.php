<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\ArticleTitleExistException;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;


class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private ConnectionInterface $connection,
        private LoggerInterface $logger)
    {}

    /**
     * @throws ArticleTitleExistException
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info('Create article command started');

        /**
         * @var Article $article
         */
        $article = $command->getEntity();

        $title = $article->getTitle();

        if(!$this->isArticleExists($title)) {
            $this->connection->prepare($this->getSql())->execute([
                ':authorId' => $article->getAuthor()->getId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText()
            ]);

            $this->logger->info("Article created with title: $title");

        }else{
            $this->logger->warning("The article with this title: $title already exists");
            throw new ArticleTitleExistException();
        }
    }

    public function isArticleExists(string $title): bool{
        try{
            $this->articleRepository->getArticleByTitle($title);
        }catch (ArticleNotFoundException){
            return false;
        }
        return true;
    }

    public function getSql(): string
    {
        return "INSERT INTO " . Article::TABLE_NAME . " (author_id, title, text) 
                 VALUES (:authorId, :title, :text)";
    }
}
<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateArticleCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ?ArticleRepositoryInterface $articleRepository = null,
        private ConnectionInterface $connection,
        private LoggerInterface $logger)
    {}

    /**
     * @throws ArticleNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $this->logger->info('Update article command started');

        /**
         * @var Article $article
         */
        $article = $command->getEntity();
        $id = $command->getId();

        if($this->isArticleExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id,
                ':authorId' => $article->getAuthorId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText()
            ]);
        }else{
            $this->logger->warning("The article with this id: $id not found");
            throw new ArticleNotFoundException();
        }
    }

    public function isArticleExists(int $id): bool{
        try{
            $this->articleRepository->getArticleById($id);
        } catch (ArticleNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "UPDATE " . Article::TABLE_NAME . " 
                SET author_id = :authorId, title = :title, text = :text 
                WHERE id = :id";
    }
}
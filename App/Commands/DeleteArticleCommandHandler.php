<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteArticleCommandHandler implements CommandHandlerInterface
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

        $this->logger->info('Delete article command started');

        /**
         * @var Article $article
         */
        $id = $command->getId();

        if($this->isArticleExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id
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
        return "DELETE FROM " . Article::TABLE_NAME . " WHERE id = :id";
    }
}
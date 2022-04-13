<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateArticleCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ConnectionInterface $connection,
        private ?ArticleRepositoryInterface $articleRepository = null){}

    /**
     * @throws ArticleNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Update article command started');

        /**
         * @var CreateEntityCommand $command
         */

        $article = $command->getEntity();

        /**
         * @var Article $article
         */
        $id = $article->getId();

        if($this->isArticleExists($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => (string)$id,
                ':authorId' => $article->getAuthor()->getId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText()
            ]);
        }else{
            $logger->warning("The article with this id: $id not found");
            throw new ArticleNotFoundException();
        }
    }

    public function isArticleExists(int $id): bool{
        try{
            $this->articleRepository->findById($id);
        } catch (ArticleNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "UPDATE articles 
                SET author_id = :authorId, title = :title, text = :text 
                WHERE id = :id";
    }
}
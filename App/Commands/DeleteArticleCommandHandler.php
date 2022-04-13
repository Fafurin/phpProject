<?php

namespace App\Commands;

use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use Psr\Log\LoggerInterface;

class DeleteArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private ?ArticleRepository $articleRepository = null){}

    /**
     * @throws ArticleNotFoundException
     */
    public function handle(CommandInterface $command): void{

        $logger = DIContainer::getInstance()->get(LoggerInterface::class);

        $logger->info('Delete article command started');

        /**
         * @var CreateEntityCommand $command
         */

        $article = $command->getEntity();

        $id = $article->getId();

        if($this->articleRepository->getArticleById($id)){
            $this->connection->prepare($this->getSql())->execute([
                ':id' => $id
            ]);
        }else{
            $logger->warning("The article with this id: $id not found");
            throw new ArticleNotFoundException();
        }
    }

    public function getSQL(): string
    {
        return "DELETE FROM articles WHERE id = :id";
    }
}
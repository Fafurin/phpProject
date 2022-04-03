<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;

class UpdateArticleCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $statement;

    public function __construct(
        private ?ArticleRepositoryInterface $articleRepository = null,
        private ?ConnectorInterface $connector = null)
    {
        $this->articleRepository = $this->articleRepository ?? new ArticleRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->statement =$this->connector->getConnection()->prepare($this->getSql());
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function handle(CommandInterface $command): void{
        /**
         * @var Article $article
         */
        $article = $command->getEntity();
        $id = $command->getId();

        if($this->isArticleExists($id)){
            $this->statement->execute([
                ':id' => (string)$id,
                ':authorId' => $article->getAuthorId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText()
            ]);
        }else{
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
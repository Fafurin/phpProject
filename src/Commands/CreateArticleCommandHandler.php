<?php

namespace App\Commands;

use App\Connections\ConnectorInterface;
use App\Connections\SqLiteConnector;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\ArticleTitleExistException;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;

class CreateArticleCommandHandler implements CommandHandlerInterface
{
    private \PDOStatement|false $statement;

    public function __construct(
        private ?ArticleRepositoryInterface $articleRepository = null,
        private ?ConnectorInterface $connector = null)
    {
        $this->articleRepository = $this->articleRepository ?? new ArticleRepository();
        $this->connector = $connector ?? new SqLiteConnector();
        $this->statement = $this->connector->getConnection()->prepare($this->getSql());
    }

    /**
     * @throws ArticleTitleExistException
     */
    public function handle(CommandInterface $command): void
    {
        /**
         * @var Article $article
         */
        $article = $command->getEntity();

        $title = $article->getTitle();

        if(!$this->isArticleExists($title)) {
            $this->statement->execute([
                ':authorId' => $article->getAuthorId(),
                ':title' => $article->getTitle(),
                ':text' => $article->getText()
            ]);
        }else{
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
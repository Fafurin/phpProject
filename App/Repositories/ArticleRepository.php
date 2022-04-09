<?php

namespace App\Repositories;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    public function __construct(
        ConnectionInterface $connection,
        private LoggerInterface $logger)
    {
        parent::__construct($connection);
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function get(int $id): Article
    {
        $statement = $this->connection->prepare('SELECT * FROM ' . ARTICLE::TABLE_NAME . ' WHERE id = :id');
        $statement->execute([
            ':id' => (string)$id
        ]);
        return $this->getArticle($statement);
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function getArticle(PDOStatement $statement): Article {
        $articleData = $statement->fetch(PDO::FETCH_OBJ);

        if(!$articleData){
            $this->logger->error("Article not found");
            throw new ArticleNotFoundException("Article not found");
        }

        return new Article(
            $articleData->author_id,
            $articleData->title,
            $articleData->text
        );
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function getArticleByTitle(string $title): Article{

        $statement = $this->connection->prepare(
            'SELECT * FROM ' . ARTICLE::TABLE_NAME . ' WHERE title = :title'
        );

        $statement->execute([
            ':title' => $title
        ]);

        return $this->getArticle($statement);
    }

    public function getArticleById(int $id): Article{

        $statement = $this->connection->prepare(
            'SELECT * FROM ' . ARTICLE::TABLE_NAME . ' WHERE id = :id'
        );

        $statement->execute([
            ':id' => $id
        ]);

        return $this->getArticle($statement);
    }

}
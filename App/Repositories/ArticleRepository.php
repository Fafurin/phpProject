<?php

namespace App\Repositories;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Exceptions\ArticleNotFoundException;
use PDO;
use PDOStatement;

class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
    public function __construct(
        ConnectionInterface $connection,
        private ?UserRepositoryInterface $userRepository = null
    )
    {
        $this->connection = $connection;
        parent::__construct($connection);
    }

    /**
     * @throws \Exception
     */
    public function findById(int $id): Article
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM articles WHERE id = :id'
        );

        $statement->execute([
            ':id' => (string)$id,
        ]);

        return $this->getArticle($statement, $id);
    }

    /**
     * @throws ArticleNotFoundException
     */
    private function getArticle(PDOStatement $statement, int $id): Article
    {
        $articleData = $statement->fetch(PDO::FETCH_OBJ);
        if (!$articleData) {
            throw new ArticleNotFoundException("The article with id: $id not found");
        }

        $article = new Article(
            $this->userRepository->findById($articleData->author_id),
            $articleData->title,
            $articleData->text
        );

        $article->setId($articleData->id);
        return $article;
    }
}
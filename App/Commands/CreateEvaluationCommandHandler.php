<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\CommentNotFoundException;
use App\Exceptions\EvaluationExistException;
use App\Exceptions\EvaluationNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\EvaluationRepositoryInterface;
use App\Repositories\UserRepositoryInterface;

class CreateEvaluationCommandHandler implements CommandHandlerInterface
{

    private \PDOStatement|false $statement;

    public function __construct(
        private ?EvaluationRepositoryInterface $evaluationRepository = null,
        private ?UserRepositoryInterface       $userRepository = null,
        private ?ArticleRepositoryInterface    $articleRepository = null,
        private ?CommentRepositoryInterface    $commentRepository = null,
        private ConnectionInterface            $connection)
    {
        $this->statement = $connection->prepare($this->getSql());
    }

    /**
     * @throws EvaluationExistException
     * @throws UserNotFoundException
     * @throws ArticleNotFoundException
     * @throws EvaluationNotFoundException
     * @throws CommentNotFoundException
     */
    public function handle(CommandInterface $command): void
    {
        /**
         * @var Evaluation $evaluation
         */
        $evaluation = $command->getEntity();
        $authorId = $evaluation->getAuthorId();
        $entityId = $evaluation->getEntityId();
        $evaluationType = $evaluation->getEvaluationType();
        $entityType = $evaluation->getEntityType();
//        var_dump($entityType);
//        die();
        if ($this->userRepository->getUserById($authorId)) {
            switch ($entityType) {
                case 1: // статья
                    if ($this->articleRepository->getArticleById($entityId)) {
                        if (!$this->isEvaluationExists($authorId, $entityId, $evaluationType, $entityType)) {
                                $this->statement->execute([
                                    ':authorId' => $authorId,
                                    ':entityId' => $entityId,
                                    ':evaluationType' => $evaluationType,
                                    ':entityType' => $entityType,
                                ]);
                            } else {
                                throw new EvaluationExistException();
                            }
                         }else throw new ArticleNotFoundException;
                case 2: //комментарий
                    if ($this->commentRepository->getCommentById($entityId)) {
                        if (!$this->isEvaluationExists($authorId, $entityId, $evaluationType, $entityType)) {
                            $this->statement->execute([
                                ':authorId' => $authorId,
                                ':entityId' => $entityId,
                                ':evaluationType' => $evaluationType,
                                ':entityType' => $entityType,
                            ]);
                        } else {
                            throw new EvaluationExistException();
                        }
                    }else throw new CommentNotFoundException;
                }
        } else throw new UserNotFoundException;
    }

    public function isEvaluationExists(int $authorId, int $entityId, int $evaluationType, int $entityType): bool{
        try{
            $this->evaluationRepository->getEvaluationByAuthorIdAndEntityId($authorId, $entityId, $evaluationType, $entityType);
        } catch (EvaluationNotFoundException){
            return false;
        }
        return true;
    }

    public function getSQL(): string
    {
        return "INSERT INTO " . Evaluation::TABLE_NAME ." (author_id, entity_id, evaluation_type, entity_type) 
                 VALUES (:authorId, :entityId, :evaluationType, :entityType)";
    }
}
<?php

namespace App\Repositories;

use App\Drivers\ConnectionInterface;
use App\Entities\Evaluation\Evaluation;
use App\Exceptions\EvaluationNotFoundException;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class EvaluationRepository extends EntityRepository implements EvaluationRepositoryInterface
{

    public function __construct(
        ConnectionInterface $connection,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger)
    {
        parent::__construct($connection);
    }
    /**
     * @throws EvaluationNotFoundException
     */
    public function findById(int $id): Evaluation
    {
        $statement = $this->connection->prepare('SELECT * FROM ' . Evaluation::TABLE_NAME . ' WHERE id = :id');
        $statement->execute([
            ':id' => (string)$id,
        ]);
        return $this->getEvaluation($statement);
    }

    /**
     * @throws EvaluationNotFoundException
     */
    public function getEvaluation(PDOStatement $statement): Evaluation
    {
        $evaluationData = $statement->fetch(PDO::FETCH_OBJ);

        if (!$evaluationData) {
            $this->logger->error("Evaluation not found");
            throw new EvaluationNotFoundException();
        }

        $evaluation = new Evaluation(
            $this->userRepository->findById($evaluationData->author_id),
            $evaluationData->entity_id,
            $evaluationData->evaluation_type,
            $evaluationData->entity_type,
        );

        $evaluation->setId($evaluationData->id);
        return $evaluation;
    }

    /**
     * @throws EvaluationNotFoundException
     */
    public function getEvaluationByAuthorIdAndEntityId(int $authorId, int $entityId, int $evaluationType, int $entityType): Evaluation
    {
        $tableName = '';
        if($entityType == 1){
            $tableName = "articles";
        } elseif($entityType == 2){
            $tableName = "comments";
        }
        $statement = $this->connection->prepare(
            'SELECT evaluations.author_id, entity_id, evaluation_type, entity_type 
                   FROM evaluations evaluations 
                   LEFT JOIN ' . $tableName . ' entities 
                   ON evaluations.entity_id = entities.id 
                   WHERE evaluations.author_id = :authorId 
                   AND evaluations.entity_id = :entityId 
                   AND evaluations.evaluation_type = :evaluationType 
                   AND evaluations.entity_type = :entityType'
        );

        $statement->execute([
            ':authorId' => $authorId,
            ':entityId' => $entityId,
            ':evaluationType' => $evaluationType,
            ':entityType' => $entityType,
        ]);

        return $this->getEvaluation($statement);
    }

}

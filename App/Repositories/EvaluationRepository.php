<?php

namespace App\Repositories;

use App\Entities\Evaluation\Evaluation;
use App\Exceptions\EvaluationNotFoundException;
use PDO;
use PDOStatement;

class EvaluationRepository extends EntityRepository implements EvaluationRepositoryInterface
{
    /**
     * @throws EvaluationNotFoundException
     */
    public function get(int $id): Evaluation
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
            throw new EvaluationNotFoundException();
        }

        return new Evaluation(
            $evaluationData->author_id,
            $evaluationData->entity_id,
            $evaluationData->evaluation_type,
            $evaluationData->entity_type,
        );
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
            'SELECT * FROM evaluations e 
                  LEFT JOIN ' . $tableName . ' c 
                  ON e.entity_id = c.id 
                  WHERE e.author_id = :authorId 
                  AND e.entity_id = :entityId 
                  AND e.evaluation_type = :evaluationType 
                  AND e.entity_type = :entityType'
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

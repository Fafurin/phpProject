<?php

namespace App\Commands;

use App\Drivers\ConnectionInterface;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
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
use Psr\Log\LoggerInterface;

class CreateEvaluationCommandHandler implements CommandHandlerInterface
{

    public function __construct(
        private ?EvaluationRepositoryInterface $evaluationRepository = null,
        private ?UserRepositoryInterface       $userRepository = null,
        private ?ArticleRepositoryInterface    $articleRepository = null,
        private ?CommentRepositoryInterface    $commentRepository = null,
        private ConnectionInterface            $connection,
        private LoggerInterface $logger){}

    /**
     * @throws EvaluationExistException
     * @throws UserNotFoundException
     * @throws ArticleNotFoundException
     * @throws CommentNotFoundException
     */
    public function handle(CommandInterface $command): void
    {
        $this->logger->info('Create evaluation command started');

        /**
         * @var Evaluation $evaluation
         */

        $evaluation = $command->getEntity();

        $authorId = $evaluation->getAuthor()->getId();
        $entityId = $evaluation->getEntityId();
        $evaluationType = $evaluation->getEvaluationType();
        $entityType = $evaluation->getEntityType();

        if ($this->userRepository->findById($authorId)) {
            switch ($entityType) {
                case Article::ENTITY_TYPE:
                    if ($this->articleRepository->findById($entityId)) {
                        $this->executeStatement($authorId, $entityId, $evaluationType, $entityType);
                    }else {
                        $this->logger->warning("The article with this id: $entityId not found");
                        throw new ArticleNotFoundException;
                    } break;

                case Comment::ENTITY_TYPE:
                    if ($this->commentRepository->findById($entityId)) {
                        $this->executeStatement($authorId, $entityId, $evaluationType, $entityType);
                    }else {
                        $this->logger->warning("The comment with this id: $entityId not found");
                        throw new CommentNotFoundException;
                    }
                }
        } else {
            $this->logger->warning("The user with this id: $authorId not found");
            throw new UserNotFoundException;
        }
    }

    /**
     * @throws EvaluationExistException
     */
    public function executeStatement(int $authorId, int $entityId, int $evaluationType, int $entityType): void{
        if (!$this->isEvaluationExists($authorId, $entityId, $evaluationType, $entityType)) {
            $this->connection->prepare($this->getSql())->execute([
                ':authorId' => $authorId,
                ':entityId' => $entityId,
                ':evaluationType' => $evaluationType,
                ':entityType' => $entityType,
            ]);
        } else {
            $this->logger->warning("The user with this id: $authorId has already rated this entity");
            throw new EvaluationExistException();
        }
    }

    public function isEvaluationExists(int $authorId, int $entityId, int $evaluationType, int $entityType): bool{
        try{
            $this->evaluationRepository->getEvaluationByAuthorIdAndEntityId($authorId, $entityId, $evaluationType, $entityType);
        }
        catch (EvaluationNotFoundException){
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
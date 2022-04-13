<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Commands\CreateEvaluationCommandHandler;
use App\Commands\CreateUserCommandHandler;
use App\Entities\Article\Article;
use App\Entities\Comment\Comment;
use App\Entities\EntityInterface;
use App\Entities\Evaluation\Evaluation;
use App\Entities\User\User;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private CreateUserCommandHandler $createUserCommandHandler,
        private CreateArticleCommandHandler $createArticleCommandHandler,
        private CreateCommentCommandHandler $createCommentCommandHandler,
        private CreateEvaluationCommandHandler $createEvaluationCommandHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addArgument('usersQuantity', InputArgument::REQUIRED, 'Users quantity')
            ->addArgument('articlesQuantity', InputArgument::REQUIRED, 'Articles quantity')
            ->addArgument('commentsQuantity', InputArgument::REQUIRED, 'Comments quantity')
            ->addArgument('articleEvaluationsQuantity', InputArgument::REQUIRED, 'Article evaluations quantity')
            ->addArgument('commentEvaluationsQuantity', InputArgument::REQUIRED, 'Comment evaluations quantity')
        ;
    }

    /**
     * @throws \App\Exceptions\EvaluationExistException
     * @throws \App\Exceptions\UserNotFoundException
     * @throws \App\Exceptions\CommentNotFoundException
     * @throws \App\Exceptions\ArticleNotFoundException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $usersQuantity = $input->getArgument('usersQuantity');
        $articlesQuantity = $input->getArgument('articlesQuantity');
        $commentsQuantity = $input->getArgument('commentsQuantity');
        $articleEvaluationsQuantity = $input->getArgument('articleEvaluationsQuantity');
        $commentEvaluationsQuantity = $input->getArgument('commentEvaluationsQuantity');


        $users = [];
        $articles = [];
        $comments = [];

        // создание пользователей
        for ($i = 0; $i < $usersQuantity; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created with the email: ' . $user->getEmail());
        }

        // создание статей
        for ($i = 0; $i < $articlesQuantity; $i++) {
           $userNumber = mt_rand(0, count($users) - 1);
           $user = $users[$userNumber];
           $article = $this->createFakeArticle($user);
           $articles[] = $article;
           $output->writeln('Article created with the title: ' . $article->getTitle());
        }

        // создание комментариев
        for ($i = 0; $i < $commentsQuantity; $i++) {
           $userNumber = mt_rand(0, count($users) - 1);
           $articleNumber = mt_rand(0, count($articles) - 1);
           $article = $articles[$articleNumber];
           $comment = $this->createFakeComment($users[$userNumber], $article);
           $comments[] = $comment;
           $output->writeln('Comment created for an article titled: ' . $article->getTitle());
        }

        // создание лайков/дизлайков для статей
        for ($i = 0; $i < $articleEvaluationsQuantity; $i++) {
            $userNumber = mt_rand(0, count($users) - 1);
            $articleNumber = mt_rand(0, count($articles) - 1);
            $article = $articles[$articleNumber];
            $this->createFakeArticleEvaluation($users[$userNumber], $article);
            $output->writeln('Evaluation created for an article titled: ' . $article->getTitle());
        }

        // создание лайков/дизлайков для комментариев
        for ($i = 0; $i < $commentEvaluationsQuantity; $i++) {
            $userNumber = mt_rand(0, count($users) - 1);
            $commentNumber = mt_rand(0, count($comments) - 1);
            $comment = $comments[$commentNumber];
            $this->createFakeCommentEvaluation($users[$userNumber], $comment);
            $output->writeln('Evaluation created for a comment');
        }


        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user =
            new User(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->email,
                $this->faker->password,
        );
        return $this->createUserCommandHandler->handle(new CreateEntityCommand($user));
    }

    private function createFakeArticle(User $author): Article
    {
        $article = new Article(
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );
        return $this->createArticleCommandHandler->handle(new CreateEntityCommand($article));
    }

    private function createFakeComment(User $author, Article $article): EntityInterface
    {
        $comment = new Comment(
            $author,
            $article->getId(),
            $this->faker->realText
        );
        return $this->createCommentCommandHandler->handle(new CreateEntityCommand($comment));
    }

    /**
     * @throws \App\Exceptions\EvaluationExistException
     * @throws \App\Exceptions\UserNotFoundException
     * @throws \App\Exceptions\CommentNotFoundException
     * @throws \App\Exceptions\ArticleNotFoundException
     */
    private function createFakeArticleEvaluation(User $author, Article $article): void
    {
        $evaluation = new Evaluation(
            $author,
            $article->getId(),
            mt_rand(1, 2),
            Article::ENTITY_TYPE
        );
        $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
    }

    private function createFakeCommentEvaluation(User $author, Comment $comment): void
    {
        $evaluation = new Evaluation(
            $author,
            $comment->getId(),
            mt_rand(1, 2),
            Comment::ENTITY_TYPE
        );
        $this->createEvaluationCommandHandler->handle(new CreateEntityCommand($evaluation));
    }
}
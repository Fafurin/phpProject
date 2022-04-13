<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateCommentCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Entities\Comment\Comment;
use App\Exceptions\ArticleNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateComment extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private ArticleRepositoryInterface $articleRepository,
        private CreateCommentCommandHandler $createCommentCommandHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('comment:create')
            ->setDescription('Creates new comment')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail address')
            ->addArgument('articleId', InputArgument::REQUIRED, 'Article id')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $output->writeln('Create comment command started');

        $email = $input->getArgument('email');

        $articleId = $input->getArgument('articleId');

        if (!$this->userExists($email)) {
           $output->writeln("User not found: $email");
           return Command::FAILURE;
        }

        if(!$this->articleExists($articleId)){
            $output->writeln("Article not found: $articleId");
            return Command::FAILURE;
        }

        $user = $this->usersRepository->getUserByEmail($email);

        $comment = new Comment(
            $user,
            $input->getArgument('articleId'),
            $input->getArgument('text'));

        $this->createCommentCommandHandler->handle(new CreateEntityCommand($comment));

        $output->writeln('Comment created');
        return Command::SUCCESS;
    }

    private function userExists(string $email): bool
    {
        try {
            $this->usersRepository->getUserByEmail($email);
        } catch (UserNotFoundException) {return false;}

        return true;
    }

    private function articleExists(int $id): bool
    {
        try {
            $this->articleRepository->findById($id);
        } catch (ArticleNotFoundException) {return false;}

        return true;
    }
}
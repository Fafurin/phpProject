<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateArticleCommandHandler;
use App\Commands\CreateEntityCommand;
use App\Entities\Article\Article;
use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateArticle extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private CreateArticleCommandHandler $createArticleCommandHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('article:create')
            ->setDescription('Creates new article')
            ->addArgument('email', InputArgument::REQUIRED, 'E-mail address')
            ->addArgument('title', InputArgument::REQUIRED, 'Title')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $output->writeln('Create article command started');

        $email = $input->getArgument('email');

        if (!$this->userExists($email)) {
           $output->writeln("User not found: $email");
           return Command::FAILURE;
        }

        $user = $this->usersRepository->getUserByEmail($email);

        $article = new Article(
            $user,
            $input->getArgument('title'),
            $input->getArgument('text'));

        $this->createArticleCommandHandler->handle(new CreateEntityCommand($article));

        $output->writeln('Article created');
        return Command::SUCCESS;
    }

    private function userExists(string $email): bool
    {
        try {
            $this->usersRepository->getUserByEmail($email);
        } catch (UserNotFoundException) {return false;}

        return true;
    }
}
<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateEntityCommand;
use App\Commands\DeleteUserCommandHandler;
use App\Repositories\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private DeleteUserCommandHandler $deleteUserCommandHandler
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('user:delete')
            ->setDescription('Delete a user')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of a user to delete'
            )
            ->addOption(
                'need-question',
                'nnq',
                InputOption::VALUE_NONE,
                'Do I need to ask before deleting',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        if ($input->getOption('need-question')) {
            $question = new ConfirmationQuestion(
                'Delete user [Y/n]? ',
                false
            );

            if (!$this->getHelper('question')->ask($input, $output, $question))
            {
                return Command::SUCCESS;
            }
        }

        $user = $this->userRepository->findById($input->getArgument('id'));
        $this->deleteUserCommandHandler->handle(new CreateEntityCommand($user));

        $output->writeln(sprintf("User %s deleted", $user->getId()));

        return Command::SUCCESS;
    }
}
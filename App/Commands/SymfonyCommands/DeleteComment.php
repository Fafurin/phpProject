<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateEntityCommand;
use App\Commands\DeleteCommentCommandHandler;
use App\Repositories\CommentRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeleteComment extends Command
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private DeleteCommentCommandHandler $deleteCommentCommandHandler
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('comment:delete')
            ->setDescription('Delete a comment')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of a comment to delete'
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
                'Delete comment [Y/n]? ',
                false
            );

            if (!$this->getHelper('question')->ask($input, $output, $question))
            {
                return Command::SUCCESS;
            }
        }

        $comment = $this->commentRepository->findById($input->getArgument('id'));
        $this->deleteCommentCommandHandler->handle(new CreateEntityCommand($comment));

        $output->writeln(sprintf("Comment %s deleted", $comment->getId()));

        return Command::SUCCESS;
    }
}
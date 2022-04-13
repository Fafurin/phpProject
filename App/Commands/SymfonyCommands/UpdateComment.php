<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateEntityCommand;
use App\Commands\UpdateCommentCommandHandler;
use App\Repositories\CommentRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateComment extends Command
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UpdateCommentCommandHandler $updateCommentCommandHandler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('comment:update')
            ->setDescription('Update a comment')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of a comment to update'
            )
            ->addOption(
                'text',
                'txt',
                InputOption::VALUE_OPTIONAL,
                'Text',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $text = $input->getOption('text');

        if (empty($text)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $comment = $this->commentRepository->findById($input->getArgument('id'));

        $text = $input->getOption('text') ?? $comment->getText();

        $comment
            ->setText($text);

        $this->updateCommentCommandHandler->handle(new CreateEntityCommand($comment));

        $output->writeln(sprintf("Comment updated: %d",  $comment->getId()));

        return Command::SUCCESS;
    }
}
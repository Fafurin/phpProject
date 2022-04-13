<?php

namespace App\Commands\SymfonyCommands;

use App\Commands\CreateEntityCommand;
use App\Commands\UpdateArticleCommandHandler;
use App\Repositories\ArticleRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateArticle extends Command
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private UpdateArticleCommandHandler $updateArticleCommandHandler
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('article:update')
            ->setDescription('Update an article')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Id of an article to update'
            )
            ->addOption(
                'title',
                'ttl',
                InputOption::VALUE_OPTIONAL,
                'Title',
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
        $title = $input->getOption('title');
        $text = $input->getOption('text');

        if (empty($title) && empty($text)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $article = $this->articleRepository->findById($input->getArgument('id'));

        $title = $input->getOption('title') ?? $article->getTitle();
        $text = $input->getOption('text') ?? $article->getText();

        $article
            ->setTitle($title)
            ->setText($text);

        $this->updateArticleCommandHandler->handle(new CreateEntityCommand($article));

        $output->writeln(sprintf("Article updated: %d",  $article->getId()));

        return Command::SUCCESS;
    }
}
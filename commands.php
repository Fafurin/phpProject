<?php

use App\Commands\SymfonyCommands\CreateUser;
use App\Commands\SymfonyCommands\CreateArticle;
use App\Commands\SymfonyCommands\CreateComment;
use App\Commands\SymfonyCommands\DeleteComment;
use App\Commands\SymfonyCommands\DeleteUser;
use App\Commands\SymfonyCommands\UpdateArticle;
use App\Commands\SymfonyCommands\UpdateComment;
use App\Commands\SymfonyCommands\UpdateUser;
use App\Commands\SymfonyCommands\DeleteArticle;
use App\Commands\SymfonyCommands\PopulateDB;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';
$application = new Application();

$commandsClasses =
    [
        CreateUser::class,
        CreateArticle::class,
        CreateComment::class,
        UpdateUser::class,
        UpdateArticle::class,
        UpdateComment::class,
        DeleteUser::class,
        DeleteArticle::class,
        DeleteComment::class,
        PopulateDB::class,
    ];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();
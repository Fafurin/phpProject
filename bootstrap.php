<?php

use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Drivers\PdoConnectionDriver;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\CommentRepositoryInterface;
use App\Repositories\EvaluationRepository;
use App\Repositories\EvaluationRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = DIContainer::getInstance();

$container->bind(
    ConnectionInterface::class,
    PdoConnectionDriver::getInstance($_SERVER['DSN_DATABASE'])
);

$container->bind(
    UserRepositoryInterface::class,
    UserRepository::class
);

$container->bind(
    ArticleRepositoryInterface::class,
    ArticleRepository::class
);

$container->bind(
    CommentRepositoryInterface::class,
    CommentRepository::class
);

$container->bind(
    EvaluationRepositoryInterface::class,
    EvaluationRepository::class
);

$container->bind(
    LoggerInterface::class,
    new Logger('project')
);

$logger = new Logger('project');

$isNeedLogToFiles = (bool)$_SERVER['LOG_TO_FILES'];
$isNeedLogToConsole = (bool)$_SERVER['LOG_TO_CONSOLE'];

if($isNeedLogToFiles){
    $logger
        ->pushHandler(new StreamHandler(
        __DIR__ . '/.logs/project.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/.logs/project.error.log',
            LOGGER::ERROR,
            false
        ));
}

if($isNeedLogToConsole){
    $logger
        ->pushHandler(new StreamHandler(
        "php://stdout"
    ));
}

return $container;


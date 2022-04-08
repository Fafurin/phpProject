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

require_once __DIR__ . '/vendor/autoload.php';

$container = DIContainer::getInstance();

$container->bind(
    ConnectionInterface::class,
    PdoConnectionDriver::getInstance(SqLiteConfig::DSN)
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

return $container;


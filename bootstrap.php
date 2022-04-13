<?php

use App\Commands\TokenCommandHandler;
use App\Commands\TokenCommandHandlerInterface;
use App\config\SqLiteConfig;
use App\Container\DIContainer;
use App\Drivers\ConnectionInterface;
use App\Drivers\PdoConnectionDriver;
use App\Http\Auth\AuthenticationInterface;
use App\Http\Auth\BearerTokenAuthentication;
use App\Http\Auth\IdentificationInterface;
use App\Http\Auth\JsonBodyUserEmailIdentification;
use App\Http\Auth\JsonBodyUserIdIdentification;
use App\Http\Auth\PasswordAuthentication;
use App\Http\Auth\PasswordAuthenticationInterface;
use App\Http\Auth\TokenAuthenticationInterface;
use App\Queries\TokenQueryHandler;
use App\Queries\TokenQueryHandlerInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\AuthTokenRepository;
use App\Repositories\AuthTokenRepositoryInterface;
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
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = DIContainer::getInstance();

$faker = new Faker\Generator();

$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

$container->bind(
    Faker\Generator::class,
    $faker
);

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

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    AuthTokenRepositoryInterface::class,
    AuthTokenRepository::class
);


$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    TokenQueryHandlerInterface::class,
    TokenQueryHandler::class
);

$container->bind(
    TokenCommandHandlerInterface::class,
    TokenCommandHandler::class
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


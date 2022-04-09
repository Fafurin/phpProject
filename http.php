<?php

use App\Http\Actions\CreateArticleDislike;
use App\Http\Actions\CreateArticleLike;
use App\Http\Actions\CreateCommentDislike;
use App\Http\Actions\CreateCommentLike;
use App\Http\Actions\CreateUser;
use App\Http\Actions\CreateArticle;
use App\Http\Actions\CreateComment;
use App\Http\Actions\DeleteArticle;
use App\Http\Actions\DeleteComment;
use App\Http\Actions\DeleteUser;
use App\Http\Actions\FindUserByEmail;
use App\Http\Actions\FindArticleByTitle;
use App\Http\Actions\FindCommentById;
use App\Http\Actions\UpdateArticle;
use App\Http\Actions\UpdateComment;
use App\Http\Actions\UpdateUser;
use App\Http\ErrorResponse;
use App\Http\Request;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

/**
 * @var LoggerInterface $logger
 */
$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();

} catch (HttpException $exception) {
    $logger->warning($exception->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $exception) {
    $logger->warning($exception->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => FindUserByEmail::class,
        '/article/show' => FindArticleByTitle::class,
        '/comment/show' => FindCommentById::class,
    ],
    'POST' => [
        '/user/create' => CreateUser::class,
        '/user/update' => UpdateUser::class,
        '/user/delete' => DeleteUser::class,
        '/article/create' => CreateArticle::class,
        '/article/update' => UpdateArticle::class,
        '/article/delete' => DeleteArticle::class,
        '/comment/create' => CreateComment::class,
        '/comment/update' => UpdateComment::class,
        '/comment/delete' => DeleteComment::class,
        '/article/like/create' => CreateArticleLike::class,
        '/article/dislike/create' => CreateArticleDislike::class,
        '/comment/like/create' => CreateCommentLike::class,
        '/comment/dislike/create' => CreateCommentDislike::class,

    ],
];

if (!array_key_exists($method, $routes)) {
    $logger->info(sprintf('А user from the ip-address: %s tried to access a non-existent route', $_SERVER['REMOTE_ADDR']));
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    $logger->info("Route not found");
    (new ErrorResponse('Not found'))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (Exception $exception) {
    $logger->error($exception->getMessage());
    (new ErrorResponse($exception->getMessage()))->send();
}

$response->send();
<?php

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

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => new FindUserByEmail(),
        '/article/show' => new FindArticleByTitle(),
        '/comment/show' => new FindCommentById(),
    ],
    'POST' => [
        '/user/create' => new CreateUser(),
        '/user/update' => new UpdateUser(),
        '/user/delete' => new DeleteUser(),
        '/article/create' => new CreateArticle(),
        '/article/update' => new UpdateArticle(),
        '/article/delete' => new DeleteArticle(),
        '/comment/create' => new CreateComment(),
        '/comment/update' => new UpdateComment(),
        '/comment/delete' => new DeleteComment(),
    ],
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();
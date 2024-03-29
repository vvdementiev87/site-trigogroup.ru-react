<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header("Access-Control-Allow-Headers: X-Requested-With, Authorization");

use Psr\Log\LoggerInterface;
use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\ErrorResponse;
use devavi\leveltwo\Http\Actions\Auth\LogIn;
use devavi\leveltwo\Http\Actions\Auth\LogOut;
use devavi\leveltwo\Blog\Exceptions\AppException;
use devavi\leveltwo\Blog\Exceptions\HttpException;
use devavi\leveltwo\Http\Actions\Posts\CreatePost;
use devavi\leveltwo\Http\Actions\Posts\DeletePost;
use devavi\leveltwo\Http\Actions\Posts\FindByUuid;
use devavi\leveltwo\Http\Actions\Posts\ShowPosts;
use devavi\leveltwo\Http\Actions\Users\CreateUser;
use devavi\leveltwo\Http\Actions\Likes\CreatePostLike;
use devavi\leveltwo\Http\Actions\Users\FindByUsername;
use devavi\leveltwo\Http\Actions\Comments\CreateComment;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}


$routes = [
    'GET' => [
        '/index.php/users/show' => FindByUsername::class,
        '/index.php/posts/show' => ShowPosts::class,
    ],
    'POST' => [
        '/index.php/login' => LogIn::class,
        '/index.php/logout' => LogOut::class,
        '/index.php/users/create' => CreateUser::class,
        '/index.php/posts/create' => CreatePost::class,
        '/index.php/comments/create' => CreateComment::class,
        '/index.php/posts/likes/create' => CreatePostLike::class,
    ],
    'DELETE' => [
        '/index.php/posts' => DeletePost::class,
    ],

];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();

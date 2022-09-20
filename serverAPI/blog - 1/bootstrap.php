<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use Faker\Generator;
use Faker\Provider\Lorem;
use Psr\Log\LoggerInterface;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\nl_BE\Internet;
use Monolog\Handler\StreamHandler;
use devavi\leveltwo\Blog\Container\DIContainer;
use devavi\leveltwo\Http\Auth\PasswordAuthentication;
use devavi\leveltwo\Http\Auth\AuthenticationInterface;
use devavi\leveltwo\Http\Auth\IdentificationInterface;
use devavi\leveltwo\Http\Auth\BearerTokenAuthentication;
use devavi\leveltwo\Http\Auth\TokenAuthenticationInterface;
use devavi\leveltwo\Http\Auth\JsonBodyUsernameIdentification;
use devavi\leveltwo\Http\Auth\PasswordAuthenticationInterface;
use devavi\leveltwo\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use devavi\leveltwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use devavi\leveltwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use devavi\leveltwo\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use devavi\leveltwo\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use devavi\leveltwo\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use devavi\leveltwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(
    PDO::class,
    // Берём путь до файла базы данных SQLite
    // из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);


$logger = new Logger('blog');


if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger->pushHandler(
        new StreamHandler("php://stdout")
    );
}

$faker = new Generator();
// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

// Добавляем генератор тестовых данных
// в контейнер внедрения зависимостей
$container->bind(
    Generator::class,
    $faker
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

$container->bind(
    LoggerInterface::class,
    $logger

);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);


$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);


return $container;

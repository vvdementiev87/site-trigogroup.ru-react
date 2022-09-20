<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use devavi\leveltwo\Blog\Command\Posts\DeletePost;
use devavi\leveltwo\Blog\Command\Users\CreateUser;
use devavi\leveltwo\Blog\Command\Users\UpdateUser;
use devavi\leveltwo\Blog\Command\FakeData\PopulateDB;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);


// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

try {

    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}

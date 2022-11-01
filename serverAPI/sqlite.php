<?php
$servername = "localhost:3306";
$username = "trigouser";
$password = "MyoXnCgpf7nBr2Em!";
$dbname = "BLOG";
$connection = new PDO('mysql:host=' . $servername . '; dbname=' . $dbname, $username, $password, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

/* $connection->exec("DROP TABLE IF EXISTS users"); */

$connection->exec(
    "CREATE TABLE users (
    uuid TEXT NOT NULL PRIMARY KEY,
    username TEXT NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL
    );"
);

/* $connection->exec("DROP TABLE IF EXISTS posts"); */

$connection->exec(
    "CREATE TABLE posts (
    uuid TEXT NOT NULL PRIMARY KEY,
    author_uuid TEXT NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL
    );"
);

/* $connection->exec("DROP TABLE IF EXISTS comments"); */

$connection->exec(
    "CREATE TABLE comments (
    uuid TEXT NOT NULL PRIMARY KEY,
    post_uuid TEXT NOT NULL,
    author_uuid TEXT NOT NULL,
    text TEXT NOT NULL
    );"
);

//creating likes table

/* $connection->exec("DROP TABLE IF EXISTS likes"); */

$connection->exec(
    "CREATE TABLE likes (
    uuid TEXT NOT NULL PRIMARY KEY,
    user_uuid TEXT NOT NULL,
    post_uuid TEXT NOT NULL
    );"
);
/* 
$connection->exec(
    "INSERT INTO users (first_name, last_name) VALUES ('Ivan', 'Nikitin')"
); */
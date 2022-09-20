<?php
$servername = "localhost";
$username = "trigouser";
$password = "MyoXnCgpf7nBr2Em!";
$dbname = "RAVTO";
    $pdo = new PDO('mysql:host='.$servername.'; dbname='.$dbname, $username, $password, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

return $pdo ;
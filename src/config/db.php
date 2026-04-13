<?php

$host = getenv('MYSQLHOST') ?: getenv('DB_HOST');
$db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME');
$user = getenv('MYSQLUSER') ?: getenv('DB_USER');
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS');
$port = getenv('MYSQLPORT') ?: 3306;

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
<?php
$host = getenv('DB_HOST') ?: 'db'; 
$db   = getenv('DB_NAME') ?: 'solo_forum';
$user = getenv('DB_USER') ?: 'stas';
$pass = getenv('DB_PASS') ?: 'stas';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header('Location: /');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /');
    exit;
}

if ($post['user_id'] != $_SESSION['user_id']) {
    header('Location: /');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
$stmt->execute([$post_id]);

$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

header('Location: /');
exit;
<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$post_id = $_POST['post_id'];
$body    = trim($_POST['body']);

if (empty($body) || empty($post_id)) {
    header("Location: /posts/view.php?id=$post_id&error=empty");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO comments (post_id, user_id, body)
    VALUES (?, ?, ?)
");
$stmt->execute([$post_id, $_SESSION['user_id'], $body]);

header("Location: /posts/view.php?id=$post_id");
exit;
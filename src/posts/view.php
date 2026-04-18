<?php
session_start();
require_once '../config/db.php';

$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header('Location: /');
    exit;
}

$stmt = $pdo->prepare("
    SELECT posts.*, users.username, categories.name as category_name
    FROM posts
    JOIN users ON posts.user_id = users.id
    LEFT JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /');
    exit;
}

$stmt = $pdo->prepare("
    SELECT comments.*, users.username
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE comments.post_id = ?
    ORDER BY comments.created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }

    $body = trim($_POST['body']);

    if (empty($body)) {
        $error = 'Comment cannot be empty';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO comments (post_id, user_id, body)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$post_id, $_SESSION['user_id'], $body]);
        header("Location: /posts/view.php?id=$post_id");
        exit;
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="post-full">
        <?php if (!empty($post['image_path'])): ?>
            <img class="post-hero-image" src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post image">
        <?php endif; ?>
        <div class="post-meta">
            <span class="category"><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></span>
            <span>by <strong><?= htmlspecialchars($post['username']) ?></strong></span>
            <span><?= $post['created_at'] ?></span>
        </div>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p><?= nl2br(htmlspecialchars($post['body'])) ?></p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <a href="/posts/delete.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this post?')">
                Delete Post
            </a>
        <?php endif; ?>
    </div>
    <hr>
    <div class="comments-section">
        <h2>Comments (<?= count($comments) ?>)</h2>

        <?php if (empty($comments)): ?>
            <p>No comments yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-card">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <span><?= $comment['created_at'] ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($comment['body'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <hr>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="comment-form">
            <h3>Leave a comment</h3>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" action="/comments/create.php">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="body" rows="4" placeholder="Write a comment..." required></textarea>
                <button type="submit" class="btn">Post Comment</button>
            </form>
        </div>
    <?php else: ?>
        <div class="comment-login-cta">
            <h3>Join the discussion</h3>
            <p>Log in to share your opinion and leave a comment on this post.</p>
            <a href="/login.php" class="btn btn-primary">Login to Comment</a>
        </div>
    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?>
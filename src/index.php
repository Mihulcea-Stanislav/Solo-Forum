<?php

session_start();
require_once 'config/db.php';

$categories = $pdo->query('SELECT * FROM categories')->fetchAll();

$category_id = $_GET['category'] ?? null;

if ($category_id) {
    $stmt = $pdo->prepare('SELECT posts.*, users.username, categories.name as category_name FROM posts JOIN users ON posts.user_id = users.id LEFT JOIN categories ON posts.category_id = categories.id WHERE posts.category_id = ? ORDER BY posts.created_at DESC');
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query('SELECT posts.*, users.username, categories.name as category_name FROM posts JOIN users ON posts.user_id = users.id LEFT JOIN categories ON posts.category_id = categories.id ORDER BY posts.created_at DESC');
}

$posts = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <section class="hero">
        <p class="hero-kicker">Community-driven discussions</p>
        <h1>Build your perspective in public.</h1>
        <p>
            A clean place to post bold ideas, read thoughtful replies, and connect with creators.
        </p>
    </section>

    <div class="filters">
        <a href="/" class="<?= !$category_id ? 'active' : '' ?>">All Posts</a>
        <?php foreach ($categories as $cat): ?>
            <a href="/?category=<?= $cat['id'] ?>" class="<?= $category_id == $cat['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/posts/create.php" class="btn">Create New Post</a>
    <?php endif; ?>
    <?php if (empty($posts)): ?>
        <p>No posts found.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <?php if (!empty($post['image_path'])): ?>
                    <img class="post-cover" src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post cover image">
                <?php endif; ?>
                <div class='post-meta'>
                    <span class="category"><?= htmlspecialchars($post['category_name'] ?? 'Uncategorized') ?></span>
                    <span>by <?= htmlspecialchars($post['username']) ?></span>
                    <span><?= $post['created_at'] ?></span>
                </div>
                <h2>
                    <a href="/posts/view.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h2>
                <p><?= htmlspecialchars(substr($post['body'], 0, 150)) ?>...</p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
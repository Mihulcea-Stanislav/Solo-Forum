<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category_id = $_POST['category_id'];

    if (empty($title) || empty($body)) {
        $error = 'Title and body are required.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, body, category_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $body,
            $category_id ?: null
        ]);
        $post_id = $pdo->lastInsertId();
        header("Location: view.php?id=$post_id");
        exit;
    }
}
$categories = $pdo->query('SELECT * FROM categories')->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Create New Post</h1>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="Enter post title" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id">
                <option value="">No Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                            <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="body">Body:</label>
            <textarea id="body" name="body" rows="8" placeholder="Write your post here..." required><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create Post</button>
        <a href="/" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
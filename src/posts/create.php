<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$upload_dir = __DIR__ . '/../uploads';

$column_stmt = $pdo->query("SHOW COLUMNS FROM posts LIKE 'image_path'");
if (!$column_stmt->fetch()) {
    $pdo->exec("ALTER TABLE posts ADD COLUMN image_path VARCHAR(255) NULL AFTER body");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category_id = $_POST['category_id'];
    $image_path = null;

    if (empty($title) || empty($body)) {
        $error = 'Title and body are required.';
    } else {
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['post_image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed. Please try again.';
            } else {
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $original_name = $_FILES['post_image']['name'];
                $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                $tmp_name = $_FILES['post_image']['tmp_name'];
                $max_size = 4 * 1024 * 1024;

                if (!in_array($extension, $allowed_ext, true)) {
                    $error = 'Only JPG, PNG, WEBP, or GIF images are allowed.';
                } elseif ($_FILES['post_image']['size'] > $max_size) {
                    $error = 'Image is too large. Max size is 4MB.';
                } else {
                    $mime_type = mime_content_type($tmp_name);
                    if (strpos($mime_type, 'image/') !== 0) {
                        $error = 'Invalid image file.';
                    } else {
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0775, true);
                        }

                        $file_name = uniqid('post_', true) . '.' . $extension;
                        $target_path = $upload_dir . '/' . $file_name;

                        if (!move_uploaded_file($tmp_name, $target_path)) {
                            $error = 'Could not save uploaded image.';
                        } else {
                            $image_path = '/uploads/' . $file_name;
                        }
                    }
                }
            }
        }
    }

    if (empty($error)) {
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, body, image_path, category_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $body,
            $image_path,
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

<div class="container create-post-page">
    <h1>Create New Post</h1>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="create-post-form">
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
        <div class="form-group image-upload-row">
            <div>
                <label for="post_image">Post Image (optional):</label>
                <p class="field-help">Upload JPG, PNG, WEBP, or GIF (max 4MB)</p>
                <p id="selected-image-name" class="selected-image-name">No image selected</p>
            </div>
            <div class="image-upload-action">
                <input type="file" id="post_image" name="post_image" accept="image/*" class="visually-hidden-file">
                <label for="post_image" class="btn btn-secondary upload-btn">Add Image</label>
            </div>
        </div>
        <div id="image-preview-wrap" class="image-preview-wrap">
            <img id="image-preview" class="image-preview" alt="Selected image preview">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Post</button>
            <a href="/" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const postImageInput = document.getElementById('post_image');
    const selectedImageName = document.getElementById('selected-image-name');
    const imagePreviewWrap = document.getElementById('image-preview-wrap');
    const imagePreview = document.getElementById('image-preview');

    postImageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            selectedImageName.textContent = 'No image selected';
            imagePreviewWrap.classList.remove('active');
            imagePreview.removeAttribute('src');
            return;
        }

        selectedImageName.textContent = 'Selected: ' + file.name;
        const reader = new FileReader();
        reader.onload = function (event) {
            imagePreview.src = event.target.result;
            imagePreviewWrap.classList.add('active');
        };
        reader.readAsDataURL(file);
    });
</script>

<?php include '../includes/footer.php'; ?>
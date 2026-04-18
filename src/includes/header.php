<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solo Forum | PATRIK SOSI HUI</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/" class="brand">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-text">
                    <strong>SoloForum</strong>
                </span>
            </a>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="/logout.php">Logout</a>
                <?php else: ?>
                    <a href="/login.php">Login</a>
                    <a href="/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
<?php

session_start();
require_once 'config/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="auth-layout">
    <aside class="auth-banner">
        <p class="auth-kicker">Welcome back</p>
        <h1>Log in and continue your conversations.</h1>
        <p>
            Access your profile, publish new threads, and reply to ongoing discussions in your forum community.
        </p>
        <ul>
            <li>Share your latest thoughts instantly</li>
            <li>Engage with comments from the community</li>
            <li>Keep your portfolio forum active every day</li>
        </ul>
    </aside>

    <div class="auth-panel">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" placeholder="Username" required>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="/register.php">Register</a></p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
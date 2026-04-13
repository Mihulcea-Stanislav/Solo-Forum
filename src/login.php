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

<h1>Login</h1>
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

<?php include 'includes/footer.php'; ?>
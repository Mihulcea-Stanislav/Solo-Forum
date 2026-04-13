<?php
session_start();
require_once 'config/db.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            if ($stmt->execute([$username, $email, $hashed])) {
                header('Location: login.php');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<h1>Register</h1>
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Username:</label>
    <input type="text" name="username" placeholder="Username" required>

    <label>Email:</label>
    <input type="email" name="email" placeholder="Email" required>

    <label>Password:</label>
    <input type="password" name="password" placeholder="Password" required>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login.php">Login here</a>.</p>

<?php include 'includes/footer.php'; ?>
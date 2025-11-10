<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: quiz.php');
    exit;
}

$usernameCookie = $_COOKIE['eduquest_username'] ?? '';
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Welcome</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header class="hero">
        <div class="hero__content">
            <h1>EduQuest</h1>
            <p class="tagline">Master your knowledge with interactive quizzes.</p>
        </div>
    </header>

    <main class="auth">
        <?php if (!empty($message)): ?>
            <div class="alert"><?= $message ?></div>
        <?php endif; ?>
        <section class="card">
            <h2>Login</h2>
            <form action="login.php" method="post" class="form">
                <label for="login-username">Username</label>
                <input type="text" id="login-username" name="username" value="<?= htmlspecialchars($usernameCookie) ?>" required>

                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" required>

                <button type="submit">Login</button>
            </form>
        </section>

        <section class="card">
            <h2>Create an account</h2>
            <form action="register.php" method="post" class="form">
                <label for="register-username">Username</label>
                <input type="text" id="register-username" name="username" required>

                <label for="register-email">Email</label>
                <input type="email" id="register-email" name="email" required>

                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="password" required>

                <button type="submit">Register</button>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Learn, compete, and grow.</p>
    </footer>
</body>
</html>
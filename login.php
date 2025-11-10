<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: index.php?message=Please+fill+in+all+fields');
    exit;
}

$stmt = $pdo->prepare('SELECT id, username, password_hash, is_admin FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    header('Location: index.php?message=Invalid+credentials');
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['is_admin'] = (bool) $user['is_admin'];
$_SESSION['LAST_ACTIVITY'] = time();

setcookie('eduquest_username', $user['username'], time() + (30 * 24 * 60 * 60), '/');

header('Location: quizzes.php');
exit;
?>
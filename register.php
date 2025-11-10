<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || $password === '') {
    header('Location: index.php?message=All+fields+are+required&showRegister=1');  
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?message=Please+enter+a+valid+email&showRegister=1');
    exit;
}

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
$checkStmt->execute(['username' => $username, 'email' => $email]);
if ($checkStmt->fetch()) {
    header('Location: index.php?message=Username+or+email+already+exists&showRegister=1');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$adminCheckStmt = $pdo->query('SELECT COUNT(*) FROM users WHERE is_admin = 1');
$isAdmin = $adminCheckStmt->fetchColumn() == 0;

$insertStmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, is_admin) VALUES (:username, :email, :password_hash, :is_admin)');
$insertStmt->execute([
    'username' => $username,
    'email' => $email,
    'password_hash' => $passwordHash,
    'is_admin' => $isAdmin ? 1 : 0,
]);

$message = $isAdmin
    ? 'Registration+successful!+Your+account+is+set+as+administrator.'
    : 'Registration+successful!+Please+log+in.';

header('Location: index.php?message=' . $message);
exit;
?>
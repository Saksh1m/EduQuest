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
    header('Location: index.php?message=All+fields+are+required');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?message=Please+enter+a+valid+email');
    exit;
}

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
$checkStmt->execute(['username' => $username, 'email' => $email]);
if ($checkStmt->fetch()) {
    header('Location: index.php?message=Username+or+email+already+exists');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertStmt = $pdo->prepare('INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)');
$insertStmt->execute([
    'username' => $username,
    'email' => $email,
    'password_hash' => $passwordHash,
]);

header('Location: index.php?message=Registration+successful!+Please+log+in.');
exit;
?>
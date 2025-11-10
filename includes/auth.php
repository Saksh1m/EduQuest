<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SESSION_TIMEOUT', 15 * 60); // 15 minutes

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?message=Please+log+in');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: timeout.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();
?>
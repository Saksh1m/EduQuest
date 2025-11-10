<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Session Timeout</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <main class="auth">
        <section class="card">
            <h1>Session Expired</h1>
            <p>Your session has timed out due to inactivity. Please log in again to continue.</p>
            <a class="button" href="index.php">Return to Login</a>
        </section>
    </main>
</body>
</html>
<?php
require_once 'includes/auth.php';
require_once 'config.php';

$lastScore = $_SESSION['last_score'] ?? null;

$recentStmt = $pdo->prepare('SELECT score, total_questions, created_at FROM quiz_attempts WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5');
$recentStmt->execute(['user_id' => $_SESSION['user_id']]);
$recentAttempts = $recentStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Results</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/scripts.js"></script>
</head>
<body>
    <nav class="top-nav">
        <span>Results for <?= htmlspecialchars($_SESSION['username']) ?></span>
        <div class="top-nav__actions">
            <a href="quiz.php">Take another quiz</a>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="results">
        <section class="card">
            <h1>Your Latest Score</h1>
            <?php if ($lastScore): ?>
                <p class="score">You scored <strong><?= $lastScore['score'] ?></strong> out of <strong><?= $lastScore['total'] ?></strong> on <?= htmlspecialchars($lastScore['time']) ?>.</p>
            <?php else: ?>
                <p class="muted">Complete a quiz to see your score.</p>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Recent Attempts</h2>
            <?php if (empty($recentAttempts)): ?>
                <p class="muted">No previous attempts recorded.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAttempts as $attempt): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('M j, Y g:i a', strtotime($attempt['created_at']))) ?></td>
                                <td><?= $attempt['score'] ?>/<?= $attempt['total_questions'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Knowledge is power.</p>
    </footer>
</body>
</html>
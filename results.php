<?php
require_once 'includes/auth.php';
require_once 'config.php';

$lastScore = $_SESSION['last_score'] ?? null;
$lastScoreTime = null;
if ($lastScore && isset($lastScore['time'])) {
    $lastScoreTime = DateTime::createFromFormat('Y-m-d H:i:s T', $lastScore['time'], new DateTimeZone('Asia/Kolkata'));
}

$recentStmt = $pdo->prepare('SELECT qa.score, qa.total_questions, qa.created_at, q.title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.id WHERE qa.user_id = :user_id ORDER BY qa.created_at DESC LIMIT 5');
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
            <a href="quizzes.php">Take another quiz</a>
            <a href="leaderboard.php">Leaderboard</a>
             <?php if (!empty($_SESSION['is_admin'])): ?>
                <a href="manage_quizzes.php">Manage quizzes</a>
            <?php endif; ?>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="results">
        <section class="card">
            <h1>Your Latest Score</h1>
            <?php if ($lastScore): ?>
                <?php $displayTime = $lastScoreTime ? $lastScoreTime->format('M j, Y g:i a T') : $lastScore['time']; ?>
                <p class="score">You scored <strong><?= $lastScore['score'] ?></strong> out of <strong><?= $lastScore['total'] ?></strong> on <strong><?= htmlspecialchars($lastScore['quiz_title'] ?? 'this quiz') ?></strong> at <?= htmlspecialchars($displayTime) ?>.</p>
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
                            <th>Quiz</th>
                            <th>Date</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentAttempts as $attempt): ?>
                            <?php
                                $attemptTime = new DateTime($attempt['created_at']);
                                $attemptTime->setTimezone(new DateTimeZone('Asia/Kolkata'));
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($attempt['title']) ?></td>
                                <td><?= htmlspecialchars($attemptTime->format('M j, Y g:i a T')) ?></td>
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
<?php
require_once 'includes/auth.php';
require_once 'config.php';

$query = <<<SQL
SELECT u.username, qa.score, qa.total_questions, qa.created_at
FROM users u
JOIN quiz_attempts qa ON qa.user_id = u.id
WHERE qa.id = (
    SELECT qa2.id
    FROM quiz_attempts qa2
    WHERE qa2.user_id = u.id
    ORDER BY qa2.score DESC, qa2.created_at ASC
    LIMIT 1
)
ORDER BY qa.score DESC, qa.created_at ASC
LIMIT 10;
SQL;

$leaderboardStmt = $pdo->query($query);
$leaders = $leaderboardStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Leaderboard</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/scripts.js"></script>
</head>
<body>
    <nav class="top-nav">
        <span>Leaderboard</span>
        <div class="top-nav__actions">
            <a href="quiz.php">Back to Quiz</a>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="leaderboard">
        <section class="card">
            <h1>Top Scorers</h1>
            <?php if (empty($leaders)): ?>
                <p class="muted">No attempts recorded yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Best Score</th>
                            <th>Last Played</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaders as $index => $player): ?>
                            <tr>
                                <td>#<?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($player['username']) ?></td>
                                <td><?= $player['score'] ?>/<?= $player['total_questions'] ?></td>
                                <td><?= htmlspecialchars(date('M j, Y g:i a', strtotime($player['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Rise to the top.</p>
    </footer>
</body>
</html>
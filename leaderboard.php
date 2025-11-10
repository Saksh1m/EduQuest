<?php
require_once 'includes/auth.php';
require_once 'config.php';

$quizStmt = $pdo->query('SELECT id, title FROM quizzes ORDER BY title ASC');
$quizzes = $quizStmt->fetchAll();

$selectedQuizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;

if (!empty($quizzes) && $selectedQuizId <= 0) {
    $selectedQuizId = (int) $quizzes[0]['id'];
}

$leaders = [];
if ($selectedQuizId > 0) {
    $query = <<<SQL
SELECT u.username, qa.score, qa.total_questions, qa.created_at
FROM users u
JOIN quiz_attempts qa ON qa.user_id = u.id
WHERE qa.quiz_id = :quiz_id
  AND qa.id = (
      SELECT qa2.id
      FROM quiz_attempts qa2
      WHERE qa2.user_id = u.id
        AND qa2.quiz_id = :quiz_id
      ORDER BY qa2.score DESC, qa2.created_at ASC
      LIMIT 1
)
ORDER BY qa.score DESC, qa.created_at ASC
LIMIT 10;
SQL;

$leaderboardStmt = $pdo->prepare($query);
    $leaderboardStmt->execute(['quiz_id' => $selectedQuizId]);
    $leaders = $leaderboardStmt->fetchAll();
}
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
            <a href="quizzes.php">Available quizzes</a>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <a href="manage_quizzes.php">Manage quizzes</a>
            <?php endif; ?>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="leaderboard">
        <section class="card">
            <h1>Top Scorers</h1>
            <?php if (empty($quizzes)): ?>
                <p class="muted">No quizzes are available yet.</p>
            <?php else: ?>
                <form method="get" class="form" style="max-width: 320px; margin-bottom: 1.5rem;">
                    <label for="quiz-filter">Select quiz</label>
                    <select id="quiz-filter" name="quiz_id" onchange="this.form.submit()">
                        <?php foreach ($quizzes as $quiz): ?>
                            <option value="<?= (int) $quiz['id'] ?>" <?= $quiz['id'] == $selectedQuizId ? 'selected' : '' ?>><?= htmlspecialchars($quiz['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if (empty($leaders)): ?>
                    <p class="muted">No attempts recorded for this quiz yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Player</th>
                                <th>Best Score</th>
                                <th>Last Played</th>
                            </thead>
                        <tbody>
                            <?php foreach ($leaders as $index => $player): ?>
                                <?php
                                    $playedAt = new DateTime($player['created_at']);
                                    $playedAt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                ?>
                                <tr>
                                    <td>#<?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($player['username']) ?></td>
                                    <td><?= $player['score'] ?>/<?= $player['total_questions'] ?></td>
                                    <td><?= htmlspecialchars($playedAt->format('M j, Y g:i a T')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Rise to the top.</p>
    </footer>
</body>
</html>
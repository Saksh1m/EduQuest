<?php
require_once 'includes/auth.php';
require_once 'config.php';

$questionCount = 5;

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

$stmt = $pdo->query('SELECT COUNT(*) as total FROM questions');
$totalQuestions = (int) $stmt->fetch()['total'];

if ($totalQuestions === 0) {
    $questions = [];
} else {
    $limit = min($questionCount, $totalQuestions);
    $randomStmt = $pdo->prepare('SELECT * FROM questions ORDER BY RAND() LIMIT :limit');
    $randomStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $randomStmt->execute();
    $questions = $randomStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Quiz</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/scripts.js"></script>
</head>
<body>
    <nav class="top-nav">
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <div class="top-nav__actions">
            <a href="leaderboard.php">Leaderboard</a>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="quiz">
        <section class="card">
            <h1>General Knowledge Quiz</h1>
            <p class="muted">Answer the questions below. Your session will expire after 15 minutes of inactivity.</p>
            <?php if ($message): ?>
                <div class="alert"><?= $message ?></div>
            <?php endif; ?>
            <?php if (empty($questions)): ?>
                <p class="alert">No questions available. Please seed the database.</p>
            <?php else: ?>
                <form action="submit_quiz.php" method="post" class="form">
                    <?php foreach ($questions as $index => $question): ?>
                        <article class="question">
                            <h2>Question <?= $index + 1 ?>:</h2>
                            <p><?= htmlspecialchars($question['prompt']) ?></p>
                            <?php foreach (['a', 'b', 'c', 'd'] as $option): ?>
                                <?php $field = 'option_' . $option; ?>
                                <label class="option">
                                    <input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= $option ?>" required>
                                    <span><?= htmlspecialchars($question[$field]) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </article>
                    <?php endforeach; ?>
                    <input type="hidden" name="question_ids" value="<?= htmlspecialchars(implode(',', array_column($questions, 'id'))) ?>">
                    <button type="submit">Submit Quiz</button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Keep challenging yourself!</p>
    </footer>
</body>
</html>
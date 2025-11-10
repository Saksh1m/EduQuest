<?php
require_once 'includes/auth.php';
require_once 'config.php';

$quizId = isset($_GET['quiz_id']) ? (int) $_GET['quiz_id'] : 0;
if ($quizId <= 0) {
    header('Location: quizzes.php?message=Please+select+a+quiz+to+begin');
    exit;
}

$quizStmt = $pdo->prepare('SELECT q.id, q.title, q.description FROM quizzes q WHERE q.id = :quiz_id');
$quizStmt->execute(['quiz_id' => $quizId]);
$quiz = $quizStmt->fetch();

if (!$quiz) {
    header('Location: quizzes.php?message=The+selected+quiz+could+not+be+found');
    exit;
}

$questionCount = 5;

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

$countStmt = $pdo->prepare('SELECT COUNT(*) as total FROM questions WHERE quiz_id = :quiz_id');
$countStmt->execute(['quiz_id' => $quizId]);
$totalQuestions = (int) $countStmt->fetch()['total'];

if ($totalQuestions === 0) {
    $questions = [];
} else {
    $limit = min($questionCount, $totalQuestions);
    $randomStmt = $pdo->prepare('SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY RAND() LIMIT :limit');
    $randomStmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
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
            <a href="quizzes.php">Available quizzes</a>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <a href="manage_quizzes.php">Manage quizzes</a>
            <?php endif; ?>
            <a href="leaderboard.php">Leaderboard</a>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="quiz">
        <section class="card">
            <h1><?= htmlspecialchars($quiz['title']) ?></h1>
            <?php if (!empty($quiz['description'])): ?>
                <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
            <?php endif; ?>
            <p class="muted">Answer the questions below. Your session will expire after 15 minutes of inactivity.</p>
            <?php if ($message): ?>
                <div class="alert"><?= $message ?></div>
            <?php endif; ?>
            <?php if (empty($questions)): ?>
                 <p class="alert">No questions are available for this quiz yet. Please check back later.</p>
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
                    <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">
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
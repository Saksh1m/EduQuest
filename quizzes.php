<?php
require_once 'includes/auth.php';
require_once 'config.php';

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

$quizStmt = $pdo->query(
    'SELECT q.id, q.title, q.description, q.created_at, u.username AS creator, COUNT(ques.id) AS question_count
     FROM quizzes q
     LEFT JOIN questions ques ON ques.quiz_id = q.id
     JOIN users u ON q.created_by = u.id
     GROUP BY q.id, q.title, q.description, q.created_at, u.username
     ORDER BY q.created_at DESC'
);
$quizzes = $quizStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Available Quizzes</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/scripts.js"></script>
</head>
<body>
    <nav class="top-nav">
        <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <div class="top-nav__actions">
            <a href="leaderboard.php">Leaderboard</a>
            <?php if (!empty($_SESSION['is_admin'])): ?>
                <a href="manage_quizzes.php">Manage quizzes</a>
            <?php endif; ?>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="quizzes">
        <section class="card">
            <h1>Choose a quiz to begin</h1>
            <p class="muted">Select from the available quizzes below. New quizzes appear here as soon as an administrator publishes them.</p>
        </section>

        <?php if ($message): ?>
            <div class="alert"><?= $message ?></div>
        <?php endif; ?>

        <?php if (empty($quizzes)): ?>
            <section class="card">
                <p class="muted">No quizzes are available yet. Please check back later.</p>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <p class="muted">As an administrator, you can create a new quiz from the <a href="manage_quizzes.php">quiz management page</a>.</p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <section class="quiz-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <article class="card">
                        <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                        <?php if (!empty($quiz['description'])): ?>
                            <p><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>
                        <?php endif; ?>
                        <?php
                            $createdAt = new DateTime($quiz['created_at']);
                            $createdAt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                        ?>
                        <div class="quiz-card__meta">
                            <span class="badge"><?= (int) $quiz['question_count'] ?> question<?= $quiz['question_count'] == 1 ? '' : 's' ?></span>
                            <span>Created by <?= htmlspecialchars($quiz['creator']) ?> on <?= htmlspecialchars($createdAt->format('M j, Y')) ?> IST</span>
                        </div>
                        <div class="quiz-card__actions">
                            <a class="button" href="quiz.php?quiz_id=<?= (int) $quiz['id'] ?>">Join quiz</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Keep exploring new challenges.</p>
    </footer>
</body>
</html>
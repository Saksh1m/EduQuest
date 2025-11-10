<?php
require_once 'includes/auth.php';
require_once 'config.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: quizzes.php?message=You+do+not+have+permission+to+access+that+page');
    exit;
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_quiz') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $errorMessage = 'Please provide a title for the quiz.';
        } else {
            $insertQuiz = $pdo->prepare('INSERT INTO quizzes (title, description, created_by) VALUES (:title, :description, :created_by)');
            $insertQuiz->execute([
                'title' => $title,
                'description' => $description,
                'created_by' => $_SESSION['user_id'],
            ]);
            $successMessage = sprintf('Quiz "%s" created successfully. You can now add questions to it.', $title);
        }
    } elseif ($action === 'add_question') {
        $quizId = (int) ($_POST['quiz_id'] ?? 0);
        $prompt = trim($_POST['prompt'] ?? '');
        $optionA = trim($_POST['option_a'] ?? '');
        $optionB = trim($_POST['option_b'] ?? '');
        $optionC = trim($_POST['option_c'] ?? '');
        $optionD = trim($_POST['option_d'] ?? '');
        $correctOption = $_POST['correct_option'] ?? '';

        if ($quizId <= 0 || $prompt === '' || $optionA === '' || $optionB === '' || $optionC === '' || $optionD === '' || !in_array($correctOption, ['a', 'b', 'c', 'd'], true)) {
            $errorMessage = 'Please complete all fields before adding a question.';
        } else {
            $quizExists = $pdo->prepare('SELECT id FROM quizzes WHERE id = :id');
            $quizExists->execute(['id' => $quizId]);
            if (!$quizExists->fetch()) {
                $errorMessage = 'The selected quiz could not be found.';
            } else {
                $insertQuestion = $pdo->prepare('INSERT INTO questions (quiz_id, prompt, option_a, option_b, option_c, option_d, correct_option) VALUES (:quiz_id, :prompt, :option_a, :option_b, :option_c, :option_d, :correct_option)');
                $insertQuestion->execute([
                    'quiz_id' => $quizId,
                    'prompt' => $prompt,
                    'option_a' => $optionA,
                    'option_b' => $optionB,
                    'option_c' => $optionC,
                    'option_d' => $optionD,
                    'correct_option' => $correctOption,
                ]);
                $successMessage = 'Question added successfully.';
            }
        }
    }
}

$quizzesStmt = $pdo->query('SELECT q.id, q.title, q.description, q.created_at, COUNT(ques.id) AS question_count FROM quizzes q LEFT JOIN questions ques ON ques.quiz_id = q.id GROUP BY q.id, q.title, q.description, q.created_at ORDER BY q.created_at DESC');
$quizzes = $quizzesStmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduQuest | Manage Quizzes</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/scripts.js"></script>
</head>
<body>
    <nav class="top-nav">
        <span>Quiz management</span>
        <div class="top-nav__actions">
            <a href="quizzes.php">Available quizzes</a>
            <a href="logout.php" class="danger">Logout</a>
        </div>
    </nav>

    <main class="quizzes">
        <?php if ($successMessage): ?>
            <div class="alert"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert" style="border-left-color: var(--danger); background: rgba(220, 38, 38, 0.12); color: var(--danger);">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <h1>Create a new quiz</h1>
            <form method="post" class="form">
                <input type="hidden" name="action" value="create_quiz">
                <label for="title">Quiz title</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"></textarea>

                <button type="submit">Create quiz</button>
            </form>
        </section>

        <section class="card">
            <h2>Add a question</h2>
            <?php if (empty($quizzes)): ?>
                <p class="muted">Create a quiz first to start adding questions.</p>
            <?php else: ?>
                <form method="post" class="form">
                    <input type="hidden" name="action" value="add_question">
                    <label for="quiz-id">Select quiz</label>
                    <select id="quiz-id" name="quiz_id" required>
                        <?php foreach ($quizzes as $quiz): ?>
                            <option value="<?= (int) $quiz['id'] ?>"><?= htmlspecialchars($quiz['title']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="prompt">Question</label>
                    <textarea id="prompt" name="prompt" rows="3" required></textarea>

                    <label for="option-a">Option A</label>
                    <input type="text" id="option-a" name="option_a" required>

                    <label for="option-b">Option B</label>
                    <input type="text" id="option-b" name="option_b" required>

                    <label for="option-c">Option C</label>
                    <input type="text" id="option-c" name="option_c" required>

                    <label for="option-d">Option D</label>
                    <input type="text" id="option-d" name="option_d" required>

                    <label for="correct-option">Correct option</label>
                    <select id="correct-option" name="correct_option" required>
                        <option value="">Select</option>
                        <option value="a">Option A</option>
                        <option value="b">Option B</option>
                        <option value="c">Option C</option>
                        <option value="d">Option D</option>
                    </select>

                    <button type="submit">Add question</button>
                </form>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Existing quizzes</h2>
            <?php if (empty($quizzes)): ?>
                <p class="muted">No quizzes have been created yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Questions</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $quiz): ?>
                            <?php
                                $createdAt = new DateTime($quiz['created_at']);
                                $createdAt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($quiz['title']) ?></td>
                                <td><?= (int) $quiz['question_count'] ?></td>
                                <td><?= htmlspecialchars($createdAt->format('M j, Y g:i a T')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?= date('Y') ?> EduQuest. Build engaging learning journeys.</p>
    </footer>
</body>
</html>
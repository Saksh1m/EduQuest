<?php
require_once 'includes/auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz.php');
    exit;
}

$quizId = isset($_POST['quiz_id']) ? (int) $_POST['quiz_id'] : 0;
$questionIds = array_filter(array_map('intval', explode(',', $_POST['question_ids'] ?? '')));
$answers = $_POST['answers'] ?? [];

if ($quizId <= 0 || empty($questionIds)) {
    header('Location: quizzes.php?message=Unable+to+submit+quiz.+Please+try+again.');
    exit;
}

$quizStmt = $pdo->prepare('SELECT id, title FROM quizzes WHERE id = :quiz_id');
$quizStmt->execute(['quiz_id' => $quizId]);
$quiz = $quizStmt->fetch();

if (!$quiz) {
    header('Location: quizzes.php?message=The+quiz+you+attempted+could+not+be+found');
    exit;
}

$placeholders = [];
$params = ['quiz_id' => $quizId];
foreach ($questionIds as $index => $id) {
    $key = ':id' . $index;
    $placeholders[] = $key;
    $params[$key] = $id;
}

$query = 'SELECT id, correct_option FROM questions WHERE quiz_id = :quiz_id AND id IN (' . implode(',', $placeholders) . ')';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$correctAnswers = $stmt->fetchAll();

if (count($correctAnswers) !== count($questionIds)) {
    header('Location: quizzes.php?message=Some+questions+could+not+be+validated.+Please+retry.');
    exit;
}

$score = 0;
foreach ($correctAnswers as $row) {
    $questionId = $row['id'];
    $correctOption = $row['correct_option'];
    $userAnswer = $answers[$questionId] ?? null;
    if ($userAnswer !== null && $userAnswer === $correctOption) {
        $score++;
    }
}

$totalQuestions = count($questionIds);

$insertStmt = $pdo->prepare('INSERT INTO quiz_attempts (user_id, quiz_id, score, total_questions) VALUES (:user_id, :quiz_id, :score, :total)');
$insertStmt->execute([
    'user_id' => $_SESSION['user_id'],
    'quiz_id' => $quizId,
    'score' => $score,
    'total' => $totalQuestions,
]);
$attemptTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));


$_SESSION['last_score'] = [
    'score' => $score,
    'total' => $totalQuestions,
    'quiz_id' => $quizId,
    'quiz_title' => $quiz['title'],
    'time' => $attemptTime->format('Y-m-d H:i:s T'),
];

header('Location: results.php?quiz_id=' . $quizId);
exit;
?>
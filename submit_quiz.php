<?php
require_once 'includes/auth.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz.php');
    exit;
}

$questionIds = array_filter(array_map('intval', explode(',', $_POST['question_ids'] ?? '')));
$answers = $_POST['answers'] ?? [];

if (empty($questionIds)) {
    header('Location: quiz.php?message=No+questions+submitted');
    exit;
}

$placeholders = implode(',', array_fill(0, count($questionIds), '?'));
$query = "SELECT id, correct_option FROM questions WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($query);
$stmt->execute($questionIds);
$correctAnswers = $stmt->fetchAll();

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

$insertStmt = $pdo->prepare('INSERT INTO quiz_attempts (user_id, score, total_questions) VALUES (:user_id, :score, :total)');
$insertStmt->execute([
    'user_id' => $_SESSION['user_id'],
    'score' => $score,
    'total' => $totalQuestions,
]);

$_SESSION['last_score'] = [
    'score' => $score,
    'total' => $totalQuestions,
    'time' => date('Y-m-d H:i:s'),
];

header('Location: results.php');
exit;
?>
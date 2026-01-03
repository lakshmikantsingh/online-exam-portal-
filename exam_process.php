<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$exam_id = $_SESSION['exam_id'];
$answers = $_POST['answers'];

$score = 0;
foreach ($answers as $qid => $selected) {
    $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT correct_option FROM questions WHERE id=$qid"));
    $is_correct = ($q['correct_option'] == $selected) ? 1 : 0;
    $score += $is_correct;
}

mysqli_query($conn, "INSERT INTO exam_attempts(user_id, exam_id, start_time, end_time, score)
VALUES($user_id, $exam_id, NOW(), NOW(), $score)");

$attempt_id = mysqli_insert_id($conn);

foreach ($answers as $qid => $selected) {
    $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT correct_option FROM questions WHERE id=$qid"));
    $is_correct = ($q['correct_option'] == $selected) ? 1 : 0;
    mysqli_query($conn, "INSERT INTO exam_answers(attempt_id, question_id, selected_option, is_correct)
                         VALUES($attempt_id, $qid, '$selected', $is_correct)");
}

unset($_SESSION['exam_id']);
unset($_SESSION['start_time']);
unset($_SESSION['duration']);

header("Location: result.php?attempt_id=$attempt_id");
exit;
?>

<?php
// result.php
session_start();
require_once 'includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : 0;
if ($exam_id <= 0) {
    die('Invalid exam.');
}
$stmt = $conn->prepare("SELECT e.*, s.name as subject_name, u.name as student_name FROM exams e LEFT JOIN subjects s ON e.subject_id=s.id LEFT JOIN users u ON e.student_id=u.id WHERE e.id = ?");
$stmt->bind_param('i', $exam_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) die('Exam not found.');
$exam = $res->fetch_assoc();

// fetch answers with questions
$answers = $conn->query("SELECT a.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option
                         FROM answers a JOIN questions q ON a.question_id = q.id
                         WHERE a.exam_id = $exam_id");
include 'includes/header.php';
?>
<div class="card p-3">
  <h4>Result for <?php echo htmlspecialchars($exam['student_name']); ?></h4>
  <p>Subject: <?php echo htmlspecialchars($exam['subject_name']); ?></p>
  <p>Score: <strong><?php echo $exam['score']; ?></strong> / <?php echo $exam['total_questions']; ?></p>
  <p>Started: <?php echo $exam['start_time']; ?> | Ended: <?php echo $exam['end_time']; ?></p>
  <hr>
  <h5>Answers</h5>
  <?php while ($a = $answers->fetch_assoc()): ?>
    <div class="card p-2 mb-2">
      <p><strong>Q:</strong> <?php echo htmlspecialchars($a['question_text']); ?></p>
      <p><em>Your answer:</em> <?php echo $a['selected_option'] ? strtoupper($a['selected_option']) : '<i>Not answered</i>'; ?>
        <?php if ($a['is_correct']): ?>
          <span class="badge bg-success">Correct</span>
        <?php else: ?>
          <span class="badge bg-danger">Wrong</span>
        <?php endif; ?>
      </p>
      <p><em>Correct:</em> <?php echo strtoupper($a['correct_option']); ?></p>
    </div>
  <?php endwhile; ?>
  <a class="btn btn-primary" href="<?php echo ($_SESSION['role']==='admin')? 'admin/view_results.php' : 'student/my_results.php'; ?>">Back</a>
</div>
<?php include 'includes/footer.php'; ?>

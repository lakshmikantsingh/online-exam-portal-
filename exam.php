<?php
// exam.php - displays questions and handles submission
session_start();
require_once 'includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Starting an exam
    $subject_id = isset($_GET['subject_id']) ? (int)$_GET['subject_id'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $duration_min = isset($_GET['duration']) ? (int)$_GET['duration'] : 10;
    if ($subject_id <= 0) {
        die('Invalid subject.');
    }
    // fetch random questions
    $stmt = $conn->prepare("SELECT * FROM questions WHERE subject_id = ? ORDER BY RAND() LIMIT ?");
    $stmt->bind_param('ii', $subject_id, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $questions = $res->fetch_all(MYSQLI_ASSOC);
    if (count($questions) === 0) {
        die('No questions for this subject.');
    }
    // pass questions to form (we'll store question IDs)
    include 'includes/header.php';
    ?>
    <div class="card p-3">
      <h4>Exam - <?php
        $sname = $conn->query("SELECT name FROM subjects WHERE id = $subject_id")->fetch_assoc()['name'] ?? 'Subject';
        echo htmlspecialchars($sname);
      ?></h4>
      <div class="mb-2">Time Left: <span id="timer" class="timer"></span></div>
      <form id="examForm" method="post" action="exam.php">
        <input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">
        <input type="hidden" name="duration" id="duration" value="<?php echo $duration_min*60; ?>">
        <input type="hidden" name="started_at" value="<?php echo date('Y-m-d H:i:s'); ?>">
        <input type="hidden" name="limit" value="<?php echo $limit; ?>">
        <?php foreach ($questions as $i => $q): ?>
          <div class="mb-3 card p-3">
            <p><strong>Q<?php echo ($i+1); ?>:</strong> <?php echo htmlspecialchars($q['question_text']); ?></p>
            <input type="hidden" name="qids[]" value="<?php echo $q['id']; ?>">
            <?php
            $opts = ['a'=>'option_a','b'=>'option_b','c'=>'option_c','d'=>'option_d'];
            foreach ($opts as $key => $field):
              $val = $q[$field];
              if (trim($val) === '') continue;
            ?>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="answer[<?php echo $q['id']; ?>]" value="<?php echo $key; ?>" id="q<?php echo $q['id'] . $key; ?>">
                <label class="form-check-label" for="q<?php echo $q['id'] . $key; ?>">
                  <?php echo htmlspecialchars($val); ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
        <button class="btn btn-primary" type="submit" name="submit_exam">Submit Exam</button>
      </form>
    </div>
    <script>
      // start timer and auto submit
      window.onload = function(){
        autoSubmitOnExpire('examForm');
      };
    </script>
    <?php
    include 'includes/footer.php';
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle submission
    if (!isset($_POST['qids']) || !is_array($_POST['qids'])) {
        die('Invalid submission.');
    }
    $subject_id = (int)$_POST['subject_id'];
    $limit = (int)($_POST['limit'] ?? count($_POST['qids']));
    $started_at = $_POST['started_at'] ?? date('Y-m-d H:i:s');
    $end_time = date('Y-m-d H:i:s');

    // calculate score
    $answers = $_POST['answer'] ?? []; // [question_id => 'a']
    $qids = $_POST['qids'];

    $score = 0;
    $total = count($qids);

    // create exam record
    $stmt = $conn->prepare("INSERT INTO exams (subject_id, student_id, total_questions, score, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiiss', $subject_id, $user_id, $total, $score, $started_at, $end_time);
    $stmt->execute();
    $exam_id = $stmt->insert_id;

    // evaluate each question, insert into answers table
    $insertAns = $conn->prepare("INSERT INTO answers (exam_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
    foreach ($qids as $qid) {
        $qid = (int)$qid;
        $sel = isset($answers[$qid]) ? $conn->real_escape_string($answers[$qid]) : null;
        // fetch correct option
        $res = $conn->query("SELECT correct_option FROM questions WHERE id = $qid");
        $correct = $res->fetch_assoc()['correct_option'] ?? null;
        $is_correct = ($sel !== null && $correct !== null && $sel === $correct) ? 1 : 0;
        if ($is_correct) $score++;
        $insertAns->bind_param('iisi', $exam_id, $qid, $sel, $is_correct);
        $insertAns->execute();
    }
    // update exam score
    $upd = $conn->prepare("UPDATE exams SET score = ? WHERE id = ?");
    $upd->bind_param('ii', $score, $exam_id);
    $upd->execute();

    header("Location: result.php?exam_id=$exam_id");
    exit;
}

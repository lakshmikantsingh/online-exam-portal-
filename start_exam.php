<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

$exam_id = $_GET['exam_id'];
$exam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM exams WHERE id=$exam_id"));
$subject_id = $exam['subject_id'];
// Load all questions for the exam's subject so student can attempt them together
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE subject_id=$subject_id ORDER BY RAND()");

// count total questions loaded (useful if you want to show totals or save them to session)
$total_questions = mysqli_num_rows($questions);

$_SESSION['exam_id'] = $exam_id;
$_SESSION['start_time'] = time();
$_SESSION['duration'] = $exam['duration_minutes'] * 60;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($exam['exam_name']) ?> - Start Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    let duration = <?= $exam['duration_minutes'] * 60 ?>;
    function startTimer() {
        const timer = setInterval(() => {
            let minutes = Math.floor(duration / 60);
            let seconds = duration % 60;
            document.getElementById("timer").textContent = `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
            duration--;
            if (duration < 0) {
                clearInterval(timer);
                document.getElementById("examForm").submit();
            }
        }, 1000);
    }
    </script>
</head>
<body onload="startTimer()">
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between">
        <h3><?= htmlspecialchars($exam['exam_name']) ?> <small class="text-muted">(<?= $total_questions ?> Questions)</small></h3>
        <div><strong>Time Left:</strong> <span id="timer" class="text-danger fw-bold"></span></div>
    </div>
    <form id="examForm" action="exam_process.php" method="POST" class="mt-4">
        <?php $q_no = 1; $q_index = 0; while($q = mysqli_fetch_assoc($questions)): ?>
            <div class="card mb-3 question-item" data-qid="<?= $q['id'] ?>" data-index="<?= $q_index ?>" style="display: block;">
                <div class="card-body">
                    <p><strong>Q<?= $q_no++ ?>.</strong> <?= htmlspecialchars($q['question_text']) ?></p>
                    <?php foreach(['a','b','c','d'] as $opt): ?>
                        <div class="form-check">
                            <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt ?>" class="form-check-input">
                            <label class="form-check-label"><?= htmlspecialchars($q['option_'.$opt]) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php $q_index++; endwhile; ?>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" id="submitBtn" class="btn btn-success">Submit Exam</button>
        </div>
    </form>
</div>
</body>
<script>
// Simple submit validation when all questions are visible
document.addEventListener('DOMContentLoaded', function () {
    const examForm = document.getElementById('examForm');
    const questions = Array.from(document.querySelectorAll('.question-item'));

    examForm.addEventListener('submit', function (e) {
        if (window.autoSubmitting) return true; // timer auto-submit bypasses prompts

        const unanswered = [];
        for (let i = 0; i < questions.length; i++) {
            const qid = questions[i].dataset.qid;
            const sel = document.querySelector(`input[name="answers[${qid}]":checked`);
            if (!sel) unanswered.push(i);
        }

        if (unanswered.length > 0) {
            e.preventDefault();
            const first = unanswered[0];
            const firstQuestion = questions[first];
            const firstRadio = firstQuestion.querySelector('input[type=radio]');
            if (firstRadio) firstRadio.focus();
            if (confirm('There are unanswered questions. Submit anyway?')) {
                window.autoSubmitting = true;
                examForm.submit();
            }
            return false;
        }

        return true;
    });
});
</script>
</html>

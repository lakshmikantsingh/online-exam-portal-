<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : null;

$res = null;
$qa_rows = [];
$totals = ['total' => 0, 'correct' => 0, 'wrong' => 0];

if ($attempt_id) {
    $sql = "SELECT ea.*, e.id AS exam_real_id, e.exam_name, e.subject_id FROM exam_attempts ea LEFT JOIN exams e ON ea.exam_id = e.id WHERE ea.id=$attempt_id AND ea.user_id=$user_id";
    $res = mysqli_fetch_assoc(mysqli_query($conn, $sql));

    if ($res) {
        // fetch answers and related question data
        $ans_q = mysqli_query($conn, "SELECT a.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option
                                      FROM exam_answers a
                                      JOIN questions q ON a.question_id = q.id
                                      WHERE a.attempt_id=$attempt_id ORDER BY a.id ASC");
        while ($row = mysqli_fetch_assoc($ans_q)) {
            $qa_rows[] = $row;
            $totals['total']++;
            if ((int)$row['is_correct'] === 1) $totals['correct']++;
            else $totals['wrong']++;
        }
    }
        // determine actual total questions for this exam's subject (all subject questions shown)
        if (!empty($res['subject_id'])) {
            $c = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM questions WHERE subject_id=" . (int)$res['subject_id']));
            $totals['total'] = (int)$c['cnt'];
        }
}

$history = mysqli_query($conn, "
    SELECT e.exam_name, ea.score, ea.start_time, ea.end_time
    FROM exam_attempts ea
    LEFT JOIN exams e ON ea.exam_id = e.id
    WHERE ea.user_id=$user_id
    ORDER BY ea.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>My Results</h2>

    <?php if ($attempt_id && $res): ?>
        <div class="mb-3">
            <h4><?= htmlspecialchars($res['exam_name'] ?? 'Deleted Exam') ?></h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card p-2 mb-2">
                        <strong>Score:</strong> <?= (int)$res['score'] ?> / <?= (int)$totals['total'] ?>
                    </div>
                </div>
                <div class="col-md-8">
                        <div class="d-flex gap-2">
                            <?php $missed = max(0, (int)$totals['total'] - ((int)$totals['correct'] + (int)$totals['wrong'])); ?>
                            <div class="badge bg-success p-2">Correct: <?= (int)$totals['correct'] ?></div>
                            <div class="badge bg-danger p-2">Wrong: <?= (int)$totals['wrong'] ?></div>
                            <div class="badge bg-warning text-dark p-2">Missed: <?= $missed ?></div>
                            <div class="badge bg-secondary p-2">Total: <?= (int)$totals['total'] ?></div>
                        </div>
                    <div class="mt-2 small text-muted">Start: <?= htmlspecialchars($res['start_time']) ?> | End: <?= htmlspecialchars($res['end_time']) ?></div>
                </div>
            </div>
        </div>

        <h5 class="mt-3">Question Breakdown</h5>
        <?php if (count($qa_rows) === 0): ?>
            <div class="alert alert-warning">No question details available for this attempt.</div>
        <?php else: ?>
            <?php foreach ($qa_rows as $i => $q): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong>Q<?= $i+1 ?>.</strong> <?= htmlspecialchars($q['question_text']) ?></p>

                        <?php
                        $opts = ['a' => 'option_a', 'b' => 'option_b', 'c' => 'option_c', 'd' => 'option_d'];
                        $selected = $q['selected_option'];
                        $correct = $q['correct_option'];
                        foreach ($opts as $key => $field):
                            $text = $q[$field];
                            if (trim($text) === '') continue;
                            $isCorrectOpt = ($key === $correct);
                            $isSelected = ($key === $selected);
                            $classes = 'd-flex align-items-start mb-1';
                            $badge = '';
                            if ($isCorrectOpt) {
                                $classes .= ' border border-success rounded p-2';
                                $badge = '<span class="badge bg-success ms-2">Correct Answer</span>';
                            }
                            if ($isSelected && !$isCorrectOpt) {
                                $classes .= ' border border-danger rounded p-2';
                                $badge .= ' <span class="badge bg-danger ms-2">Your Answer</span>';
                            } elseif ($isSelected && $isCorrectOpt) {
                                $badge .= ' <span class="badge bg-success ms-2">Your Answer</span>';
                            }
                        ?>
                            <div class="<?= $classes ?>">
                                <div style="width:24px;"><strong><?= strtoupper($key) ?>.</strong></div>
                                <div class="ms-2 flex-fill"><?= htmlspecialchars($text) ?> <?= $badge ?></div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (is_null($selected) || $selected === ''): ?>
                            <div class="text-warning mt-2"><em>You did not answer this question.</em></div>
                        <?php endif; ?>

                        <div class="mt-2">
                            <?php if ((int)$q['is_correct'] === 1): ?>
                                <span class="badge bg-success">Correct</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Wrong</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!$attempt_id): ?>
        <h4 class="mt-4">Previous Attempts</h4>
        <table class="table table-bordered">
            <tr>
                <th>Exam</th>
                <th>Score</th>
                <th>Start</th>
                <th>End</th>
            </tr>
            <?php while($r = mysqli_fetch_assoc($history)): ?>
            <tr>
                <td><?= htmlspecialchars($r['exam_name'] ?? 'Deleted Exam') ?></td>
                <td><?= $r['score'] ?></td>
                <td><?= $r['start_time'] ?></td>
                <td><?= $r['end_time'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

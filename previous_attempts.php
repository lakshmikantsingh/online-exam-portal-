<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$attempts = mysqli_query($conn, "SELECT ea.id AS attempt_id, e.exam_name, ea.score, ea.start_time, ea.end_time, s.name AS subject_name
                                FROM exam_attempts ea
                                LEFT JOIN exams e ON ea.exam_id = e.id
                                LEFT JOIN subjects s ON e.subject_id = s.id
                                WHERE ea.user_id = $user_id
                                ORDER BY ea.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Previous Attempts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f8f9fa;}</style>
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Previous Attempts</h2>
    <p class="text-muted">Click an attempt to view detailed results.</p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Exam</th>
                <th>Subject</th>
                <th>Score</th>
                <th>Start</th>
                <th>End</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; while($r = mysqli_fetch_assoc($attempts)): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($r['exam_name']) ?></td>
                <td><?= htmlspecialchars($r['subject_name'] ?? '') ?></td>
                <td><?= (int)$r['score'] ?></td>
                <td><?= $r['start_time'] ?></td>
                <td><?= $r['end_time'] ?></td>
                <td><a class="btn btn-sm btn-primary" href="result.php?attempt_id=<?= $r['attempt_id'] ?>">View</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

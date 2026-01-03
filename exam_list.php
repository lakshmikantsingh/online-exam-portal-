<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

// Fetch exams with subject name
$exams = mysqli_query($conn, "
    SELECT e.*, s.name AS subject_name
    FROM exams e
    LEFT JOIN subjects s ON e.subject_id = s.id
    ORDER BY e.id DESC
");

// If query failed, capture error for display (development only)
$query_error = '';
if ($exams === false) {
    $query_error = mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Exams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Available Exams</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Exam Name</th>
            <th>Subject</th>
            <th>Total Questions</th>
            <th>Duration</th>
            <th>Action</th>
        </tr>
        <?php if ($query_error): ?>
        <tr>
            <td colspan="6" class="text-danger">Query error: <?= htmlspecialchars($query_error) ?></td>
        </tr>
        <?php elseif ($exams && mysqli_num_rows($exams) > 0): ?>
            <?php while($e = mysqli_fetch_assoc($exams)): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['exam_name']) ?></td>
                <td><?= htmlspecialchars($e['subject_name'] ?? 'Unknown') ?></td>
                <td><?= $e['total_questions'] ?></td>
                <td><?= $e['duration_minutes'] ?> min</td>
                <td><a href="start_exam.php?exam_id=<?= $e['id'] ?>" class="btn btn-primary btn-sm">Start Exam</a></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
        <tr>
            <td colspan="6">No exams available at the moment.</td>
        </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

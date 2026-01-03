<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$results = mysqli_query($conn, "
    SELECT ea.id, u.name, e.exam_name, ea.score, ea.start_time, ea.end_time
    FROM exam_attempts ea
    JOIN users u ON ea.user_id = u.id
    LEFT JOIN exams e ON ea.exam_id = e.id
    ORDER BY ea.id DESC
");

$results_rows = [];
if ($results) {
    while ($row = mysqli_fetch_assoc($results)) {
        $results_rows[] = $row;
    }
} else {
    $query_error = mysqli_error($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Exam Results</h2>
    <div class="row mb-4">
        <div class="col-12">
            <?php if (!empty($query_error)): ?>
                <div class="alert alert-danger">Query error: <?= htmlspecialchars($query_error) ?></div>
            <?php endif; ?>

            <div class="mt-4">
                <table class="table table-bordered" id="resultsTable">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Exam</th>
                        <th>Score</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                    <?php if (empty($results_rows)): ?>
                        <tr><td colspan="6" class="text-center">No results available.</td></tr>
                    <?php else: ?>
                        <?php foreach ($results_rows as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td><?= htmlspecialchars($r['exam_name'] ?? 'Deleted Exam') ?></td>
                            <td><?= $r['score'] ?></td>
                            <td><?= $r['start_time'] ?></td>
                            <td><?= $r['end_time'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>

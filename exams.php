<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";
$subjects = mysqli_query($conn, "SELECT * FROM subjects");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Only handle creation when all create fields are present
    if (isset($_POST['subject_id'], $_POST['exam_name'], $_POST['total_questions'], $_POST['duration'])) {
        $subject_id = (int)$_POST['subject_id'];
        $exam_name = mysqli_real_escape_string($conn, $_POST['exam_name']);
        $total_questions = (int)$_POST['total_questions'];
        $duration = (int)$_POST['duration'];

        $sql = "INSERT INTO exams(subject_id, exam_name, total_questions, duration_minutes)
                VALUES($subject_id, '$exam_name', $total_questions, $duration)";
        if (mysqli_query($conn, $sql)) {
            $msg = "Exam created successfully!";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle exam deletion — only remove the exam row, preserve attempts/answers
if (isset($_POST['delete_exam_id'])) {
    $del_id = (int)$_POST['delete_exam_id'];

    // Only delete the exam entry itself. Leave `exam_attempts` and `exam_answers`
    // intact so previous attempts remain available for reporting.
    if (mysqli_query($conn, "DELETE FROM exams WHERE id=$del_id")) {
        $msg = "Exam deleted. Student attempts are preserved.";
    } else {
        $msg = "Error deleting exam: " . mysqli_error($conn);
    }
}

$exams = mysqli_query($conn, "SELECT e.*, s.name AS subject_name FROM exams e JOIN subjects s ON e.subject_id=s.id ORDER BY e.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Exams</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Manage Exams</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" class="border p-3 mb-4">
        <div class="row mb-2">
            <div class="col-md-4">
                <select name="subject_id" class="form-select" required>
                    <option value="">Select Subject</option>
                    <?php while($s = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="exam_name" class="form-control" placeholder="Exam Name" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="total_questions" class="form-control" placeholder="Total Qs" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="duration" class="form-control" placeholder="Duration (min)" required>
            </div>
        </div>
        <button type="submit" class="btn btn-outline-success"><b>Create Exam</b></button>
    </form>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Exam Name</th>
            <th>Subject</th>
            <th>Total Qs</th>
            <th>Duration</th>
            <th>Actions</th>
        </tr>
        <?php while($e = mysqli_fetch_assoc($exams)): ?>
        <tr>
            <td><?= $e['id'] ?></td>
            <td><?= $e['exam_name'] ?></td>
            <td><?= $e['subject_name'] ?></td>
            <td><?= $e['total_questions'] ?></td>
            <td><?= $e['duration_minutes'] ?> min</td>
            <td>
                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this exam? Student attempts will be preserved.');">
                    <input type="hidden" name="delete_exam_id" value="<?= $e['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

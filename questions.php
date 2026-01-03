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
    $subject_id = $_POST['subject_id'];
    $question = $_POST['question'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $correct = $_POST['correct'];

    $sql = "INSERT INTO questions(subject_id, question_text, option_a, option_b, option_c, option_d, correct_option)
            VALUES('$subject_id', '$question', '$a', '$b', '$c', '$d', '$correct')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Question added!";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}

$questions = mysqli_query($conn, "SELECT q.*, s.name AS subject_name FROM questions q JOIN subjects s ON q.subject_id=s.id ORDER BY q.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Manage Questions</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" class="border p-3 mb-4">
        <div class="row mb-2">
            <div class="col-md-4">
                <select name="subject_id" class="form-select" required>
                    <option value="">Select Subject</option>
                    <?php while($sub = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?= $sub['id'] ?>"><?= $sub['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <textarea name="question" class="form-control mb-2" placeholder="Question Text" required></textarea>
        <input type="text" name="a" class="form-control mb-2" placeholder="Option A" required>
        <input type="text" name="b" class="form-control mb-2" placeholder="Option B" required>
        <input type="text" name="c" class="form-control mb-2" placeholder="Option C" required>
        <input type="text" name="d" class="form-control mb-2" placeholder="Option D" required>
        <input type="text" name="correct" class="form-control mb-2" placeholder="Correct Option (a/b/c/d)" required>
        <button type="submit" class="btn btn-outline-success"><b>Add Question</b></button>
    </form>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Question</th>
            <th>Correct</th>
        </tr>
        <?php while($q = mysqli_fetch_assoc($questions)): ?>
        <tr>
            <td><?= $q['id'] ?></td>
            <td><?= $q['subject_name'] ?></td>
            <td><?= htmlspecialchars($q['question_text']) ?></td>
            <td><?= strtoupper($q['correct_option']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

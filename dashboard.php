<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Online Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Admin Dashboard</h2>
    <p class="lead">Welcome, Admin! Use the menu to manage subjects, questions, exams, and view results.</p>

    <div class="row mt-4">
        <div class="col-md-3">
            <a href="subjects.php" class="btn btn-outline-primary w-100">Manage Subjects</a>
        </div>
        <div class="col-md-3">
            <a href="questions.php" class="btn btn-outline-dark w-100">Manage Questions</a>
        </div>
        <div class="col-md-3">
            <a href="exams.php" class="btn btn-outline-success w-100">Manage Exams</a>
        </div>
        <div class="col-md-3">
            <a href="results.php" class="btn btn-outline-info w-100">View Results</a>
        </div>
    </div>
</div>
</body>
</html>

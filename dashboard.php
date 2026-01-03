<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h2>
    <p class="lead">You can view available exams, take them, and check your results here.</p>

    <div class="mt-4">
        <a href="exam_list.php" class="btn btn-outline-primary me-2">View Available Exams</a>
        <a href="result.php" class="btn btn-outline-success">View My Results</a>
    </div>
</div>
</body>
</html>

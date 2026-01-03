<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: student/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title style="font-size: large; color: aquamarine;">Online Exam Portal></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Page background with dark overlay for readability */
        body.page-with-bg {
            min-height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.35)), url('images/exam.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #ececf3ff;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 200;
            font-size: 45px;
        }

        /* Center content vertically on larger screens */
        .hero {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 1.5rem;
        }

        /* Make buttons slightly more visible on the dark background */
        .hero .btn { min-width: 140px; }
    </style>
</head>
<body class="page-with-bg">
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container text-center mt-5 hero">
    <h1 class="mb-4" style="font-size:83px">Welcome to Online Exam Portal</h1>
    <div class="d-flex gap-2">
        <a href="login.php" class="btn btn-outline-light" style="color:blue;">Login</a>
        <a href="register.php" class="btn btn-outline-light" style="color:blue;">Register</a>
    </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

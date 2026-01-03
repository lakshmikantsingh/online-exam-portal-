<?php
include("config/db.php");
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Email already exists.";
    } else {
        $sql = "INSERT INTO users(name, email, password, role) VALUES('$name', '$email', '$pass', 'student')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Registration successful! You can login now.";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Online Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body.page-with-bg {
            min-height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.35)), url('images/exam.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        .hero {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .hero .btn { min-width: 140px; }
        .card-transparent { background: rgba(255,255,255,0.06); border: none; }
        .mb-3{
            font-size: 20px;
            color: white;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .text-center {
            font-family: 'Courier New', Courier, monospace;
            color: white;
            font-size: 46px;       
        }

    </style>
</head>
<body class="page-with-bg">
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container text-center mt-5 hero">
    <div class="col-md-4 offset-md-4 card card-transparent p-4">
        <h3 class="text-center">Registration</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
           <button type="submit" class="btn btn-success">Register</button>
        </form>
        <?php if($msg): ?>
        <div class="alert alert-info mt-3"><?= $msg ?></div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="login.php" style="font-size: 25px; color: black;">Already have an account?</a>
        </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

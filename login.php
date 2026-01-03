<?php
session_start();
include("config/db.php");

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // remember checkbox value (set later on successful auth)
    $remember_post = isset($_POST['remember']) ? true : false;

    if (empty($email) || empty($password)) {
        $msg = "Please provide email and password.";
    } else {
        // Try to find user by email
        $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) == 1) {
            $row = mysqli_fetch_assoc($res);

            $authenticated = false;

            // 1) If stored password looks like a bcrypt hash, try password_verify (backwards compatible)
            if (!empty($row['password']) && password_verify($password, $row['password'])) {
                $authenticated = true;
            }

            // 2) Fallback: check SHA2(password) match in DB (legacy / alternative method)
            if (!$authenticated) {
                $shaQuery = "SELECT * FROM users WHERE email='$email' AND password=SHA2('" . mysqli_real_escape_string($conn, $password) . "',256) LIMIT 1";
                $shaRes = mysqli_query($conn, $shaQuery);
                if ($shaRes && mysqli_num_rows($shaRes) == 1) {
                    $row = mysqli_fetch_assoc($shaRes); // replace with row from sha match
                    $authenticated = true;
                }
            }

            if ($authenticated) {
                // Set session values used across the app
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = isset($row['name']) ? $row['name'] : null;

                // If user chose to remember email, set a cookie (30 days). Otherwise clear cookie.
                if (!empty($remember_post)) {
                    setcookie('remember_email', $email, time() + 30*24*60*60, '/online_exam/');
                } else {
                    setcookie('remember_email', '', time() - 3600, '/online_exam/');
                }

                if ($row['role'] == 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: student/dashboard.php");
                }
                exit;
            } else {
                $msg = "Invalid password.";
            }
        } else {
            $msg = "No account found with that email.";
        }
    }
}
// Prefill email from cookie if present
$prefill_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Online Exam</title>
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
            font-weight: lighter;
        }
    </style>
</head>
<body class="page-with-bg">
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container text-center mt-5 hero">
    <div class="col-md-4 offset-md-4 card card-transparent p-4">
        <h3 class="text-center">Login</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars(
                    isset(
                        $prefill_email
                    ) ? $prefill_email : ''
                ) ?>" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input" <?php if(!empty(
                    $prefill_email
                )) echo 'checked'; ?>>
                <label class="form-check-label" for="remember">Remember my email</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <?php if($msg): ?>
        <div class="alert alert-danger mt-3"><?= $msg ?></div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="register.php" style="font-size: 25px; color: black">Create new account</a>
        </div>
    </div>
    </div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

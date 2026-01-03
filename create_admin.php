<?php
// Local-only admin creation helper.
// USAGE (local only): http://localhost/online_exam/tools/create_admin.php
// This script only accepts requests from localhost and will insert an admin user into the `users` table.
// After use: DELETE this file.

// Only allow localhost
$allowed = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
if (!in_array($remote, $allowed)) {
    http_response_code(403);
    echo "Access denied. This script can only be run from the local machine.";
    exit;
}

require __DIR__ . '/../config/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : 'Site Admin';

    if (empty($email) || empty($password)) {
        $msg = 'Email and password are required.';
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        // Check existence
        $res = mysqli_query($conn, "SELECT id FROM users WHERE email='$email_safe' LIMIT 1");
        if ($res && mysqli_num_rows($res) > 0) {
            $msg = 'A user with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $name_safe = mysqli_real_escape_string($conn, $name);
            $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name_safe', '$email_safe', '$hash', 'admin')";
            if (mysqli_query($conn, $sql)) {
                $msg = 'Admin user created successfully. Please delete this file (tools/create_admin.php) for security.';
            } else {
                $msg = 'DB error: ' . mysqli_error($conn);
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create Admin - Local Only</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{padding:30px;background:#f8f9fa}</style>
</head>
<body>
<div class="container">
    <h3>Create admin user (local only)</h3>
    <?php if($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Full name</label>
            <input type="text" name="name" class="form-control" value="Site Admin">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary">Create Admin</button>
    </form>
    <hr>
    <p class="small text-muted">This tool is for local development only. Delete the file after use.</p>
</div>
</body>
</html>

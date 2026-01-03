<?php
// Local-only migration: add 'description' column to subjects table
// Usage: open http://localhost/online_exam/tools/add_description.php in your browser (local machine only)

$allowed = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
if (!in_array($remote, $allowed)) {
    http_response_code(403);
    echo "Access denied. Run this on the local machine only.";
    exit;
}

require __DIR__ . '/../config/db.php';

$status = '';
$check = mysqli_query($conn, "SHOW COLUMNS FROM subjects LIKE 'description'");
if ($check && mysqli_num_rows($check) > 0) {
    $status = "Column 'description' already exists.";
} else {
    $alter = "ALTER TABLE subjects ADD COLUMN description TEXT NULL AFTER name";
    if (mysqli_query($conn, $alter)) {
        $status = "Column 'description' added successfully.";
    } else {
        $status = "Error adding column: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add description column</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <div class="container">
    <h3>Add 'description' column to subjects (local only)</h3>
    <div class="alert alert-info mt-3"><?= htmlspecialchars($status) ?></div>
    <p class="small text-muted">If success, reload <a href="/online_exam/admin/subjects.php">Manage Subjects</a>.</p>
  </div>
</body>
</html>

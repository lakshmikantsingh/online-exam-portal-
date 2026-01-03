<?php
session_start();
include("../config/db.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";
// Detect whether the subjects table has a 'description' column
$hasDescription = false;
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM subjects LIKE 'description'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) {
    $hasDescription = true;
}

// If description column missing, and running on localhost, offer to create it automatically (dev-only)
if (!$hasDescription) {
    $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $allowed = ['127.0.0.1', '::1', '::ffff:127.0.0.1'];
    if (in_array($remote, $allowed)) {
        // Try to add the column safely
        $alter = "ALTER TABLE subjects ADD COLUMN description TEXT NULL AFTER name";
        @mysqli_query($conn, $alter);
        // Re-check
        $colCheck2 = mysqli_query($conn, "SHOW COLUMNS FROM subjects LIKE 'description'");
        if ($colCheck2 && mysqli_num_rows($colCheck2) > 0) {
            $hasDescription = true;
            // Optional: set a transient message to inform admin (will be shown below)
            $msg = "Note: 'description' column was added to the subjects table.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $desc = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';

    if (empty($name)) {
        $msg = "Subject name is required.";
    } else {
        if ($hasDescription) {
            $sql = "INSERT INTO subjects(name, description) VALUES('$name', '$desc')";
        } else {
            // description column missing; insert only name
            $sql = "INSERT INTO subjects(name) VALUES('$name')";
        }

        if (mysqli_query($conn, $sql)) {
            $msg = "Subject added successfully!";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}

// Retrieve subjects; only select description if column exists
if ($hasDescription) {
    $subjects = mysqli_query($conn, "SELECT id, name, description FROM subjects ORDER BY id DESC");
} else {
    $subjects = mysqli_query($conn, "SELECT id, name FROM subjects ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>Manage Subjects</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Subject Name" required>
        </div>
        <?php if ($hasDescription): ?>
        <div class="col-md-5">
            <input type="text" name="description" class="form-control" placeholder="Description">
        </div>
        <?php endif; ?>
        <div class="col-md-<?php echo $hasDescription ? '3' : '8'; ?>">
            <button type="submit" class="btn btn-outline-primary w-100">Add Subject</button>
        </div>
    </form>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <?php if ($hasDescription): ?><th>Description</th><?php endif; ?>
        </tr>
        <?php while($row = mysqli_fetch_assoc($subjects)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <?php if ($hasDescription): ?><td><?= isset($row['description']) ? $row['description'] : '' ?></td><?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

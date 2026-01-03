<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ensure $role is defined to avoid undefined array key warnings
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
// If current request is for admin or student area, inject background CSS using absolute path
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
if (strpos($requestUri, '/admin/') !== false || strpos($requestUri, '/student/') !== false) {
    // Use absolute path so it resolves correctly from subfolders
    echo "<style>
        body { min-height:100vh; background: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.35)), url('/online_exam/images/exam.jpg') no-repeat center center fixed; background-size: cover; color: #fff; }
        .hero { min-height:70vh; display:flex; align-items:center; justify-content:center; flex-direction:column; }
        .card-transparent { background: rgba(255,255,255,0.06); border: none; }
        .btn-light, .btn-outline-light { color: #000; }
    </style>";
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Online Exam Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
  <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/subjects.php">Subjects</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/questions.php">Questions</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/exams.php">Exams</a></li>
          <li class="nav-item"><a class="nav-link" href="../admin/results.php">Results</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../student/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="../student/exam_list.php">Exams</a></li>
          <li class="nav-item"><a class="nav-link" href="../student/previous_attempts.php">Previous Attempts</a></li>
          <li class="nav-item"><a class="nav-link" href="../student/result.php">My Results</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-danger" href="../logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

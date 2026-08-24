<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$planner_count = isset($_SESSION['day_plan']) ? count($_SESSION['day_plan']) : 0;
$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mawanella Tourist Day-Visit Planner</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

  <!-- Preloader -->
  <div id="preloader">
    <div class="spinner"></div>
  </div>

  <!-- navbar -->
  <header>
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <a href="index.php">
            <i class="fa-solid fa-compass"></i>
            <span>Mawanella</span> Tourism
          </a>
        </div>
        <ul class="nav-links">
          <li><a href="index.php" class="<?= $page === 'index.php' ? 'active' : ''; ?>">Attractions</a></li>
          <li><a href="planner.php" class="btn-planner-cta <?= $page === 'planner.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-route"></i> Day Plan 
            <span id="planner-badge" class="planner-badge-count"><?= $planner_count; ?></span>
          </a></li>
          <li><a href="admin/login.php"><i class="fa-solid fa-user-shield"></i> Admin</a></li>
          <li>
            <button id="theme-toggle" class="btn-theme-toggle" title="Toggle Dark Mode">
              <i class="fa-solid fa-moon"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </header>

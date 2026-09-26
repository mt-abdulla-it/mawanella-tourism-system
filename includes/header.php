<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$planner_count = isset($_SESSION['day_plan']) ? count($_SESSION['day_plan']) : 0;
$page = basename($_SERVER['PHP_SELF']);

// dynamic page title (set by each page before including header)
$page_title = isset($page_title) ? $page_title . ' | Mawanella Tourism' : 'Mawanella Tourist Day-Visit Planner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Plan your day visit to tourist attractions within 25km of Mawanella, Sri Lanka. Discover waterfalls, temples, and wildlife.">
  <title><?= escape($page_title); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        <!-- hamburger toggle for mobile -->
        <button id="menu-toggle" class="hamburger-btn" aria-label="Toggle Menu">
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
          <span class="hamburger-line"></span>
        </button>

        <ul class="nav-links" id="nav-links">
          <li><a href="index.php" class="<?= $page === 'index.php' ? 'active' : ''; ?>">Attractions</a></li>
          <li><a href="planner.php" class="btn-planner-cta <?= $page === 'planner.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-route"></i> Day Plan 
            <span id="planner-badge" class="planner-badge-count <?= $planner_count > 0 ? 'pulse-anim' : ''; ?>"><?= $planner_count; ?></span>
          </a></li>
          <li><a href="#" id="openEmergencyModal" style="color: #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Emergency</a></li>
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

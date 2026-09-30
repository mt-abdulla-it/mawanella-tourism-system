<?php
require_once 'config/db.php';
$page_title = '404 Not Found';
require_once 'includes/header.php';
?>

<div class="container error-page-container">
  <i class="fa-solid fa-triangle-exclamation error-page-icon"></i>
  <h1 class="error-page-title">404 - Page Not Found</h1>
  <p class="error-page-desc">
    The requested tourist attraction page or link does not exist. It may have been removed or updated.
  </p>
  <a href="index.php" class="btn btn-primary">
    <i class="fa-solid fa-house"></i> Return to Homepage
  </a>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect("dashboard.php");
}

$errorMsg = '';

if (isset($_POST['login'])) {
    // csrf token validation
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        $errorMsg = "Invalid form submission (CSRF check failed).";
    } else {
        $uname = trim($_POST['username']);
        $pwd = trim($_POST['password']);

        if (empty($uname) || empty($pwd)) {
            $errorMsg = "Please enter both username and password.";
        } else {
            $stmt = mysqli_prepare($conn, "SELECT admin_id, username, password_hash FROM admins WHERE username = ?");
            mysqli_stmt_bind_param($stmt, "s", $uname);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);

            if ($userRow = mysqli_fetch_assoc($res)) {
                // pure password_verify check (no hardcoded fallback)
                if (password_verify($pwd, $userRow['password_hash'])) {
                    session_regenerate_id(true); // prevent session fixation
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $userRow['admin_id'];
                    $_SESSION['admin_user'] = $userRow['username'];
                    
                    redirect("dashboard.php");
                } else {
                    $errorMsg = "Invalid password.";
                }
            } else {
                $errorMsg = "Admin user not found.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Mawanella Tourism System</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-bg">

  <div class="container">
    <div class="form-card" style="max-width: 450px;">
      
      <div class="login-header">
        <i class="fa-solid fa-user-shield login-icon"></i>
        <h2 class="login-title">Admin Login</h2>
        <p class="login-subtitle">Enter details to access admin panel</p>
      </div>

      <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-circle-exclamation"></i> <?= escape($errorMsg); ?>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf(); ?>">

        <div class="form-group">
          <label for="username"><i class="fa-solid fa-user"></i> Username</label>
          <input type="text" id="username" name="username" required placeholder="Username">
        </div>

        <div class="form-group">
          <label for="password"><i class="fa-solid fa-key"></i> Password</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" required placeholder="Password">
            <button type="button" id="togglePassword" class="btn-toggle-password" title="Show Password">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" name="login" class="btn btn-primary btn-full-width">
          <i class="fa-solid fa-right-to-bracket"></i> Login Securely
        </button>
      </form>

      <div class="login-footer">
        <a href="../index.php" class="login-back-link">
          <i class="fa-solid fa-arrow-left"></i> Back to Homepage
        </a>
      </div>
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
      togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        // Toggle the eye / eye slash icon
        this.innerHTML = type === 'password' ? '<i class="fa-regular fa-eye"></i>' : '<i class="fa-regular fa-eye-slash"></i>';
      });
    }
  </script>
</body>
</html>

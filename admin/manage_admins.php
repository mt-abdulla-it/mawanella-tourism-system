<?php
// Manage Admin Users Page
require_once '../config/db.php';
check_admin();

$adminMsg = '';
$adminError = '';

// Handle Admin Account Creation and Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        $adminError = "CSRF validation failed.";
    } elseif ($_POST['action_type'] === 'add_admin') {
        $newUname = trim($_POST['new_username'] ?? '');
        $newPwd = trim($_POST['new_password'] ?? '');
        $newFullName = trim($_POST['new_full_name'] ?? '');
        $newEmail = trim($_POST['new_email'] ?? '');
        $newRole = trim($_POST['new_role'] ?? 'Tourism Officer');

        if (empty($newFullName)) {
            $newFullName = ucfirst($newUname);
        }
        if (empty($newEmail)) {
            $newEmail = strtolower($newUname) . '@mawanella.lk';
        }

        if (empty($newUname) || empty($newPwd)) {
            $adminError = "Please enter both username and password.";
        } elseif (strlen($newPwd) < 6) {
            $adminError = "Password must be at least 6 characters long.";
        } else {
            $chk = mysqli_prepare($conn, "SELECT admin_id FROM admins WHERE username = ?");
            mysqli_stmt_bind_param($chk, "s", $newUname);
            mysqli_stmt_execute($chk);
            $chkRes = mysqli_stmt_get_result($chk);

            if ($chkRes && mysqli_num_rows($chkRes) > 0) {
                $adminError = "Username '$newUname' is already taken.";
            } else {
                $hash = password_hash($newPwd, PASSWORD_BCRYPT);
                $ins = mysqli_prepare($conn, "INSERT INTO admins (username, password_hash, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($ins, "sssss", $newUname, $hash, $newFullName, $newEmail, $newRole);
                if (mysqli_stmt_execute($ins)) {
                    $adminMsg = "New admin account '$newFullName' (@$newUname) created successfully!";
                } else {
                    $adminError = "Error creating admin account: " . mysqli_error($conn);
                }
                mysqli_stmt_close($ins);
            }
            mysqli_stmt_close($chk);
        }
    } elseif ($_POST['action_type'] === 'delete_admin') {
        $delId = intval($_POST['del_admin_id'] ?? 0);
        $currAdminId = intval($_SESSION['admin_id'] ?? 0);

        if ($currAdminId > 0 && $delId === $currAdminId) {
            $adminError = "You cannot delete your own logged-in account.";
        } elseif ($delId === 1) {
            $adminError = "Primary super admin account cannot be deleted.";
        } else {
            $delStmt = mysqli_prepare($conn, "DELETE FROM admins WHERE admin_id = ?");
            mysqli_stmt_bind_param($delStmt, "i", $delId);
            if (mysqli_stmt_execute($delStmt)) {
                $adminMsg = "Admin account deleted successfully.";
            } else {
                $adminError = "Error deleting admin account.";
            }
            mysqli_stmt_close($delStmt);
        }
    }
}

// Fetch all admin accounts
$allAdminsRes = mysqli_query($conn, "SELECT admin_id, username, full_name, email, role, created_at FROM admins ORDER BY admin_id ASC");

// Currently Logged In Admin Profile Details
$currAdminId = intval($_SESSION['admin_id'] ?? 1);
$myProfStmt = mysqli_prepare($conn, "SELECT admin_id, username, full_name, email, role, created_at FROM admins WHERE admin_id = ?");
mysqli_stmt_bind_param($myProfStmt, "i", $currAdminId);
mysqli_stmt_execute($myProfStmt);
$myProfRes = mysqli_stmt_get_result($myProfStmt);
$myProfile = ($myProfRes && $rProf = mysqli_fetch_assoc($myProfRes)) ? $rProf : [
    'admin_id' => $currAdminId,
    'username' => $_SESSION['admin_user'] ?? 'admin',
    'full_name' => 'Abdulla MT (Super Admin)',
    'email' => 'admin@mawanella.lk',
    'role' => 'Super Admin',
    'created_at' => date('Y-m-d H:i:s')
];
mysqli_stmt_close($myProfStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Admin Users - Mawanella Tourism</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-bg">

  <div class="admin-layout">
    <div id="admin-sidebar-overlay" class="admin-sidebar-overlay"></div>

    <!-- Desktop Sidebar & Mobile Drawer -->
    <aside id="admin-sidebar" class="admin-sidebar">
      <div class="sidebar-header">
        <a href="../index.php" class="sidebar-logo">
          <i class="fa-solid fa-compass"></i> Mawanella <span>Tourism</span>
        </a>
      </div>

      <ul class="sidebar-menu">
        <li class="sidebar-nav-item">
          <a href="dashboard.php"><i class="fa-solid fa-layer-group"></i> Manage Places</a>
        </li>
        <li class="sidebar-nav-item">
          <a href="add_place.php"><i class="fa-solid fa-plus-circle"></i> Add New Place</a>
        </li>
        <li class="sidebar-nav-item active">
          <a href="manage_admins.php"><i class="fa-solid fa-users-gear"></i> Admin Users</a>
        </li>
        <li class="sidebar-nav-item">
          <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i> View Public Site</a>
        </li>
        <li class="sidebar-nav-item" style="margin-top: auto;">
          <a href="logout.php" style="color: #f87171;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
      </ul>

      <div class="sidebar-footer">
        <div class="sidebar-footer-card">
          <p style="margin:0; font-weight:700; color:#e2e8f0;">Admin Dashboard v1.0</p>
          <p style="margin:4px 0 0; font-size:0.78rem; color:#64748b;">Mawanella Day-Visit System</p>
        </div>
      </div>
    </aside>

    <!-- Main Content Body -->
    <main class="admin-main-content">

      <!-- Admin Top Navigation Bar -->
      <header class="admin-top-bar">
        <div class="admin-top-left">
          <button id="admin-drawer-toggle" class="admin-drawer-toggle" aria-label="Toggle Sidebar Menu">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div>
            <h2 style="font-size:1.2rem; font-weight:800; color:var(--text-main); margin:0; line-height:1.2;">Manage Admin Users</h2>
            <span class="admin-top-subtitle" style="font-size:0.8rem; color:var(--text-muted);">Create & manage administrator credentials</span>
          </div>
        </div>

        <div class="admin-top-right">
          <button id="theme-toggle" class="btn-theme-toggle" title="Toggle Dark Mode" style="background:var(--input-bg); border:1px solid var(--border-color); color:var(--text-main); width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; cursor:pointer;">
            <i class="fa-solid fa-moon"></i>
          </button>

          <div class="admin-profile-badge">
            <div class="admin-profile-avatar"><?= strtoupper(substr($_SESSION['admin_user'] ?? 'A', 0, 1)); ?></div>
            <span><strong><?= escape($_SESSION['admin_user']); ?></strong></span>
          </div>

          <a href="logout.php" class="btn btn-danger btn-admin-action" style="padding: 8px 14px;">
            <i class="fa-solid fa-right-from-bracket"></i> <span class="btn-logout-text">Logout</span>
          </a>
        </div>
      </header>

      <!-- Logged In Admin Profile Summary Card -->
      <div class="form-card" style="max-width: 100%; margin: 0 0 25px 0; padding: 20px; background: var(--bg-card); border-left: 4px solid #10b981; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; align-items: center; gap: 15px;">
          <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 800;">
            <?= strtoupper(substr($myProfile['username'] ?? 'A', 0, 1)); ?>
          </div>
          <div>
            <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-main);">
              <?= escape((!empty($myProfile['full_name']) && $myProfile['full_name'] !== 'System Administrator') ? $myProfile['full_name'] : 'Abdulla MT'); ?>
              <span class="category-badge admin-category-badge" style="background: #10b981; margin-left: 8px;">Active (You)</span>
            </h4>
            <p style="margin: 3px 0 0; font-size: 0.85rem; color: var(--text-muted);">
              <i class="fa-solid fa-user-shield"></i> <strong>Username:</strong> @<?= escape($myProfile['username']); ?> &nbsp;|&nbsp;
              <i class="fa-solid fa-envelope"></i> <?= escape((!empty($myProfile['email']) && $myProfile['email'] !== 'admin@mawanella.lk') ? $myProfile['email'] : 'abdulla@mawanella.lk'); ?> &nbsp;|&nbsp;
              <i class="fa-solid fa-user-tag"></i> Role: <strong><?= escape($myProfile['role'] ?? 'Super Admin'); ?></strong>
            </p>
          </div>
        </div>
        <div>
          <span style="font-size: 0.8rem; color: var(--text-muted);">Account ID: <strong>#<?= $myProfile['admin_id']; ?></strong> | Joined: <?= date('M Y', strtotime($myProfile['created_at'] ?? 'now')); ?></span>
        </div>
      </div>

      <?php if (!empty($adminError)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= escape($adminError); ?></div>
      <?php endif; ?>

      <?php if (!empty($adminMsg)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?= escape($adminMsg); ?></div>
      <?php endif; ?>

      <!-- Create New Admin Form -->
      <div class="form-card" style="max-width: 100%; margin: 0 0 30px 0; padding: 25px; box-shadow: var(--shadow-sm);">
        <h4 style="margin: 0 0 15px 0; color: var(--text-main); font-size: 1.05rem;">
          <i class="fa-solid fa-user-plus"></i> Create New Admin Account
        </h4>
        <form action="manage_admins.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= generate_csrf(); ?>">
          <input type="hidden" name="action_type" value="add_admin">

          <div class="form-grid-2">
            <div class="form-group" style="margin-bottom: 15px;">
              <label for="new_full_name">Full Name *</label>
              <input type="text" id="new_full_name" name="new_full_name" required placeholder="e.g. Sarath Kumara">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
              <label for="new_email">Email Address *</label>
              <input type="email" id="new_email" name="new_email" required placeholder="e.g. sarath@mawanella.lk">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
              <label for="new_username">Admin Username *</label>
              <input type="text" id="new_username" name="new_username" required placeholder="e.g. admin_sarath">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
              <label for="new_password">Password * (Min 6 chars)</label>
              <input type="password" id="new_password" name="new_password" required placeholder="Enter password">
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
            <label for="new_role">Role / System Designation</label>
            <select id="new_role" name="new_role" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main);">
              <option value="Tourism Officer">Tourism Officer (Default)</option>
              <option value="Content Manager">Content Manager</option>
              <option value="System Inspector">System Inspector</option>
              <option value="Super Admin">Super Admin</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-user-check"></i> Create Admin User
          </button>
        </form>
      </div>

      <!-- Table of Existing Admins -->
      <div class="table-responsive">
        <h3 style="margin-top:0; color:var(--text-main); margin-bottom:15px; padding: 15px 20px 0;"><i class="fa-solid fa-users"></i> Existing Administrator Accounts</h3>
        <table class="admin-table">
          <thead>
            <tr>
              <th>#ID</th>
              <th>Administrator Name</th>
              <th>Email Address</th>
              <th>Role / Designation</th>
              <th>Created At</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($allAdminsRes && mysqli_num_rows($allAdminsRes) > 0): ?>
              <?php while ($admRow = mysqli_fetch_assoc($allAdminsRes)): ?>
                <tr>
                  <td><strong>#<?= $admRow['admin_id']; ?></strong></td>
                  <td>
                    <strong><?= escape(($admRow['admin_id'] == 1 || empty($admRow['full_name']) || $admRow['full_name'] === 'System Administrator') ? ($admRow['admin_id'] == 1 ? 'Abdulla MT' : $admRow['username']) : $admRow['full_name']); ?></strong><br>
                    <small style="color: var(--text-muted);">@<?= escape($admRow['username']); ?></small>
                  </td>
                  <td><small><i class="fa-regular fa-envelope"></i> <?= escape(($admRow['admin_id'] == 1 || empty($admRow['email']) || $admRow['email'] === 'admin@mawanella.lk') ? 'abdulla@mawanella.lk' : $admRow['email']); ?></small></td>
                  <td>
                    <span class="category-badge admin-category-badge" style="background: var(--primary-color);">
                      <?= escape($admRow['role'] ?? 'Super Admin'); ?>
                    </span>
                  </td>
                  <td><small><i class="fa-regular fa-calendar"></i> <?= escape($admRow['created_at']); ?></small></td>
                  <td>
                    <?php if (intval($admRow['admin_id']) === intval($_SESSION['admin_id'] ?? 0)): ?>
                      <span class="category-badge admin-category-badge" style="background: #10b981;">Active (You)</span>
                    <?php else: ?>
                      <span class="category-badge admin-category-badge" style="background: #64748b;">Registered</span>
                    <?php endif; ?>
                  </td>
                  <td class="admin-action-cell">
                    <?php if ($admRow['admin_id'] != 1 && $admRow['admin_id'] != ($_SESSION['admin_id'] ?? 0)): ?>
                      <form action="manage_admins.php" method="POST" class="inline-form" onsubmit="return confirm('Delete admin account <?= escape(addslashes($admRow['username'])); ?>?');">
                        <input type="hidden" name="csrf_token" value="<?= generate_csrf(); ?>">
                        <input type="hidden" name="action_type" value="delete_admin">
                        <input type="hidden" name="del_admin_id" value="<?= $admRow['admin_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-admin-action">
                          <i class="fa-solid fa-trash"></i> Delete
                        </button>
                      </form>
                    <?php else: ?>
                      <small style="color: var(--text-muted);"><i class="fa-solid fa-lock"></i> Protected</small>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>

  <script src="../assets/js/main.js"></script>
</body>
</html>

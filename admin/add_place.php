<?php
// Add Place Page
require_once '../config/db.php';
check_admin();

$error = '';

// Get categories
$categories = get_categories($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $error = "Error: Uploaded files are too large in total size. Server limit is " . ini_get('post_max_size') . ". Please select fewer or smaller files (Maximum 10 images allowed).";
    } elseif (isset($_POST['save_place'])) {
        if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
            $error = "CSRF verification failed.";
        } else {
            $name = trim($_POST['name']);
            $catId = intval($_POST['category_id']);
            $desc = trim($_POST['description']);
            $hours = trim($_POST['opening_hours']);
            $tips = trim($_POST['travel_tips']);
            $cuisine = trim($_POST['local_cuisine']);
            $routes = trim($_POST['travel_routes']);
            $travelTime = trim($_POST['travel_time']);
            $dist = floatval($_POST['distance']);
            $estTime = trim($_POST['estimated_time']);
            $safeTime = trim($_POST['safe_visit_time']);
            $lat = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
            $lng = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
            $mapEmbed = !empty($_POST['map_embed']) ? trim($_POST['map_embed']) : null;
            $facil = trim($_POST['facilities']);
            $fees = trim($_POST['entrance_fees']);

            if (empty($name) || $catId <= 0 || empty($desc) || $dist <= 0) {
                $error = "Please fill in all required fields (Name, Category, Description, Distance).";
            } else {
                // Check image limit BEFORE inserting place
                if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                    $total_uploaded = 0;
                    $upload_error = '';
                    foreach($_FILES['images']['error'] as $err) {
                        if ($err === UPLOAD_ERR_OK) {
                            $total_uploaded++;
                        } elseif ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                            $upload_error = "Error: One or more images exceed the maximum file size limit (2MB).";
                        }
                    }
                    if (!empty($upload_error)) {
                        $error = $upload_error;
                    } elseif ($total_uploaded > 10) {
                        $error = "Error: You can only upload up to 10 images.";
                    }
                }

                if (empty($error)) {
                    $stmt = mysqli_prepare($conn, "INSERT INTO places (name, category_id, description, opening_hours, travel_tips, local_cuisine, travel_routes, travel_time, distance, estimated_time, safe_visit_time, latitude, longitude, map_embed, facilities, entrance_fees) 
                                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    mysqli_stmt_bind_param($stmt, "sissssssdssddsss", 
                        $name, $catId, $desc, $hours, $tips, $cuisine, $routes, $travelTime, $dist, $estTime, $safeTime, $lat, $lng, $mapEmbed, $facil, $fees
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $new_place_id = mysqli_insert_id($conn);
                        mysqli_stmt_close($stmt);

                        // handle multiple images upload
                        if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                            $targetDir = __DIR__ . '/../assets/images/places';
                            if (!file_exists($targetDir)) {
                                mkdir($targetDir, 0755, true);
                            }

                            $total = count($_FILES['images']['name']);
                            $is_first = true;

                            for ($i = 0; $i < $total; $i++) {
                                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                                    $tmpName = $_FILES['images']['tmp_name'][$i];
                                    $fileName = basename($_FILES['images']['name'][$i]);
                                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                                    
                                    // Security check: verify extension and actual image file type
                                    if (in_array($ext, $allowed) && @getimagesize($tmpName) !== false) {
                                        $newName = uniqid() . '_' . $i . '.' . $ext;
                                        $dest = $targetDir . '/' . $newName;
                                        if (move_uploaded_file($tmpName, $dest)) {
                                            $img_path = 'assets/images/places/' . $newName;
                                            $is_primary = $is_first ? 1 : 0;
                                            $img_stmt = mysqli_prepare($conn, "INSERT INTO place_images (place_id, image_path, is_primary) VALUES (?, ?, ?)");
                                            mysqli_stmt_bind_param($img_stmt, "isi", $new_place_id, $img_path, $is_primary);
                                            mysqli_stmt_execute($img_stmt);
                                            mysqli_stmt_close($img_stmt);
                                            $is_first = false;
                                        }
                                    }
                                }
                            }
                        }

                        redirect("dashboard.php?msg=added");
                    } else {
                        $error = "Error adding place: " . mysqli_error($conn);
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New Place - Admin</title>
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
        <li class="sidebar-nav-item active">
          <a href="add_place.php"><i class="fa-solid fa-plus-circle"></i> Add New Place</a>
        </li>
        <li class="sidebar-nav-item">
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

    <main class="admin-main-content">
      <!-- Admin Top Navigation Bar -->
      <header class="admin-top-bar">
        <div class="admin-top-left">
          <button id="admin-drawer-toggle" class="admin-drawer-toggle" aria-label="Toggle Sidebar Menu">
            <i class="fa-solid fa-bars"></i>
          </button>
          <div>
            <h2 style="font-size:1.2rem; font-weight:800; color:var(--text-main); margin:0; line-height:1.2;">Add New Place</h2>
            <span class="admin-top-subtitle" style="font-size:0.8rem; color:var(--text-muted);">Create a new tourist destination entry</span>
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


      <div class="form-card form-card-large">
        <div class="admin-form-header">
          <h2><i class="fa-solid fa-plus-circle"></i> Place Information</h2>
          <a href="dashboard.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= escape($error); ?></div>
        <?php endif; ?>

        <form action="add_place.php" method="POST" enctype="multipart/form-data">
          <!-- Include Shared Form Fields -->
          <?php include 'form_fields.php'; ?>

          <button type="submit" name="save_place" class="btn btn-primary btn-full-width">
            <i class="fa-solid fa-save"></i> Save Place
          </button>
        </form>
      </div>
    </main>
  </div>


  <script src="../assets/js/main.js"></script>
</body>
</html>

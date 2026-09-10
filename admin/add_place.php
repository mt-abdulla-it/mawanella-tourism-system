<?php
require_once '../config/db.php';
// check admin session using helper function
check_admin();

$error = '';

// Mawanella has waterfalls, temples and monuments so category dropdown is important
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
        $dist = floatval($_POST['distance']);
        $estTime = trim($_POST['estimated_time']);
        $lat = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $lng = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
        $facil = trim($_POST['facilities']);

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
                $stmt = mysqli_prepare($conn, "INSERT INTO places (name, category_id, description, opening_hours, travel_tips, distance, estimated_time, latitude, longitude, facilities) 
                                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                mysqli_stmt_bind_param($stmt, "sisssdsdds", 
                    $name, $catId, $desc, $hours, $tips, $dist, $estTime, $lat, $lng, $facil
                );

                if (mysqli_stmt_execute($stmt)) {
                    $new_place_id = mysqli_insert_id($conn);
                    mysqli_stmt_close($stmt);

                    // handle multiple images upload
                    if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                        $total = count($_FILES['images']['name']);
                        $is_first = true;

                        for ($i = 0; $i < $total; $i++) {
                            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                                $tmpName = $_FILES['images']['tmp_name'][$i];
                                $fileName = basename($_FILES['images']['name'][$i]);
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                                
                                if (in_array($ext, $allowed)) {
                                    $newName = uniqid() . '_' . $i . '.' . $ext;
                                    $dest = __DIR__ . '/../assets/images/places/' . $newName;
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
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-bg">

  <div class="container">
    <div class="form-card form-card-large">
      
      <div class="admin-form-header">
        <h2><i class="fa-solid fa-plus-circle"></i> Add New Place</h2>
        <a href="dashboard.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
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
  </div>

</body>
</html>

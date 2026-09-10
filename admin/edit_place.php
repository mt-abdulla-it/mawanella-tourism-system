<?php
require_once '../config/db.php';
// check admin session
check_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    redirect("dashboard.php");
}

$error = '';

// fetch current place record
$stmt = mysqli_prepare($conn, "SELECT * FROM places WHERE place_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$placeRes = mysqli_stmt_get_result($stmt);

if (!$placeRes || mysqli_num_rows($placeRes) === 0) {
    redirect("dashboard.php");
}

$place = mysqli_fetch_assoc($placeRes);
mysqli_stmt_close($stmt);

// fetch existing images
$place_images = [];
$stmt_img = mysqli_prepare($conn, "SELECT image_id, image_path, is_primary FROM place_images WHERE place_id = ?");
mysqli_stmt_bind_param($stmt_img, "i", $id);
mysqli_stmt_execute($stmt_img);
$res_img = mysqli_stmt_get_result($stmt_img);
while($row = mysqli_fetch_assoc($res_img)){
    $place_images[] = $row;
}
mysqli_stmt_close($stmt_img);

// fetch category dropdown options
$categories = get_categories($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $error = "Error: Uploaded files are too large in total size. Server limit is " . ini_get('post_max_size') . ". Please select fewer or smaller files (Maximum 10 images allowed).";
    } elseif (isset($_POST['update_place'])) {
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
            $error = "Please fill in all required fields.";
        } else {
            // handle image deletions
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $del_id) {
                    $del_id = intval($del_id);
                    $del_stmt = mysqli_prepare($conn, "SELECT image_path FROM place_images WHERE image_id = ? AND place_id = ?");
                    mysqli_stmt_bind_param($del_stmt, "ii", $del_id, $id);
                    mysqli_stmt_execute($del_stmt);
                    $del_res = mysqli_stmt_get_result($del_stmt);
                    if ($del_row = mysqli_fetch_assoc($del_res)) {
                        if (file_exists(__DIR__ . '/../' . $del_row['image_path'])) {
                            unlink(__DIR__ . '/../' . $del_row['image_path']);
                        }
                        $del_do = mysqli_prepare($conn, "DELETE FROM place_images WHERE image_id = ?");
                        mysqli_stmt_bind_param($del_do, "i", $del_id);
                        mysqli_stmt_execute($del_do);
                        mysqli_stmt_close($del_do);
                    }
                    mysqli_stmt_close($del_stmt);
                }
            }

            // update primary image if set
            if (isset($_POST['primary_image'])) {
                $primary_id = intval($_POST['primary_image']);
                $reset_stmt = mysqli_prepare($conn, "UPDATE place_images SET is_primary = 0 WHERE place_id = ?");
                mysqli_stmt_bind_param($reset_stmt, "i", $id);
                mysqli_stmt_execute($reset_stmt);
                mysqli_stmt_close($reset_stmt);
                
                $set_stmt = mysqli_prepare($conn, "UPDATE place_images SET is_primary = 1 WHERE image_id = ? AND place_id = ?");
                mysqli_stmt_bind_param($set_stmt, "ii", $primary_id, $id);
                mysqli_stmt_execute($set_stmt);
                mysqli_stmt_close($set_stmt);
            }

            // Count existing and uploaded
            $count_stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM place_images WHERE place_id = ?");
            mysqli_stmt_bind_param($count_stmt, "i", $id);
            mysqli_stmt_execute($count_stmt);
            $count_res = mysqli_stmt_get_result($count_stmt);
            $curr_count = mysqli_fetch_assoc($count_res)['c'];
            mysqli_stmt_close($count_stmt);

            $total_uploaded = 0;
            $upload_error = '';
            if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && $_FILES['images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
                foreach($_FILES['images']['error'] as $err) {
                    if ($err === UPLOAD_ERR_OK) {
                        $total_uploaded++;
                    } elseif ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                        $upload_error = "Error: One or more images exceed the maximum file size limit (2MB).";
                    }
                }
            }

            if (!empty($upload_error)) {
                $error = $upload_error;
            } elseif (($curr_count + $total_uploaded) > 10) {
                $error = "Error: You can only have up to 10 images. Please remove some images or select fewer files.";
            }

            if (empty($error)) {
                // handle new image uploads
                if ($total_uploaded > 0) {
                    $total_files = count($_FILES['images']['name']);
                    for ($i = 0; $i < $total_files; $i++) {
                        if ($curr_count >= 10) break;

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
                                    $is_prim = ($curr_count === 0 && $i === 0) ? 1 : 0;
                                    $img_stmt = mysqli_prepare($conn, "INSERT INTO place_images (place_id, image_path, is_primary) VALUES (?, ?, ?)");
                                    mysqli_stmt_bind_param($img_stmt, "isi", $id, $img_path, $is_prim);
                                    mysqli_stmt_execute($img_stmt);
                                    mysqli_stmt_close($img_stmt);
                                    $curr_count++;
                                }
                            }
                        }
                    }
                }

                $update_stmt = mysqli_prepare($conn, "UPDATE places SET 
                                                      name = ?, 
                                                      category_id = ?, 
                                                      description = ?, 
                                                      opening_hours = ?, 
                                                      travel_tips = ?, 
                                                      distance = ?, 
                                                      estimated_time = ?, 
                                                      latitude = ?, 
                                                      longitude = ?, 
                                                      facilities = ?
                                                      WHERE place_id = ?");
                
                mysqli_stmt_bind_param($update_stmt, "sisssdsddsi", 
                    $name, $catId, $desc, $hours, $tips, $dist, $estTime, $lat, $lng, $facil, $id
                );

                if (mysqli_stmt_execute($update_stmt)) {
                    mysqli_stmt_close($update_stmt);
                    redirect("dashboard.php?msg=updated");
                } else {
                    $error = "Error updating place: " . mysqli_error($conn);
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
  <title>Edit Place - Admin</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-bg">

  <div class="container">
    <div class="form-card form-card-large">
      
      <div class="admin-form-header">
        <h2><i class="fa-solid fa-pen-to-square"></i> Edit Place #<?= $place['place_id']; ?></h2>
        <a href="dashboard.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= escape($error); ?></div>
      <?php endif; ?>

      <form action="edit_place.php?id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
        <!-- Include Shared Form Fields -->
        <?php include 'form_fields.php'; ?>

        <button type="submit" name="update_place" class="btn btn-primary btn-full-width">
          <i class="fa-solid fa-save"></i> Update Place
        </button>
      </form>

    </div>
  </div>

</body>
</html>

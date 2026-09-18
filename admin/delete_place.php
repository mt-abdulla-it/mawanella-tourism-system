<?php
require_once '../config/db.php';
check_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        die("CSRF check failed.");
    }

    $id = isset($_POST['place_id']) ? intval($_POST['place_id']) : 0;

    if ($id > 0) {
        // Fetch all images for this place to delete from server
        $stmt_img = mysqli_prepare($conn, "SELECT image_path FROM place_images WHERE place_id = ?");
        mysqli_stmt_bind_param($stmt_img, "i", $id);
        mysqli_stmt_execute($stmt_img);
        $res = mysqli_stmt_get_result($stmt_img);
        while ($row = mysqli_fetch_assoc($res)) {
            if (!empty($row['image_path']) && file_exists(__DIR__ . '/../' . $row['image_path'])) {
                unlink(__DIR__ . '/../' . $row['image_path']);
            }
        }
        mysqli_stmt_close($stmt_img);

        $stmt = mysqli_prepare($conn, "DELETE FROM places WHERE place_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        redirect("dashboard.php?msg=deleted");
    } else {
        redirect("dashboard.php");
    }
} else {
    redirect("dashboard.php");
}
?>

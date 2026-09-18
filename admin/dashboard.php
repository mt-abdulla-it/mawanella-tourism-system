<?php
require_once '../config/db.php';
check_admin();

// admin search filter
$adminSearch = isset($_GET['search']) ? trim($_GET['search']) : '';

// pagination setup (5 places per page for admin table)
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// count total records for pagination
if (!empty($adminSearch)) {
    $countSql = "SELECT COUNT(*) as total FROM places p JOIN categories c ON p.category_id = c.category_id WHERE p.name LIKE ? OR c.category_name LIKE ?";
    $stmtCount = mysqli_prepare($conn, $countSql);
    $term = '%' . $adminSearch . '%';
    mysqli_stmt_bind_param($stmtCount, "ss", $term, $term);
    mysqli_stmt_execute($stmtCount);
    $totalRows = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCount))['total'];
    mysqli_stmt_close($stmtCount);

    $sql = "SELECT p.*, c.category_name 
            FROM places p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.name LIKE ? OR c.category_name LIKE ?
            ORDER BY p.place_id ASC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssii", $term, $term, $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $totalRows = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM places"))['total'];

    $sql = "SELECT p.*, c.category_name 
            FROM places p 
            JOIN categories c ON p.category_id = c.category_id 
            ORDER BY p.place_id ASC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$totalPages = ceil($totalRows / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Manage Places</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-bg">

  <!-- Admin Navigation -->
  <header class="admin-header">
    <div class="container navbar">
      <div class="logo">
        <a href="../index.php">Mawanella<span>Tourism</span> <small style="font-size:0.8rem; margin-left:10px; color:#cbd5e1;">(Admin Panel)</small></a>
      </div>
      <ul class="nav-links">
        <li><span class="admin-header-nav-text">Logged in as: <strong><?= escape($_SESSION['admin_user']); ?></strong></span></li>
        <li>
          <a href="logout.php" class="btn btn-danger btn-admin-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
      </ul>
    </div>
  </header>

  <div class="container admin-page-container">
    
    <div class="flex-between admin-page-header">
      <div>
        <h1 class="admin-page-title">Manage Tourist Places</h1>
        <p class="admin-page-subtitle">Add, edit or delete tourist places near Mawanella (under 25km radius).</p>
      </div>

      <a href="add_place.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add New Place
      </a>
    </div>

    <!-- Search & Counter Control Bar -->
    <div class="flex-between admin-search-wrapper">
      <form action="dashboard.php" method="GET" class="admin-search-form">
        <input type="text" name="search" placeholder="Search place or category..." value="<?= escape($adminSearch); ?>" class="admin-search-input">
        <button type="submit" class="btn btn-outline btn-admin-search"><i class="fa-solid fa-search"></i> Search</button>
        <?php if ($adminSearch): ?>
          <a href="dashboard.php" class="btn btn-danger btn-admin-clear">Clear</a>
        <?php endif; ?>
      </form>

      <div class="admin-total-wrapper">
        Total Places: <span class="admin-total-count"><?= $totalRows; ?></span>
      </div>
    </div>

    <!-- Notifications -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
      <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Place added successfully!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
      <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> Place updated successfully!</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
      <div class="alert alert-danger"><i class="fa-solid fa-trash"></i> Place deleted.</div>
    <?php endif; ?>

    <!-- Places Table -->
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#ID</th>
            <th>Place Name</th>
            <th>Category</th>
            <th>Opening Hours</th>
            <th>Visit Time</th>
            <th>Distance</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($place = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $place['place_id']; ?></td>
                <td>
                  <strong><?= escape($place['name']); ?></strong>
                </td>
                <td>
                  <span class="category-badge admin-category-badge">
                    <?= escape($place['category_name']); ?>
                  </span>
                </td>
                <td><small><?= escape($place['opening_hours']); ?></small></td>
                <td><small><?= escape($place['estimated_time']); ?></small></td>
                <td><?= number_format($place['distance'], 1); ?> km</td>
                <td class="admin-action-cell">
                  <a href="edit_place.php?id=<?= $place['place_id']; ?>" class="btn btn-outline btn-admin-action mr-5">
                    <i class="fa-solid fa-pen"></i> Edit
                  </a>
                  
                  <form action="delete_place.php" method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete <?= addslashes($place['name']); ?>?');">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf(); ?>">
                    <input type="hidden" name="place_id" value="<?= $place['place_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-admin-action">
                      <i class="fa-solid fa-trash"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="admin-empty-table">No tourist place records found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Student Pagination Controls -->
    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="dashboard.php?page=<?= $page - 1; ?><?= !empty($adminSearch) ? '&search=' . urlencode($adminSearch) : ''; ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="dashboard.php?page=<?= $i; ?><?= !empty($adminSearch) ? '&search=' . urlencode($adminSearch) : ''; ?>" class="<?= $i == $page ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="dashboard.php?page=<?= $page + 1; ?><?= !empty($adminSearch) ? '&search=' . urlencode($adminSearch) : ''; ?>">Next &raquo;</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>

  </div>

</body>
</html>

<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Mawanella area has waterfalls, cave temples and wildlife parks
$catQuery = "SELECT * FROM categories ORDER BY category_id ASC";
$catRes = mysqli_query($conn, $catQuery);

// filter and search checks
$catFilter = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// prepared query for security
$sql = "SELECT p.*, c.category_name, 
               (SELECT image_path FROM place_images WHERE place_id = p.place_id ORDER BY is_primary DESC, image_id ASC LIMIT 1) as primary_image 
        FROM places p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE 1=1";

$params = [];
$types = "";

if ($catFilter > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $catFilter;
    $types .= "i";
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.facilities LIKE ?)";
    $term = '%' . $search . '%';
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
    $types .= "sss";
}

$sql .= " ORDER BY p.distance ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$placesRes = mysqli_stmt_get_result($stmt);
?>

<!-- Hero Banner -->
<section class="hero animate-fade-up">
  <div class="container">
    <h1>Mawanella Tourism & Day Visit Planner</h1>
    <p>Discover waterfalls, cave temples, scenic peaks and wildlife spots within 25 km from Mawanella town.</p>
    
    <!-- Search Form -->
    <form action="index.php" method="GET" class="search-box animate-fade-up delay-100">
      <?php if ($catFilter > 0): ?>
        <input type="hidden" name="category" value="<?= $catFilter; ?>">
      <?php endif; ?>
      <input type="text" name="search" placeholder="Search places..." value="<?= escape($search); ?>">
      <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </form>
  </div>
</section>

<!-- Main Content -->
<div class="container">

  <!-- Category Filter Section -->
  <section class="filter-section animate-fade-up delay-200">
    <div class="filter-title"><i class="fa-solid fa-filter"></i> Filter by Category:</div>
    <div class="category-tabs">
      <a href="index.php<?= !empty($search) ? '?search=' . urlencode($search) : ''; ?>" 
         class="tab-btn <?= $catFilter == 0 ? 'active' : ''; ?>">All Attractions</a>
      
      <?php while ($cat = mysqli_fetch_assoc($catRes)): ?>
        <a href="index.php?category=<?= $cat['category_id']; ?><?= !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
           class="tab-btn <?= $catFilter == $cat['category_id'] ? 'active' : ''; ?>">
           <?= escape($cat['category_name']); ?>
        </a>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- Places Grid -->
  <section class="places-grid">
    <?php if (mysqli_num_rows($placesRes) > 0): ?>
      <?php while ($place = mysqli_fetch_assoc($placesRes)): ?>
        <?php 
            $imgSrc = (!empty($place['primary_image']) && file_exists($place['primary_image'])) 
                      ? escape($place['primary_image']) 
                      : "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=500&q=80";
        ?>
        <article class="card animate-fade-up delay-300">
          <div class="card-header">
            <img src="<?= $imgSrc; ?>" alt="<?= escape($place['name']); ?>">
            <span class="category-badge"><?= escape($place['category_name']); ?></span>
            <span class="distance-badge"><i class="fa-solid fa-location-dot"></i> <?= number_format($place['distance'], 1); ?> km from Mawanella</span>
          </div>

          <div class="card-body">
            <h2 class="card-title"><?= escape($place['name']); ?></h2>
            <p class="card-text">
              <?php 
                $descText = escape($place['description']);
                echo strlen($descText) > 110 ? substr($descText, 0, 110) . '...' : $descText;
              ?>
            </p>

            <div class="meta-info">
              <span><i class="fa-regular fa-clock"></i> <?= escape($place['opening_hours']); ?></span>
              <span><i class="fa-solid fa-hourglass-half"></i> <?= escape($place['estimated_time']); ?></span>
            </div>

            <div class="card-actions">
              <a href="details.php?id=<?= $place['place_id']; ?>" class="btn btn-outline btn-flex-1">
                <i class="fa-solid fa-circle-info"></i> Details
              </a>
              <button class="btn btn-primary btn-add-plan" 
                      data-id="<?= $place['place_id']; ?>" 
                      data-name="<?= escape($place['name']); ?>">
                <i class="fa-solid fa-plus"></i> Add to Plan
              </button>
            </div>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fa-solid fa-magnifying-glass-location empty-state-icon"></i>
        <h3>No Attractions Found</h3>
        <p class="empty-state-text">No results found. Try a different search or clear your filter.</p>
        <a href="index.php" class="btn btn-primary empty-state-btn">Clear Filters</a>
      </div>
    <?php endif; ?>
  </section>

</div>

<?php 
mysqli_stmt_close($stmt);
require_once 'includes/footer.php'; 
?>

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

  <!-- Live Weather Widget -->
  <section class="weather-widget animate-fade-up delay-150" id="weatherWidget" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 25px; margin-top: -30px; margin-bottom: 35px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); position: relative; z-index: 10;">
    <div class="flex-between" style="align-items: center; flex-wrap: wrap; gap: 15px;">
      <div class="weather-info">
        <h3 style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 8px;"><i class="fa-solid fa-location-dot"></i> Live Mawanella Weather</h3>
        <p id="weatherDesc" style="color: var(--text-color); font-size: 1rem; display: flex; align-items: center; gap: 10px; margin:0;">
          <i class="fa-solid fa-spinner fa-spin"></i> Loading forecast...
        </p>
      </div>
      <div class="weather-temp">
        <h2 id="weatherTemp" style="font-size: 2.8rem; font-weight: 800; color: var(--text-color); margin:0;">--°C</h2>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Free Open-Meteo API for Mawanella coordinates (Lat 7.25, Lon 80.43)
      fetch('https://api.open-meteo.com/v1/forecast?latitude=7.25&longitude=80.43&current=temperature_2m,weather_code&timezone=Asia%2FColombo')
        .then(res => res.json())
        .then(data => {
           if(data && data.current) {
             const temp = data.current.temperature_2m;
             const code = data.current.weather_code;
             
             let desc = "Clear Skies";
             let icon = '<i class="fa-solid fa-sun" style="color:#f59e0b; font-size: 1.2rem;"></i>';
             
             if (code > 0 && code <= 3) { desc = "Partly Cloudy"; icon = '<i class="fa-solid fa-cloud-sun" style="color:#f59e0b; font-size: 1.2rem;"></i>'; }
             else if (code >= 45 && code <= 48) { desc = "Foggy"; icon = '<i class="fa-solid fa-smog" style="color:#94a3b8; font-size: 1.2rem;"></i>'; }
             else if (code >= 51 && code <= 67) { desc = "Rainy"; icon = '<i class="fa-solid fa-cloud-rain" style="color:#3b82f6; font-size: 1.2rem;"></i>'; }
             else if (code >= 80 && code <= 82) { desc = "Showers"; icon = '<i class="fa-solid fa-cloud-showers-heavy" style="color:#2563eb; font-size: 1.2rem;"></i>'; }
             else if (code >= 95) { desc = "Thunderstorms"; icon = '<i class="fa-solid fa-bolt" style="color:#d97706; font-size: 1.2rem;"></i>'; }
             
             document.getElementById('weatherTemp').innerHTML = Math.round(temp) + '°C';
             
             const tip = (code >= 51) ? "Carry an umbrella!" : "Perfect for outdoor trips!";
             document.getElementById('weatherDesc').innerHTML = icon + ' <strong>' + desc + '</strong> &mdash; ' + tip;
           }
        })
        .catch(err => {
           document.getElementById('weatherDesc').innerHTML = '<i class="fa-solid fa-cloud-xmark"></i> Forecast unavailable right now.';
        });
    });
  </script>

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
            <img src="<?= $imgSrc; ?>" alt="<?= escape($place['name']); ?>" loading="lazy">
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
              <span><i class="fa-solid fa-ticket"></i> <?= !empty($place['entrance_fees']) ? escape($place['entrance_fees']) : 'Free'; ?></span>
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

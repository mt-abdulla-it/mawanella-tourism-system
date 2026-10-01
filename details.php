<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    redirect("index.php");
}

$stmt = mysqli_prepare($conn, "SELECT p.*, c.category_name 
                               FROM places p 
                               JOIN categories c ON p.category_id = c.category_id 
                               WHERE p.place_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if (!$res || mysqli_num_rows($res) === 0) {
    $page_title = 'Place Not Found';
    require_once 'includes/header.php';
    echo "<div class='container' style='padding:50px 0;'><h2>Place not found.</h2><a href='index.php' class='btn btn-primary'>Back to List</a></div>";
    require_once 'includes/footer.php';
    exit();
}

$place = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

// set dynamic page title
$page_title = $place['name'];
require_once 'includes/header.php';

// fetch images
$images = [];
$img_stmt = mysqli_prepare($conn, "SELECT image_path FROM place_images WHERE place_id = ?");
mysqli_stmt_bind_param($img_stmt, "i", $id);
mysqli_stmt_execute($img_stmt);
$img_res = mysqli_stmt_get_result($img_stmt);
while($row = mysqli_fetch_assoc($img_res)){
    if (!empty($row['image_path']) && file_exists(__DIR__ . '/' . $row['image_path'])) {
        $images[] = $row['image_path'];
    }
}
mysqli_stmt_close($img_stmt);

$displayImages = count($images) > 0 ? $images : ['https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=1200&q=80'];
?>

<div class="container details-page-container animate-fade-up">
  
  <div class="back-btn-wrapper">
    <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Attractions</a>
  </div>

  <div class="details-wrapper">
      <div class="details-header">
        <div>
          <span class="category-badge details-category-badge">
            <i class="fa-solid fa-tag"></i> <?= escape($place['category_name']); ?>
          </span>
          <h1 class="details-title"><?= escape($place['name']); ?></h1>
        </div>
      
      <div>
        <button class="btn btn-primary btn-add-plan" 
                data-id="<?= $place['place_id']; ?>" 
                data-name="<?= escape($place['name']); ?>">
          <i class="fa-solid fa-plus"></i> Add to Day Plan
        </button>
      </div>
    </div>

    <!-- Premium Image Gallery -->
    <div style="margin-bottom: 30px;" class="animate-fade-up delay-100">
      <div class="premium-gallery">
        <?php foreach($displayImages as $index => $img): ?>
          <div class="gallery-item" onclick="openLightbox(<?= $index ?>)">
            <img src="<?= escape($img); ?>" alt="<?= escape($place['name']); ?> - Image <?= $index + 1; ?>" loading="lazy">
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="details-grid animate-fade-up delay-200">
      <div>
        <div class="info-block">
          <h3><i class="fa-solid fa-align-left"></i> Description</h3>
          <p><?= nl2br(escape($place['description'])); ?></p>
        </div>

        <div class="info-block meta-info-grid">
          <div>
            <h3><i class="fa-solid fa-location-dot"></i> Distance & Time</h3>
            <p><strong><?= number_format($place['distance'], 1); ?> km</strong> (approx. <?= !empty($place['travel_time']) ? escape($place['travel_time']) : 'N/A'; ?> travel)</p>
          </div>
          <div>
            <h3><i class="fa-regular fa-clock"></i> Opening Hours</h3>
            <p><?= escape($place['opening_hours']); ?></p>
          </div>
          <div>
            <h3><i class="fa-solid fa-hourglass-half"></i> Visit Time</h3>
            <p><?= escape($place['estimated_time']); ?></p>
          </div>
          <div>
            <h3><i class="fa-solid fa-shield-halved"></i> Safe Visit Time</h3>
            <p><?= !empty($place['safe_visit_time']) ? escape($place['safe_visit_time']) : 'Any time during opening hours'; ?></p>
          </div>
          <div>
            <h3><i class="fa-solid fa-ticket"></i> Entrance Fee</h3>
            <p><?= !empty($place['entrance_fees']) ? escape($place['entrance_fees']) : 'Free'; ?></p>
          </div>
        </div>

        <?php if (!empty($place['travel_routes'])): ?>
          <div class="info-block">
            <h3><i class="fa-solid fa-route"></i> Travel Routes</h3>
            <p><?= nl2br(escape($place['travel_routes'])); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($place['travel_tips'])): ?>
          <div class="info-block travel-tips-block">
            <h3 class="travel-tips-title"><i class="fa-solid fa-lightbulb"></i> Travel & Safety Tips</h3>
            <p class="travel-tips-text"><?= escape($place['travel_tips']); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($place['local_cuisine'])): ?>
          <div class="info-block">
            <h3><i class="fa-solid fa-utensils"></i> Local Cuisine</h3>
            <p><?= escape($place['local_cuisine']); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($place['facilities'])): ?>
          <div class="info-block">
            <h3><i class="fa-solid fa-concierge-bell"></i> Facilities</h3>
            <div class="facility-tags">
              <?php 
                $facilityList = array_map('trim', explode(',', $place['facilities']));
                // icon mapping for common facilities
                $facilityIcons = [
                  'parking' => 'fa-square-parking',
                  'restroom' => 'fa-restroom',
                  'restrooms' => 'fa-restroom',
                  'food' => 'fa-utensils',
                  'restaurant' => 'fa-utensils',
                  'restaurants' => 'fa-utensils',
                  'cafeteria' => 'fa-mug-hot',
                  'snack' => 'fa-cookie-bite',
                  'shop' => 'fa-shop',
                  'shops' => 'fa-shop',
                  'souvenir' => 'fa-gift',
                  'guided' => 'fa-person-hiking',
                  'ticket' => 'fa-ticket',
                  'walking' => 'fa-person-walking',
                  'viewpoint' => 'fa-binoculars',
                  'viewing' => 'fa-binoculars',
                  'tower' => 'fa-tower-observation',
                  'waiting' => 'fa-chair',
                  'shoe' => 'fa-shoe-prints',
                  'trail' => 'fa-map-signs',
                  'local' => 'fa-store',
                ];
                foreach ($facilityList as $fac):
                  $facLower = strtolower($fac);
                  $icon = 'fa-check';
                  foreach ($facilityIcons as $keyword => $iconClass) {
                    if (strpos($facLower, $keyword) !== false) {
                      $icon = $iconClass;
                      break;
                    }
                  }
              ?>
                <span class="facility-tag"><i class="fa-solid <?= $icon; ?>"></i> <?= escape($fac); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <h3 class="map-title">
          <i class="fa-solid fa-map-location-dot"></i> Map Location
        </h3>
        
        <?php
          $hasCoords = !empty($place['latitude']) && !empty($place['longitude']) && floatval($place['latitude']) != 0;
          $dirOrigin = 'Mawanella,+Sri+Lanka';
          $dirDestination = urlencode($place['name'] . ', Sri Lanka');
        ?>

        <?php if (!empty($place['map_embed']) && stripos($place['map_embed'], '<iframe') !== false): ?>
          <div class="map-embed-container">
            <?= $place['map_embed']; ?>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; flex-wrap: wrap; gap: 10px;">
            <p class="map-desc" style="margin: 0;">
              <?php if ($hasCoords): ?>
                <i class="fa-solid fa-location-arrow"></i> Coordinates: <?= escape($place['latitude']); ?>, <?= escape($place['longitude']); ?>
              <?php else: ?>
                <i class="fa-solid fa-location-dot"></i> <?= number_format($place['distance'], 1); ?> km from Mawanella
              <?php endif; ?>
            </p>
            <a href="https://www.google.com/maps/dir/?api=1&origin=<?= $dirOrigin; ?>&destination=<?= $dirDestination; ?>" 
               target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.92rem; font-weight: 600; border-radius: 8px;">
               <i class="fa-solid fa-diamond-turn-right"></i> Get Directions From Mawanella
            </a>
          </div>
        <?php elseif ($hasCoords): ?>
          <div id="map-container" data-lat="<?= escape($place['latitude']); ?>" data-lng="<?= escape($place['longitude']); ?>" data-title="<?= escape($place['name']); ?>" style="width:100%; height:350px; border-radius:12px; overflow:hidden; box-shadow: var(--shadow-sm);">
            <iframe 
              src="https://maps.google.com/maps?q=<?= escape($place['latitude']); ?>,<?= escape($place['longitude']); ?>&hl=en&z=14&output=embed" 
              width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; flex-wrap: wrap; gap: 10px;">
            <p class="map-desc" style="margin: 0;">
              <i class="fa-solid fa-location-arrow"></i> Coordinates: <?= escape($place['latitude']); ?>, <?= escape($place['longitude']); ?>
            </p>
            <a href="https://www.google.com/maps/dir/?api=1&origin=<?= $dirOrigin; ?>&destination=<?= $dirDestination; ?>" 
               target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.92rem; font-weight: 600; border-radius: 8px;">
               <i class="fa-solid fa-diamond-turn-right"></i> Get Directions From Mawanella
            </a>
          </div>
        <?php else: ?>
          <div class="map-placeholder" style="margin-bottom: 15px;">
            <p><i class="fa-solid fa-map-pin"></i> Interactive map coordinates not available for this location.</p>
          </div>
          <div style="display: flex; justify-content: flex-end; margin-top: 15px;">
            <a href="https://www.google.com/maps/dir/?api=1&origin=<?= $dirOrigin; ?>&destination=<?= $dirDestination; ?>" 
               target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="padding: 10px 18px; font-size: 0.92rem; font-weight: 600; border-radius: 8px;">
               <i class="fa-solid fa-diamond-turn-right"></i> Get Directions From Mawanella
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Nearby Places Section -->
  <?php
  // Fetch nearby places by distance
  $currentDist = floatval($place['distance']);
  $nearby_stmt = mysqli_prepare($conn, "SELECT p.*, c.category_name,
                                        (SELECT image_path FROM place_images WHERE place_id = p.place_id ORDER BY is_primary DESC, image_id ASC LIMIT 1) as primary_image
                                        FROM places p 
                                        JOIN categories c ON p.category_id = c.category_id 
                                        WHERE p.place_id != ? 
                                        AND ABS(p.distance - ?) < 10
                                        ORDER BY ABS(p.distance - ?) ASC
                                        LIMIT 3");
  mysqli_stmt_bind_param($nearby_stmt, "idd", $id, $currentDist, $currentDist);
  mysqli_stmt_execute($nearby_stmt);
  $nearbyRes = mysqli_stmt_get_result($nearby_stmt);

  if (mysqli_num_rows($nearbyRes) > 0):
  ?>
  <div class="nearby-places-section animate-fade-up delay-400" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color);">
    <h2 style="color: var(--primary-color); margin-bottom: 20px; font-size: 1.5rem;"><i class="fa-solid fa-map-location"></i> Explore Nearby Places</h2>
    <div class="places-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
      <?php while ($nPlace = mysqli_fetch_assoc($nearbyRes)): ?>
        <?php 
            $imgSrc = (!empty($nPlace['primary_image']) && file_exists(__DIR__ . '/' . $nPlace['primary_image'])) 
                      ? escape($nPlace['primary_image']) 
                      : "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=500&q=80";
        ?>
        <article class="card">
          <div class="card-header" style="height: 150px;">
            <img src="<?= $imgSrc; ?>" alt="<?= escape($nPlace['name']); ?>" loading="lazy">
            <span class="category-badge" style="font-size: 0.7rem;"><?= escape($nPlace['category_name']); ?></span>
          </div>
          <div class="card-body" style="padding: 15px;">
            <h3 class="card-title" style="font-size: 1.1rem; margin-bottom: 5px;"><?= escape($nPlace['name']); ?></h3>
            <div class="meta-info" style="font-size: 0.8rem; margin-bottom: 10px;">
              <span><i class="fa-solid fa-location-dot"></i> <?= number_format($nPlace['distance'], 1); ?> km from Mawanella</span>
            </div>
            <a href="details.php?id=<?= $nPlace['place_id']; ?>" class="btn btn-outline btn-full-width" style="padding: 8px; font-size: 0.9rem;">View Details</a>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
  </div>
  <?php endif; ?>
  <?php mysqli_stmt_close($nearby_stmt); ?>

</div>

<!-- Lightbox Modal -->
<div id="galleryLightbox" class="lightbox">
    <div class="lightbox-content">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <button class="lightbox-nav lightbox-prev" onclick="changeImage(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="lightboxImage" class="lightbox-img" src="" alt="Gallery Image">
        <button class="lightbox-nav lightbox-next" onclick="changeImage(1)"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
</div>

<script>
// Lightbox Javascript Logic
const images = <?= json_encode($displayImages); ?>;
let currentIndex = 0;
const lightbox = document.getElementById('galleryLightbox');
const lightboxImg = document.getElementById('lightboxImage');

function openLightbox(index) {
    if (images.length === 0) return;
    currentIndex = index;
    lightboxImg.src = images[currentIndex];
    lightbox.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // stop background scrolling
}

function closeLightbox() {
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function changeImage(direction) {
    currentIndex += direction;
    if (currentIndex >= images.length) currentIndex = 0;
    if (currentIndex < 0) currentIndex = images.length - 1;
    lightboxImg.src = images[currentIndex];
}

// Close lightbox on clicking outside the image
lightbox.addEventListener('click', function(e) {
    if (e.target === lightbox) closeLightbox();
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (lightbox.style.display === 'flex') {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') changeImage(1);
        if (e.key === 'ArrowLeft') changeImage(-1);
    }
});
</script>

<script src="assets/js/map.js"></script>
<!-- Google Maps JavaScript API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATsyhCIT9v9ONMtxAUNa_7e_pQGQt6MxE&callback=initMap"></script>

<?php require_once 'includes/footer.php'; ?>

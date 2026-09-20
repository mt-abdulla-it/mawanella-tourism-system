<?php
require_once 'config/db.php';
require_once 'includes/header.php';

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
    echo "<div class='container' style='padding:50px 0;'><h2>Place not found.</h2><a href='index.php' class='btn btn-primary'>Back to List</a></div>";
    require_once 'includes/footer.php';
    exit();
}

$place = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

// fetch images
$images = [];
$img_stmt = mysqli_prepare($conn, "SELECT image_path FROM place_images WHERE place_id = ?");
mysqli_stmt_bind_param($img_stmt, "i", $id);
mysqli_stmt_execute($img_stmt);
$img_res = mysqli_stmt_get_result($img_stmt);
while($row = mysqli_fetch_assoc($img_res)){
    $images[] = $row['image_path'];
}
mysqli_stmt_close($img_stmt);
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
      <?php if (count($images) > 0): ?>
        <div class="premium-gallery">
          <?php foreach($images as $index => $img): ?>
            <div class="gallery-item" onclick="openLightbox(<?= $index ?>)">
              <img src="<?= escape($img); ?>" alt="<?= escape($place['name']); ?>">
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="premium-gallery">
          <div class="gallery-item">
            <img src="https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=1200&q=80" alt="Default Image">
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="details-grid animate-fade-up delay-200">
      <div>
        <div class="info-block">
          <h3><i class="fa-solid fa-align-left"></i> Description</h3>
          <p><?= nl2br(escape($place['description'])); ?></p>
        </div>

        <div class="info-block meta-info-grid">
          <div>
            <h3><i class="fa-solid fa-location-dot"></i> Distance</h3>
            <p><strong><?= number_format($place['distance'], 1); ?> km</strong> from Mawanella</p>
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
            <h3><i class="fa-solid fa-ticket"></i> Entrance Fee</h3>
            <p><?= !empty($place['entrance_fees']) ? escape($place['entrance_fees']) : 'Free'; ?></p>
          </div>
        </div>

        <?php if (!empty($place['travel_tips'])): ?>
          <div class="info-block travel-tips-block">
            <h3 class="travel-tips-title"><i class="fa-solid fa-lightbulb"></i> Travel & Safety Tips</h3>
            <p class="travel-tips-text"><?= escape($place['travel_tips']); ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($place['facilities'])): ?>
          <div class="info-block">
            <h3><i class="fa-solid fa-concierge-bell"></i> Facilities</h3>
            <p><?= escape($place['facilities']); ?></p>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <h3 class="map-title">
          <i class="fa-solid fa-map-location-dot"></i> Map Location
        </h3>
        
        <?php if (!empty($place['latitude']) && !empty($place['longitude'])): ?>
          <div id="map-container" data-lat="<?= escape($place['latitude']); ?>" data-lng="<?= escape($place['longitude']); ?>" data-title="<?= escape($place['name']); ?>">
            <!-- Map renders here -->
          </div>
          <p class="map-desc">
            <i class="fa-solid fa-location-arrow"></i> Coordinates: <?= escape($place['latitude']); ?>, <?= escape($place['longitude']); ?>
          </p>
        <?php else: ?>
          <div class="map-placeholder">
            <p><i class="fa-solid fa-map-pin"></i> Map coordinates not available for this location.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

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
const images = <?= json_encode($images); ?>;
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

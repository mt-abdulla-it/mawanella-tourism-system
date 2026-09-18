<!-- Shared Form Fields Partial for Add/Edit Place -->
<!-- Student: Abdulla MT (E2410182) -->

<input type="hidden" name="csrf_token" value="<?= generate_csrf(); ?>">

<div class="form-group">
  <label for="name">Place Name *</label>
  <input type="text" id="name" name="name" required placeholder="e.g. Alagalla Mountain Peak" value="<?= isset($place['name']) ? escape($place['name']) : ''; ?>">
</div>

<div class="form-grid-2">
  <div class="form-group">
    <label for="category_id">Category *</label>
    <select id="category_id" name="category_id" required>
      <option value="">-- Select Category --</option>
      <?php if (isset($categories) && is_array($categories)): ?>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['category_id']; ?>" <?= (isset($place['category_id']) && $place['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
            <?= escape($cat['category_name']); ?>
          </option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="distance">Distance from Mawanella (km) *</label>
    <input type="number" step="0.1" id="distance" name="distance" required placeholder="e.g. 12.5" value="<?= isset($place['distance']) ? escape($place['distance']) : ''; ?>">
  </div>
</div>

<div class="form-group">
  <label for="description">Description *</label>
  <textarea id="description" name="description" rows="4" required placeholder="Write details about the tourist place..."><?= isset($place['description']) ? escape($place['description']) : ''; ?></textarea>
</div>

<div class="form-grid-2">
  <div class="form-group">
    <label for="opening_hours">Opening Hours</label>
    <input type="text" id="opening_hours" name="opening_hours" placeholder="e.g. 8:00 AM - 5:00 PM" value="<?= isset($place['opening_hours']) ? escape($place['opening_hours']) : ''; ?>">
  </div>

  <div class="form-group">
    <label for="estimated_time">Visit Duration</label>
    <input type="text" id="estimated_time" name="estimated_time" placeholder="e.g. 1 - 2 hours" value="<?= isset($place['estimated_time']) ? escape($place['estimated_time']) : ''; ?>">
  </div>
</div>

<div class="form-group">
  <label for="travel_tips">Travel & Safety Tips</label>
  <textarea id="travel_tips" name="travel_tips" rows="2" placeholder="Wear proper trekking shoes, carry water..."><?= isset($place['travel_tips']) ? escape($place['travel_tips']) : ''; ?></textarea>
</div>

<div class="form-grid-2">
  <div class="form-group">
    <label for="facilities">Facilities Available</label>
    <input type="text" id="facilities" name="facilities" placeholder="Parking, Food Stalls, Restrooms..." value="<?= isset($place['facilities']) ? escape($place['facilities']) : ''; ?>">
  </div>

  <div class="form-group">
    <label for="entrance_fees">Entrance Fees</label>
    <input type="text" id="entrance_fees" name="entrance_fees" placeholder="e.g. Free, LKR 500, USD 10" value="<?= isset($place['entrance_fees']) ? escape($place['entrance_fees']) : ''; ?>">
  </div>
</div>

<div class="form-group">
  <label for="images">Place Images (Select up to 10 images)</label>
  <input type="file" id="images" name="images[]" multiple accept="image/png, image/jpeg, image/webp" class="admin-file-input">
  
  <?php if (isset($place_images) && count($place_images) > 0): ?>
    <div class="admin-image-preview-wrapper">
      <p class="admin-image-preview-title">Current Images (Check to Delete):</p>
      <div class="admin-image-preview-grid">
        <?php foreach ($place_images as $img): ?>
          <div class="admin-image-item">
            <img src="../<?= escape($img['image_path']); ?>" class="admin-image-thumb">
            <div class="admin-image-actions">
              <label class="admin-image-label">
                <input type="radio" name="primary_image" value="<?= $img['image_id']; ?>" <?= ($img['is_primary']) ? 'checked' : ''; ?>>
                Main
              </label>
              <label class="admin-image-label-danger">
                <input type="checkbox" name="delete_images[]" value="<?= $img['image_id']; ?>"> Delete
              </label>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>


<div class="form-grid-2">
  <div class="form-group">
    <label for="latitude">Latitude (Google Map)</label>
    <input type="number" step="any" id="latitude" name="latitude" placeholder="e.g. 7.2515" value="<?= isset($place['latitude']) ? escape($place['latitude']) : ''; ?>">
  </div>

  <div class="form-group">
    <label for="longitude">Longitude (Google Map)</label>
    <input type="number" step="any" id="longitude" name="longitude" placeholder="e.g. 80.4491" value="<?= isset($place['longitude']) ? escape($place['longitude']) : ''; ?>">
  </div>
</div>

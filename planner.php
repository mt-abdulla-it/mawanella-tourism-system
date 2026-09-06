<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';

if (!isset($_SESSION['day_plan'])) {
    $_SESSION['day_plan'] = [];
}

if (isset($_GET['action'])) {
    $act = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $status = 'invalid';

    if ($act === 'add') {
        if ($id > 0) {
            if (!in_array($id, $_SESSION['day_plan'])) {
                $_SESSION['day_plan'][] = $id;
                $status = 'success';
            } else {
                $status = 'exists';
            }
        }
        
        if (isset($_GET['ajax']) || isset($_GET['json'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => $status, 'count' => count($_SESSION['day_plan'])]);
            exit();
        }
        redirect("planner.php");
    }

    if ($act === 'remove' && $id > 0) {
        if (($key = array_search($id, $_SESSION['day_plan'])) !== false) {
            unset($_SESSION['day_plan'][$key]);
            $_SESSION['day_plan'] = array_values($_SESSION['day_plan']);
        }
        redirect("planner.php");
    }

    if ($act === 'clear') {
        $_SESSION['day_plan'] = [];
        redirect("planner.php");
    }
}

require_once 'includes/header.php';

$planList = [];
$totalKm = 0.00;

if (!empty($_SESSION['day_plan'])) {
    $placeIds = array_map('intval', $_SESSION['day_plan']);
    $inClause = implode(',', array_fill(0, count($placeIds), '?'));
    
    $sql = "SELECT p.*, c.category_name 
            FROM places p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.place_id IN ($inClause) 
            ORDER BY p.distance ASC";
            
    $stmt = mysqli_prepare($conn, $sql);
    $types = str_repeat('i', count($placeIds));
    mysqli_stmt_bind_param($stmt, $types, ...$placeIds);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($res)) {
        $planList[] = $row;
        $totalKm += $row['distance'];
    }
    mysqli_stmt_close($stmt);
}
?>

<div class="container planner-page-container">
  
  <div class="planner-header-wrapper">
    <div>
      <h1 class="planner-title"><i class="fa-solid fa-route"></i> Day Visit Itinerary Plan</h1>
      <p class="planner-subtitle">Selected tourist places for 1-day trip around Mawanella.</p>
    </div>

    <?php if (!empty($planList)): ?>
      <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn btn-primary"><i class="fa-solid fa-print"></i> Print Plan</button>
        <a href="planner.php?action=clear" class="btn btn-outline" onclick="return confirm('Clear all items from your plan?');">
          <i class="fa-solid fa-trash-can"></i> Clear All
        </a>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($planList)): ?>
    <div class="planner-summary">
      <div class="summary-item">
        <h3><?= count($planList); ?></h3>
        <p>Selected Places</p>
      </div>
      <div class="summary-item">
        <h3><?= number_format($totalKm, 1); ?> km</h3>
        <p>Total Distance</p>
      </div>
      <div class="summary-item">
        <h3>Full Day Trip</h3>
        <p>Estimated Duration</p>
      </div>
    </div>

    <div class="planner-list">
      <?php foreach ($planList as $index => $item): ?>
        <div class="planner-card">
          <div class="planner-item-content">
            <div class="planner-item-index">
              <?= $index + 1; ?>
            </div>
            <div>
              <h3 class="planner-item-title"><?= escape($item['name']); ?></h3>
              <p class="planner-item-meta">
                <span class="category-badge planner-category-badge">
                  <?= escape($item['category_name']); ?>
                </span>
                <i class="fa-solid fa-location-dot"></i> <?= number_format($item['distance'], 1); ?> km from Mawanella | 
                <i class="fa-regular fa-clock"></i> Visit: <?= escape($item['estimated_time']); ?>
              </p>
            </div>
          </div>
          <div class="planner-item-actions">
            <a href="details.php?id=<?= $item['place_id']; ?>" class="btn btn-outline btn-small">
              View
            </a>
            <a href="planner.php?action=remove&id=<?= $item['place_id']; ?>" class="btn btn-danger btn-small">
              Remove
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <div class="planner-empty-state">
      <i class="fa-solid fa-map-location planner-empty-icon"></i>
      <h2 class="planner-empty-title">No Places Added to Plan Yet</h2>
      <p class="planner-empty-text">
        Go back to the attractions page and click "Add to Day Plan" to start building your itinerary.
      </p>
      <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Browse Attractions</a>
    </div>
  <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>

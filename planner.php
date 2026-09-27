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

// set page title before including header
$page_title = 'Day Visit Itinerary Plan';

require_once 'includes/header.php';

$planList = [];
$totalKm = 0.00;
$totalMinutes = 0;

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
        
        // Calculate estimated time
        $est = strtolower($row['estimated_time'] ?? '');
        preg_match_all('/\d+(?:\.\d+)?/', $est, $matches);
        $nums = $matches[0];
        if (count($nums) > 0) {
            $sum = array_sum($nums);
            $avg = $sum / count($nums);
            if (strpos($est, 'min') !== false) {
                $totalMinutes += $avg;
            } else {
                $totalMinutes += $avg * 60;
            }
        }
    }
    mysqli_stmt_close($stmt);
}

// Format total duration string
$totalHours = floor($totalMinutes / 60);
$remainingMins = round($totalMinutes - ($totalHours * 60));
$durationStr = "0 mins";

if ($totalHours > 0 && $remainingMins > 0) {
    $durationStr = $totalHours . " hr" . ($totalHours > 1 ? "s" : "") . " " . $remainingMins . " min" . ($remainingMins > 1 ? "s" : "");
} elseif ($totalHours > 0) {
    $durationStr = $totalHours . " hr" . ($totalHours > 1 ? "s" : "");
} elseif ($remainingMins > 0) {
    $durationStr = $remainingMins . " min" . ($remainingMins > 1 ? "s" : "");
}

// Calculate total entrance fees across selected places
$totalEntranceFees = 0;
foreach ($planList as $p) {
    if (!empty($p['entrance_fees']) && strtolower($p['entrance_fees']) !== 'free') {
        preg_match_all('/\d+/', $p['entrance_fees'], $feeMatches);
        if (!empty($feeMatches[0])) {
            $totalEntranceFees += intval(implode('', $feeMatches[0]));
        }
    }
}
?>

<div class="container planner-page-container">
  
  <div class="planner-header-wrapper">
    <div>
      <h1 class="planner-title"><i class="fa-solid fa-route"></i> Day Visit Itinerary Plan</h1>
      <p class="planner-subtitle">Selected tourist places for 1-day trip around Mawanella.</p>
    </div>

    <?php if (!empty($planList)): ?>
      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button onclick="copyItinerarySummary()" class="btn btn-outline" style="border-color: var(--primary-color); color: var(--primary-color);">
          <i class="fa-solid fa-share-nodes"></i> Copy Summary
        </button>
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
        <h3><?= $durationStr; ?></h3>
        <p>Estimated Duration</p>
      </div>
    </div>

    <!-- Trip Budget & Fuel Expense Estimator -->
    <div class="planner-budget-card animate-fade-up" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 25px; margin-bottom: 30px; box-shadow: var(--shadow-sm);">
      <h3 style="color: var(--primary-color); font-size: 1.2rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-calculator"></i> Trip Budget & Fuel Expense Estimator
      </h3>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 6px;">Vehicle Type:</label>
          <select id="calcVehicle" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); font-weight: 500;" onchange="calculateBudget()">
            <option value="30">Motorbike / Scooter (~30 km/L)</option>
            <option value="25">Three-Wheeler / Tuk Tuk (~25 km/L)</option>
            <option value="14" selected>Car / Sedan (~14 km/L)</option>
            <option value="10">Van / SUV (~10 km/L)</option>
          </select>
        </div>
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 6px;">Fuel Price (LKR / Liter):</label>
          <input type="number" id="calcFuelPrice" value="370" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); font-weight: 500;" oninput="calculateBudget()">
        </div>
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 6px;">Trip Circuit Type:</label>
          <select id="calcTripType" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); font-weight: 500;" onchange="calculateBudget()">
            <option value="1">One-Way Route (1x Distance)</option>
            <option value="2" selected>Round-Trip Back to Town (2x Distance)</option>
          </select>
        </div>
      </div>

      <div style="margin-top: 20px; padding-top: 15px; border-top: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div style="font-size: 0.95rem; color: var(--text-main);">
          <span>Est. Fuel Needed: <strong id="resFuelLiters">0.0</strong> L</span> &nbsp;|&nbsp; 
          <span>Est. Fuel Cost: <strong id="resFuelCost">LKR 0</strong></span> &nbsp;|&nbsp; 
          <span>Total Entrance Fees: <strong>LKR <?= number_format($totalEntranceFees); ?></strong></span>
        </div>
        <div style="font-size: 1.15rem; font-weight: 800; color: var(--primary-color); background: rgba(15, 118, 110, 0.1); padding: 8px 18px; border-radius: 30px;">
          Total Est. Expense: <span id="resTotalBudget">LKR 0</span>
        </div>
      </div>
    </div>

    <!-- Interactive Map showing all selected places -->
    <div class="planner-map-section animate-fade-up">
      <h3 class="planner-map-title"><i class="fa-solid fa-map-location-dot"></i> Your Itinerary Map</h3>
      <div id="planner-map"></div>
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

<?php if (!empty($planList)): ?>
<!-- Planner map data and Google Maps -->
<script>
// places data for planner map
var plannerPlaces = <?= json_encode(array_map(function($p) {
    return [
        'name' => $p['name'],
        'lat' => floatval($p['latitude']),
        'lng' => floatval($p['longitude']),
        'distance' => number_format($p['distance'], 1),
        'time' => $p['estimated_time']
    ];
}, $planList)); ?>;

function calculateBudget() {
    var totalKm = <?= floatval($totalKm); ?>;
    var entranceFees = <?= floatval($totalEntranceFees); ?>;
    var vehicleSelect = document.getElementById('calcVehicle');
    var fuelPriceInput = document.getElementById('calcFuelPrice');
    var tripTypeSelect = document.getElementById('calcTripType');

    if (!vehicleSelect || !fuelPriceInput || !tripTypeSelect) return;

    var eff = parseFloat(vehicleSelect.value) || 14;
    var price = parseFloat(fuelPriceInput.value) || 370;
    var mult = parseFloat(tripTypeSelect.value) || 2;

    var totalDistance = totalKm * mult;
    var liters = totalDistance / eff;
    var fuelCost = liters * price;
    var grandTotal = fuelCost + entranceFees;

    document.getElementById('resFuelLiters').innerText = liters.toFixed(1);
    document.getElementById('resFuelCost').innerText = 'LKR ' + Math.round(fuelCost).toLocaleString();
    document.getElementById('resTotalBudget').innerText = 'LKR ' + Math.round(grandTotal).toLocaleString();
}
document.addEventListener('DOMContentLoaded', calculateBudget);

function copyItinerarySummary() {
    if (typeof plannerPlaces === 'undefined' || plannerPlaces.length === 0) return;
    
    var summary = "🚗 My Mawanella 1-Day Visit Itinerary\n";
    summary += "-------------------------------------\n";
    
    plannerPlaces.forEach(function(p, i) {
        summary += (i + 1) + ". " + p.name + " (" + p.distance + " km - Visit: " + p.time + ")\n";
    });
    
    summary += "-------------------------------------\n";
    summary += "📊 Total Distance: <?= number_format($totalKm, 1); ?> km\n";
    summary += "⏱️ Est. Duration: <?= escape($durationStr); ?>\n";
    
    var totalBudgetEl = document.getElementById('resTotalBudget');
    if (totalBudgetEl) {
        summary += "💰 Est. Expense: " + totalBudgetEl.innerText + "\n";
    }
    
    summary += "\nPlanned via Mawanella Tourism Day-Visit System";

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(summary).then(function() {
            if (typeof showToast === 'function') {
                showToast("Itinerary copied to clipboard!", "success");
            } else {
                alert("Itinerary copied to clipboard!");
            }
        }).catch(function(err) {
            console.error(err);
            if (typeof showToast === 'function') {
                showToast("Failed to copy itinerary.", "error");
            }
        });
    } else {
        var textarea = document.createElement("textarea");
        textarea.value = summary;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand("copy");
        document.body.removeChild(textarea);
        if (typeof showToast === 'function') {
            showToast("Itinerary copied to clipboard!", "success");
        } else {
            alert("Itinerary copied to clipboard!");
        }
    }
}
</script>
<script src="assets/js/map.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyATsyhCIT9v9ONMtxAUNa_7e_pQGQt6MxE&callback=initMap"></script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

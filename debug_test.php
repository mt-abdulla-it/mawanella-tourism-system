<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: PHP is working<br>";

session_start();
echo "Step 2: Session started<br>";

require_once __DIR__ . '/config/db.php';
echo "Step 3: DB connected<br>";

// Test a simple query
$res = mysqli_query($conn, "SELECT COUNT(*) as c FROM places");
$row = mysqli_fetch_assoc($res);
echo "Step 4: Places count = " . $row['c'] . "<br>";

// Test the INSERT bind_param types
$addName = "Debug Test Place";
$addCatId = 3;
$addDesc = "Test description.";
$addHours = "8:00 AM - 5:00 PM";
$addTips = "Tips.";
$addCuisine = "Cuisine.";
$addRoutes = "Routes.";
$addTTime = "20 mins";
$addDist = 3.5;
$addEst = "1 - 2 hours";
$addSafe = "8:00 AM - 4:00 PM";
$addFac = "Parking";
$addFees = "Free";

echo "Step 5: Variables set. Types string length = " . strlen("sissssssdssss") . ", param count = 13<br>";

// Clean first
mysqli_query($conn, "DELETE FROM places WHERE name = 'Debug Test Place'");

$stmtAdd = mysqli_prepare($conn, 
    "INSERT INTO places (name, category_id, description, opening_hours, travel_tips, local_cuisine, travel_routes, travel_time, distance, estimated_time, safe_visit_time, facilities, entrance_fees) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmtAdd) {
    echo "PREPARE FAILED: " . mysqli_error($conn) . "<br>";
} else {
    echo "Step 6: Statement prepared<br>";
    
    $bindResult = mysqli_stmt_bind_param($stmtAdd, "sissssssdssss", 
        $addName, $addCatId, $addDesc, $addHours, $addTips, $addCuisine, 
        $addRoutes, $addTTime, $addDist, $addEst, $addSafe, $addFac, $addFees
    );
    echo "Step 7: Bind result = " . ($bindResult ? "OK" : "FAIL: " . mysqli_stmt_error($stmtAdd)) . "<br>";
    
    if (mysqli_stmt_execute($stmtAdd)) {
        $pid = mysqli_insert_id($conn);
        echo "Step 8: INSERT OK, Place ID = $pid<br>";
        // Clean up
        mysqli_query($conn, "DELETE FROM places WHERE place_id = $pid");
        echo "Step 9: Cleaned up<br>";
    } else {
        echo "Step 8: EXECUTE FAILED: " . mysqli_stmt_error($stmtAdd) . "<br>";
    }
    mysqli_stmt_close($stmtAdd);
}

echo "<br><strong>All steps passed! The main test script should work.</strong>";
?>

<?php
// Database connection

$host = 'localhost';
$user = 'abdullah';
$pass = 'Tourism@2026';
$dbname = 'mawanella_tourism_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Disable mysqli exceptions temporarily for safe migration queries
$prevReportMode = MYSQLI_REPORT_OFF;
mysqli_report(MYSQLI_REPORT_OFF);

@mysqli_query($conn, "UPDATE admins SET full_name = 'Abdulla MT', email = 'abdulla@mawanella.lk', role = 'Super Admin' WHERE admin_id = 1 AND (full_name IN ('System Administrator', 'Abdulla MT (Super Admin)') OR full_name IS NULL OR full_name = '')");

// Check if map_embed column exists, add it if missing
$mapColExists = false;
$colCheck = @mysqli_query($conn, "SHOW COLUMNS FROM places LIKE 'map_embed'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) {
    $mapColExists = true;
} else {
    // Column doesn't exist, create it
    $added = @mysqli_query($conn, "ALTER TABLE places ADD COLUMN map_embed TEXT DEFAULT NULL AFTER longitude");
    if ($added) {
        $mapColExists = true;
    }
}

// Only run map_embed queries if the column exists
if ($mapColExists) {
    // Clean up any non-iframe data in map_embed
    @mysqli_query($conn, "UPDATE places SET facilities = map_embed, map_embed = NULL WHERE map_embed IS NOT NULL AND map_embed NOT LIKE '%<iframe%'");

    // Sync Saradiel Village map location embed
    $saradielIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.823631303603!2d80.41994048977666!3d7.260903500287627!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3149aa1a6e73d%3A0xcd5efe099ce2339a!2sSaradiel%20Resort!5e0!3m2!1sen!2slk!4v1790530350253!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtS = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.26090350, longitude = 80.41994050 WHERE place_id = 1 OR name LIKE '%Saradiel%'");
    if ($stmtS) {
        mysqli_stmt_bind_param($stmtS, "s", $saradielIframe);
        mysqli_stmt_execute($stmtS);
        mysqli_stmt_close($stmtS);
    }

    // Sync Bo Ella Waterfall map location embed
    $boEllaIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6918112287754!2d80.4378765!3d7.2758658999999986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae314c147ce1ad1%3A0x2e6aa8faa08ade6e!2sBo%20Ella!5e0!3m2!1sen!2slk!4v1790532300198!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtB = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.27586590, longitude = 80.43787650 WHERE place_id = 2 OR name LIKE '%Bo Ella%'");
    if ($stmtB) {
        mysqli_stmt_bind_param($stmtB, "s", $boEllaIframe);
        mysqli_stmt_execute($stmtB);
        mysqli_stmt_close($stmtB);
    }

    // Sync Mawela Walagamba Cave Temple map location embed
    $mawelaIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d572.5318164046174!2d80.49913614849224!3d7.25197093447644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b2aeda1cdd7%3A0x6cc65af288566da3!2sMawela%20rajamaha%20viharaya!5e0!3m2!1sen!2slk!4v1790533631850!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtM = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.25197093, longitude = 80.49913615 WHERE place_id = 3 OR name LIKE '%Mawela%'");
    if ($stmtM) {
        mysqli_stmt_bind_param($stmtM, "s", $mawelaIframe);
        mysqli_stmt_execute($stmtM);
        mysqli_stmt_close($stmtM);
    }

    // Sync Kadugannawa Tunnel map location embed
    $kadugannawaIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63325.68093522701!2d80.447771496197!3d7.257331793752407!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36c780b2311d5%3A0xb4eb84c60d8aacc5!2zS2FkdWdhbm5hd2EgVHVubmVsIHwg4Laa4Lap4LeU4Lac4Lax4LeK4Lax4LeP4LeAIOC2i-C2uOC2nw!5e0!3m2!1sen!2slk!4v1790533939429!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtK = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.25733179, longitude = 80.44777150 WHERE place_id = 4 OR name LIKE '%Kadugannawa Tunnel%'");
    if ($stmtK) {
        mysqli_stmt_bind_param($stmtK, "s", $kadugannawaIframe);
        mysqli_stmt_execute($stmtK);
        mysqli_stmt_close($stmtK);
    }

    // Sync Kadugannawa Train Station map location embed
    $stationIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63325.94921547175!2d80.4413160461926!3d7.255425444229544!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b8ca7e9f571%3A0xb65ee15931c748af!2sKadugannawa!5e0!3m2!1sen!2slk!4v1790534677468!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtKS = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.25542544, longitude = 80.44131605 WHERE place_id = 5 OR name LIKE '%Kadugannawa Train Station%'");
    if ($stmtKS) {
        mysqli_stmt_bind_param($stmtKS, "s", $stationIframe);
        mysqli_stmt_execute($stmtKS);
        mysqli_stmt_close($stmtKS);
    }

    // Sync Meeyan Ella Waterfall map location embed
    $meeyanIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31661.85280833187!2d80.43348940625259!3d7.271352735071465!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae36b28faa54de1%3A0xec363027dc428652!2sMeeyan%20Ella%20Waterfall%20(Walagoda)!5e0!3m2!1sen!2slk!4v1790534783743!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtME = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.27135274, longitude = 80.43348941 WHERE place_id = 6 OR name LIKE '%Meeyan Ella%'");
    if ($stmtME) {
        mysqli_stmt_bind_param($stmtME, "s", $meeyanIframe);
        mysqli_stmt_execute($stmtME);
        mysqli_stmt_close($stmtME);
    }

    // Sync Pinnawala Zoo map location embed
    $zooIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63320.14844650705!2d80.31482219696042!3d7.296534182838259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae315ca19d4f40d%3A0x3d3bdad28c9b3625!2sPinnawala%20Zoo!5e0!3m2!1sen!2slk!4v1790534903839!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtZ = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.29653418, longitude = 80.31482220 WHERE place_id = 7 OR name LIKE '%Pinnawala Zoo%'");
    if ($stmtZ) {
        mysqli_stmt_bind_param($stmtZ, "s", $zooIframe);
        mysqli_stmt_execute($stmtZ);
        mysqli_stmt_close($stmtZ);
    }

    // Sync Pinnawala Elephant Orphanage map location embed
    $elephantIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63319.539894220754!2d80.31675338745116!3d7.300833512124203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae315c83a2c0273%3A0xa5744b525ada1e6c!2sPinnawala%20Elephant%20Orphanage!5e0!3m2!1sen!2slk!4v1790535001087!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtE = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.30083351, longitude = 80.31675339 WHERE place_id = 8 OR name LIKE '%Elephant Orphanage%'");
    if ($stmtE) {
        mysqli_stmt_bind_param($stmtE, "s", $elephantIframe);
        mysqli_stmt_execute($stmtE);
        mysqli_stmt_close($stmtE);
    }

    // Sync Asupini Ella Waterfall map location embed
    $asupiniIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126690.27598695076!2d80.34147262573246!3d7.1177564881039865!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3731b770bc943%3A0x6b11b25d846475bb!2sAsupini%20Ella%20Falls!5e0!3m2!1sen!2slk!4v1790535084512!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtA = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.11775649, longitude = 80.34147263 WHERE place_id = 9 OR name LIKE '%Asupini Ella%'");
    if ($stmtA) {
        mysqli_stmt_bind_param($stmtA, "s", $asupiniIframe);
        mysqli_stmt_execute($stmtA);
        mysqli_stmt_close($stmtA);
    }

    // Sync Ambuluwawa Biodiversity Complex map location embed
    $ambuluwawaIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63332.959051200065!2d80.45450644607732!3d7.205437056743686!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae371f92e975b5d%3A0xa383ec30d0ad2ef6!2sAmbuluwawa%20Biodiversity%20Complex!5e0!3m2!1sen!2slk!4v1790535195015!5m2!1sen!2slk" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    $stmtAM = @mysqli_prepare($conn, "UPDATE places SET map_embed = ?, latitude = 7.20543706, longitude = 80.45450645 WHERE place_id = 10 OR name LIKE '%Ambuluwawa%'");
    if ($stmtAM) {
        mysqli_stmt_bind_param($stmtAM, "s", $ambuluwawaIframe);
        mysqli_stmt_execute($stmtAM);
        mysqli_stmt_close($stmtAM);
    }
}

// Ensure all 10 places have valid latitude and longitude
$defaultCoords = [
    1 => ['lat' => 7.26090350, 'lng' => 80.41994050],
    2 => ['lat' => 7.27586590, 'lng' => 80.43787650],
    3 => ['lat' => 7.25197093, 'lng' => 80.49913615],
    4 => ['lat' => 7.25733179, 'lng' => 80.44777150],
    5 => ['lat' => 7.25542544, 'lng' => 80.44131605],
    6 => ['lat' => 7.27135274, 'lng' => 80.43348941],
    7 => ['lat' => 7.29653418, 'lng' => 80.31482220],
    8 => ['lat' => 7.30083351, 'lng' => 80.31675339],
    9 => ['lat' => 7.11775649, 'lng' => 80.34147263],
    10 => ['lat' => 7.20543706, 'lng' => 80.45450645],
];
foreach ($defaultCoords as $pid => $c) {
    @mysqli_query($conn, "UPDATE places SET latitude = {$c['lat']}, longitude = {$c['lng']} WHERE place_id = {$pid}");
}

require_once __DIR__ . '/helpers.php';
?>
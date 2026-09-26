<?php
// db connection script
// student: abdulla mt (E2410182)

$host = 'localhost';
$user = 'abdullah';
$pass = 'Tourism@2026';
$dbname = 'mawanella_tourism_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Sync categories & place data strictly matching Table 2 specs
$checkCat = mysqli_query($conn, "SELECT category_name FROM categories WHERE category_id = 1");
if ($checkCat && $row = mysqli_fetch_assoc($checkCat)) {
    if ($row['category_name'] !== 'Cultural / Heritage Places') {
        mysqli_query($conn, "UPDATE categories SET category_name = 'Cultural / Heritage Places' WHERE category_id = 1");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Nature / Sightseeing Places' WHERE category_id = 2");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Religious / Heritage Places' WHERE category_id = 3");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Heritage / Monument Places' WHERE category_id = 4");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Heritage / Infrastructure Places' WHERE category_id = 5");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Nature / Waterfall Places' WHERE category_id = 6");
        mysqli_query($conn, "UPDATE categories SET category_name = 'Nature / Wildlife Places' WHERE category_id = 7");

        mysqli_query($conn, "UPDATE places SET category_id = 1, description = 'Cultural village and resort that pays homage to the great Sri Lankan hero, Saradiel, with historical significance.' WHERE place_id = 1");
        mysqli_query($conn, "UPDATE places SET category_id = 2, description = 'A local waterfall that can be photographed as it is surrounded by lush greenery and is easily accessible.' WHERE place_id = 2");
        mysqli_query($conn, "UPDATE places SET category_id = 3, description = 'Ancient cave temple with Buddhist heritage.' WHERE place_id = 3");
        mysqli_query($conn, "UPDATE places SET category_id = 4, description = 'Historic rock tunnel constructed during the British rule and is one of the landmarks along the Colombo – Kandy Road.' WHERE place_id = 4");
        mysqli_query($conn, "UPDATE places SET category_id = 5, description = 'Colonial styled railway station with scenic views of the surroundings.' WHERE place_id = 5");
        mysqli_query($conn, "UPDATE places SET category_id = 6, description = 'Scenic waterfall that can be visited through a trek, which is located near the historic railway line.' WHERE place_id = 6");
        mysqli_query($conn, "UPDATE places SET category_id = 7, description = 'Zoological garden located near the Elephant Orphanage, houses a variety of endemic and exotic animal species.' WHERE place_id = 7");
        mysqli_query($conn, "UPDATE places SET category_id = 7, description = 'World renowned center for taking care of orphaned elephants which includes their feeding and allowing them to bathe in the river' WHERE place_id = 8");
        mysqli_query($conn, "UPDATE places SET category_id = 6, description = 'Majestic water fall with local mythology and legend associated with it, provides a challenge and great natural sight.' WHERE place_id = 9");
        mysqli_query($conn, "UPDATE places SET category_id = 2, description = 'Multireligious Tower and Biodiversity Complex located atop a mountain that offers a 360 degree view of the surroundings.' WHERE place_id = 10");
    }
}

// Auto-migrate additional details for admins table if missing
$checkCol = @mysqli_query($conn, "SHOW COLUMNS FROM admins LIKE 'full_name'");
if ($checkCol && mysqli_num_rows($checkCol) === 0) {
    @mysqli_query($conn, "ALTER TABLE admins ADD COLUMN full_name VARCHAR(100) DEFAULT 'Abdulla MT'");
    @mysqli_query($conn, "ALTER TABLE admins ADD COLUMN email VARCHAR(100) DEFAULT 'abdulla@mawanella.lk'");
    @mysqli_query($conn, "ALTER TABLE admins ADD COLUMN role VARCHAR(50) DEFAULT 'Super Admin'");
}

// Ensure super admin #1 has student full name 'Abdulla MT'
@mysqli_query($conn, "UPDATE admins SET full_name = 'Abdulla MT', email = 'abdulla@mawanella.lk', role = 'Super Admin' WHERE admin_id = 1 AND (full_name = 'System Administrator' OR full_name IS NULL OR full_name = '')");

require_once __DIR__ . '/helpers.php';
?>

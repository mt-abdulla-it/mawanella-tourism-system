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

require_once __DIR__ . '/helpers.php';
?>

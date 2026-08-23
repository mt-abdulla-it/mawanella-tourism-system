<?php
// helper functions for mawanella tourism project
// student: abdulla mt (E2410182)

function escape($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function check_admin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect('login.php');
    }
}

function generate_csrf() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function get_categories($conn) {
    $res = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");
    $list = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $list[] = $row;
        }
    }
    return $list;
}
?>

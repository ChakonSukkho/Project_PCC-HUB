<?php
session_start();
require_once('../config.php');

// If no admin session, force login
if (!isset($_SESSION['admin_id'])) {
    header("Location: $link/admin/login.php");
    exit();
}

// Check if admin exists in database
$admin_id = $connect->real_escape_string($_SESSION['admin_id']);
$sql_login = "SELECT * FROM admins WHERE admin_id = '$admin_id'";
$result_login = $connect->query($sql_login);

if ($result_login && $result_login->num_rows > 0) {
    $rows_login = $result_login->fetch_assoc();
} else {
    // Invalid session, clear it and go to login
    session_unset();
    session_destroy();
    header('Location: '.$link.'/admin/login.php');
    exit();
}

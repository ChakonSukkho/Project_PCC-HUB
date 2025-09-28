<?php
include('../config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cert_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cert_id <= 0) {
    die("Invalid certificate ID.");
}

// Get certificate info
$sql = "SELECT certificate_file FROM user_certificates 
        WHERE certificate_id = ? AND user_id = ? AND status = 'active'";
$stmt = $connect->prepare($sql);
$stmt->bind_param("ii", $cert_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Certificate not found.");
}

$cert = $result->fetch_assoc();
$filePath = "../" . $cert['certificate_file'];

if (!file_exists($filePath)) {
    die("Certificate file missing on server.");
}

// Show PDF in browser
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"certificate.pdf\"");
readfile($filePath);
exit();
?>

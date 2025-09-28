<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    if ($id) {
        $stmt = $connect->prepare("DELETE FROM announcements WHERE announcement_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Delete failed"]);
        }
    }
}
?>

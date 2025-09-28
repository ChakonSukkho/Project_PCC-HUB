<?php
include('../config.php'); // adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $priority = $_POST['priority'] ?? 'normal';
    $content = $_POST['content'] ?? '';

    if ($title && $category && $content) {
        $stmt = $connect->prepare("INSERT INTO announcements (title, category, priority, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $category, $priority, $content);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Database error"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Missing fields"]);
    }
}
?>

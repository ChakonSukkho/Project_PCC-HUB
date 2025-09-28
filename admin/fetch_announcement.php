<?php
include('../config.php');

$result = $connect->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = [];

while ($row = $result->fetch_assoc()) {
    $announcements[] = [
        "id" => $row['announcement_id'],
        "title" => $row['title'],
        "category" => $row['category'],
        "priority" => $row['priority'],
        "content" => $row['content'],
        "time" => date("M d, Y H:i", strtotime($row['created_at'])),
        "timestamp" => strtotime($row['created_at']) * 1000
    ];
}

echo json_encode($announcements);
?>

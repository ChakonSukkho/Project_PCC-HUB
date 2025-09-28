<?php
include("../config.php");
$user_id = 1; // replace with $_SESSION["user_id"]

$sql = "SELECT session_date, total_distance, total_duration 
        FROM user_running_sessions 
        WHERE user_id = ? ORDER BY session_id DESC LIMIT 5";
$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$runs = [];
while($row = $res->fetch_assoc()) {
    $runs[] = $row;
}
echo json_encode($runs);
?>
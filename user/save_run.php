<?php
include("config.php"); // your DB connection

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

$user_id = $input["user_id"];
$run = $input["run"];

// Prepare query
$sql = "INSERT INTO user_running_sessions 
        (user_id, session_date, start_time, end_time, total_duration, total_distance, avg_pace, avg_speed, calories_burned, route_data)
        VALUES (?, CURDATE(), FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?, ?, ?, ?, ?, ?)";

$stmt = $connect->prepare($sql);
$stmt->bind_param(
    "issddssds",
    $user_id,
    $run["startTime"],
    $run["endTime"],
    $run["duration"],
    $run["distance"],
    $run["pace"],
    $run["speed"],
    $run["calories"],
    json_encode($run["coords"])
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>
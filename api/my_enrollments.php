<?php
include_once '../config.php';
header('Content-Type: application/json');
session_start(); if(!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
$user = $_SESSION['user_id'];
$sql = "SELECT e.id as enroll_id, p.id, p.name, p.category, p.duration_text, e.progress_percent FROM enrollments e JOIN programs p ON p.id = e.program_id WHERE e.user_id = ? ORDER BY e.enrolled_at DESC";
$stmt = $connect->prepare($sql);
$stmt->bind_param('i',$user); $stmt->execute(); $res = $stmt->get_result();
$out = [];
while($r = $res->fetch_assoc()) $out[] = $r;
echo json_encode($out);
?>
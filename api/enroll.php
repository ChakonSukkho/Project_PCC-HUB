<?php

// Enroll current user into a program
include_once '../config.php';
header('Content-Type: application/json');
session_start();
if(!isset($_SESSION['user_id'])){ echo json_encode(['success'=>false,'message'=>'Not logged in']); exit; }
$body = json_decode(file_get_contents('php://input'), true);
$pid = intval($body['program_id'] ?? 0);
if(!$pid){ echo json_encode(['success'=>false,'message'=>'Invalid program']); exit; }
$user = $_SESSION['user_id'];
// Prevent duplicate
$check = $connect->prepare("SELECT id FROM enrollments WHERE user_id=? AND program_id=?");
$check->bind_param('ii',$user,$pid); $check->execute(); $g = $check->get_result();
if($g->num_rows){ echo json_encode(['success'=>false,'message'=>'Already enrolled']); exit; }
$stmt = $connect->prepare("INSERT INTO enrollments (user_id, program_id, progress_percent, enrolled_at) VALUES (?,?,0,NOW())");
$stmt->bind_param('ii',$user,$pid);
if($stmt->execute()) echo json_encode(['success'=>true]); else echo json_encode(['success'=>false,'message'=>'DB error']);

?>
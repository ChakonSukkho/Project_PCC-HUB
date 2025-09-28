<?php

include_once '../config.php';
header('Content-Type: application/json');
session_start(); if(!isset($_SESSION['user_id'])){ echo json_encode(['success'=>false,'message'=>'Not logged']); exit; }
$body = json_decode(file_get_contents('php://input'), true);
enroll_id = intval($body['enroll_id'] ?? 0);
if(!$enroll_id){ echo json_encode(['success'=>false,'message'=>'Invalid']); exit; }
// Validate ownership
$stmt = $connect->prepare("SELECT user_id, program_id FROM enrollments WHERE id = ?");
$stmt->bind_param('i', $enroll_id); $stmt->execute(); $res = $stmt->get_result();
if($res->num_rows == 0){ echo json_encode(['success'=>false,'message'=>'Not found']); exit; }\$row = $res->fetch_assoc();
if($row['user_id'] != $_SESSION['user_id']){ echo json_encode(['success'=>false,'message'=>'Forbidden']); exit; }
// Mark complete (set 100)
$u = $_SESSION['user_id']; $p = $row['program_id'];
$upd = $connect->prepare("UPDATE enrollments SET progress_percent = 100, completed_at = NOW() WHERE id = ?");
$upd->bind_param('i',$enroll_id); $ok = $upd->execute();
if(!$ok) { echo json_encode(['success'=>false,'message'=>'DB']); exit; }
// Generate ce

?>
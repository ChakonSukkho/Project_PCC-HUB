<?php
// Returns JSON array of available programs filterned by category / seacrh

include_once '../config.php'; // adjust path
header('Content-Type: application/json; charset=utf-8');
$category = $_GET['category'] ?? 'all';
$q = $_GET['q'] ?? '';
$params = [];
$sql = "SELECT id, name, category, description, duration_text, difficulty FROM programs WHERE 1=1";
if($category !== 'all') { $sql .= " AND category = ?"; $params[] = $category; }
if($q) { $sql .= " AND (name LIKE ? OR description LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
$stmt = $connect->prepare($sql);
if($params){ $stmt->bind_param(str_repeat('s', count($params)), ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while($r = $res->fetch_assoc()) $out[] = $r;
echo json_encode($out);

?>
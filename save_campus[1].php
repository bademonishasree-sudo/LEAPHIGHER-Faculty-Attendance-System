<?php
require_once __DIR__ . '/config.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
verify_csrf();
$name = trim($_POST['campus_name'] ?? '');
$lat = filter_var($_POST['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
$lon = filter_var($_POST['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
$radius = filter_var($_POST['radius_meters'] ?? null, FILTER_VALIDATE_INT);
if ($name === '' || $lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180 || $radius === false || $radius < 20 || $radius > 2000) {
    exit('Invalid campus details. <a href="dashboard.php">Back</a>');
}
$id = $pdo->query("SELECT id FROM campus_locations ORDER BY id LIMIT 1")->fetchColumn();
if ($id) {
    $stmt = $pdo->prepare("UPDATE campus_locations SET campus_name=?,latitude=?,longitude=?,radius_meters=? WHERE id=?");
    $stmt->execute([$name,$lat,$lon,$radius,$id]);
} else {
    $stmt = $pdo->prepare("INSERT INTO campus_locations (campus_name,latitude,longitude,radius_meters) VALUES (?,?,?,?)");
    $stmt->execute([$name,$lat,$lon,$radius]);
}
header('Location: dashboard.php'); exit;

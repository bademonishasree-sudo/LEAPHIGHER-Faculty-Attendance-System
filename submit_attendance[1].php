<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'faculty') {
    http_response_code(401); echo json_encode(['success'=>false,'message'=>'Please sign in with a faculty account.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Invalid request method.']); exit;
}
$sent = $_POST['csrf_token'] ?? '';
if (!$sent || !hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
    http_response_code(419); echo json_encode(['success'=>false,'message'=>'Security token expired. Refresh and try again.']); exit;
}
$token = trim($_POST['token'] ?? '');
$lat = filter_var($_POST['latitude'] ?? null, FILTER_VALIDATE_FLOAT);
$lon = filter_var($_POST['longitude'] ?? null, FILTER_VALIDATE_FLOAT);
if (!preg_match('/^[a-f0-9]{64}$/', $token) || $lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    echo json_encode(['success'=>false,'message'=>'Invalid QR code or location data.']); exit;
}
$stmt = $pdo->prepare("SELECT * FROM qr_sessions WHERE token=? LIMIT 1");
$stmt->execute([$token]); $session = $stmt->fetch();
if (!$session) { echo json_encode(['success'=>false,'message'=>'Invalid QR code.']); exit; }

$now = new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata'));
$start = new DateTimeImmutable($session['starts_at'], new DateTimeZone('Asia/Kolkata'));
$end = new DateTimeImmutable($session['ends_at'], new DateTimeZone('Asia/Kolkata'));
if ($now < $start || $now > $end) {
    echo json_encode(['success'=>false,'message'=>'Attendance rejected: outside scheduled time.']); exit;
}
$campus = $pdo->query("SELECT * FROM campus_locations ORDER BY id LIMIT 1")->fetch();
if (!$campus || ((float)$campus['latitude'] === 0.0 && (float)$campus['longitude'] === 0.0)) {
    echo json_encode(['success'=>false,'message'=>'Admin must configure the actual campus GPS coordinates first.']); exit;
}
$distance = distance_meters((float)$campus['latitude'],(float)$campus['longitude'],(float)$lat,(float)$lon);
if ($distance > (int)$campus['radius_meters']) {
    echo json_encode(['success'=>false,'message'=>'Attendance rejected: outside authorized LEAPHIGHER campus.']); exit;
}
try {
    $insert = $pdo->prepare("INSERT INTO faculty_attendance (faculty_user_id,qr_session_id,scanned_at,latitude,longitude,distance_meters,status) VALUES (?,?,?,?,?,?,'PRESENT')");
    $insert->execute([$_SESSION['user_id'],$session['id'],$now->format('Y-m-d H:i:s'),$lat,$lon,$distance]);
    echo json_encode(['success'=>true,'message'=>'Attendance successfully submitted for '.$session['subject'].' at '.$now->format('d M Y, h:i:s A').'.']);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        echo json_encode(['success'=>false,'message'=>'Attendance already submitted for this session.']);
    } else {
        http_response_code(500); echo json_encode(['success'=>false,'message'=>'Server error while saving attendance.']);
    }
}

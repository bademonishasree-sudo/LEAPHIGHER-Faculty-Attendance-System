<?php
require_once __DIR__ . '/config.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }
verify_csrf();
$subject = trim($_POST['subject'] ?? '');
$startRaw = $_POST['starts_at'] ?? '';
$endRaw = $_POST['ends_at'] ?? '';
$start = DateTime::createFromFormat('Y-m-d\TH:i', $startRaw);
$end = DateTime::createFromFormat('Y-m-d\TH:i', $endRaw);
if ($subject === '' || !$start || !$end || $end <= $start) { exit('Enter a subject and a valid start/end time. <a href="dashboard.php">Back</a>'); }
$token = bin2hex(random_bytes(32));
$stmt = $pdo->prepare("INSERT INTO qr_sessions (subject,token,starts_at,ends_at,created_by) VALUES (?,?,?,?,?)");
$stmt->execute([$subject,$token,$start->format('Y-m-d H:i:s'),$end->format('Y-m-d H:i:s'),$_SESSION['user_id']]);
header('Location: dashboard.php'); exit;

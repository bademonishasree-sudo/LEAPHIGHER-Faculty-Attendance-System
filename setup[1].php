<?php
require_once __DIR__ . '/config.php';

$check = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role='admin'")->fetch();
if ((int)$check['total'] === 0) {
    $insert = $pdo->prepare("INSERT INTO users (login_id, full_name, password_hash, role, department) VALUES (?, ?, ?, ?, ?)");
    $insert->execute(['admin', 'LEAPHIGHER Administrator', password_hash('Admin@123', PASSWORD_DEFAULT), 'admin', 'Administration']);
}
$checkFaculty = $pdo->prepare("SELECT COUNT(*) AS total FROM users WHERE login_id=?");
$checkFaculty->execute(['FAC001']);
if ((int)$checkFaculty->fetch()['total'] === 0) {
    $insert = $pdo->prepare("INSERT INTO users (login_id, full_name, password_hash, role, department) VALUES (?, ?, ?, ?, ?)");
    $insert->execute(['FAC001', 'Prof. Ravi (Demo)', password_hash('Faculty@123', PASSWORD_DEFAULT), 'faculty', 'Computer Science']);
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>LEAPHIGHER Setup</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="center-page"><main class="panel narrow"><h1>LEAPHIGHER setup complete</h1><p>Demo accounts are ready:</p><p><b>Admin:</b> admin / Admin@123</p><p><b>Faculty:</b> FAC001 / Faculty@123</p><p class="warning">Change these passwords before real use. Delete setup.php after setup.</p><a class="btn" href="index.php">Go to login</a></main></body></html>

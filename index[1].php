<?php
require_once __DIR__ . '/config.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $loginId = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login_id=? AND is_active=1 LIMIT 1");
    $stmt->execute([$loginId]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['login_id'] = $user['login_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['department'] = $user['department'];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Invalid ID or password, or account is inactive.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>LEAPHIGHER | Login</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="center-page"><main class="panel login-panel"><div class="brand-mark">L</div><p class="eyebrow">LEAPHIGHER COLLEGE</p><h1>Faculty Attendance</h1><p class="muted">Secure attendance with identity, location and time validation</p>
<?php if ($error): ?><div class="alert error"><?=h($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=h(csrf_token())?>"><label>Faculty / Admin ID</label><input name="login_id" required autocomplete="username" placeholder="Enter your registered ID"><label>Password</label><input type="password" name="password" required autocomplete="current-password" placeholder="Enter password"><button class="btn full" type="submit">Sign in</button></form>
<p class="small muted">Demo accounts are shown in README.md after running setup.php.</p></main></body></html>

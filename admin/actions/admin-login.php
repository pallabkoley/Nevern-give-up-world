<?php
// admin/actions/admin-login.php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/admin-login');
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['login_error'] = 'Your session expired. Please try again.';
    header('Location: /admin/admin-login');
    exit;
}

$identifier = trim((string) ($_POST['identifier'] ?? ''));
$password   = (string) ($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
    $_SESSION['login_error']      = 'Please enter your username/email and password.';
    $_SESSION['login_identifier'] = $identifier;
    header('Location: /admin/admin-login');
    exit;
}

$now      = time();
$attempts = is_array($_SESSION['login_attempts'] ?? null) ? $_SESSION['login_attempts'] : [];
$attempts = array_values(array_filter($attempts, fn($t) => is_int($t) && $t > $now - 900));

// if (count($attempts) >= 5) {
//     $_SESSION['login_error'] = 'Too many failed attempts. Please wait 15 minutes.';
//     header('Location: /admin/admin-login');
//     exit;
// }

// ডাটাবেস কানেকশন
require_once __DIR__ . '/../../includes/db.php';

try {
    $stmt = $pdo->prepare(
        'SELECT id, name, email, username, password, role, status
         FROM admin_users
         WHERE username = ? OR email = ?
         LIMIT 1'
    );
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();
} catch (Throwable $ex) {
    error_log('Admin login DB error: ' . $ex->getMessage());
    $_SESSION['login_error'] = 'A system error occurred. Please try again later.';
    header('Location: /admin/admin-login');
    exit;
}

$valid = $user
    && $user['status'] === 'active'
    && password_verify($password, $user['password']);

if (!$valid) {
    $attempts[]                   = $now;
    $_SESSION['login_attempts']   = $attempts;
    $_SESSION['login_error']      = 'Invalid credentials. Please try again.';
    $_SESSION['login_identifier'] = $identifier;
    header('Location: /admin/admin-login');
    exit;
}

// সেশন সেট
session_regenerate_id(true);

$_SESSION['admin_id']          = (int) $user['id'];
$_SESSION['admin_name']        = $user['name'];
$_SESSION['admin_email']       = $user['email'];
$_SESSION['admin_username']    = $user['username'];
$_SESSION['admin_role']        = $user['role'];
$_SESSION['admin_logged_in_at'] = $now;

unset($_SESSION['login_attempts'], $_SESSION['login_error'], $_SESSION['login_identifier']);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

try {
    $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
} catch (Throwable $ex) {
    error_log('Admin last_login update failed: ' . $ex->getMessage());
}

// সফলভাবে লগইন হলে .php ছাড়া ড্যাশবোর্ডে পাঠাবে
header('Location: /admin/dashboard');
exit;
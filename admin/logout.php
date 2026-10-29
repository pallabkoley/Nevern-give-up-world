<?php
// admin/logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session data
session_unset();
session_destroy();

// Clear remember-me cookie
if (isset($_COOKIE['ngup_admin_user'])) {
    setcookie('ngup_admin_user', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.php?logout=1');
exit;
?>
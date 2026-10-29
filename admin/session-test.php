<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

echo "<h2>Session Test</h2>";

if (isset($_GET['set'])) {

    $_SESSION['test'] = 'SESSION WORKING';
    $_SESSION['test_time'] = time();

    echo "<p>Session value has been SET.</p>";
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo '<p><a href="session-test.php?set=1">Set Session</a></p>';
echo '<p><a href="session-test.php">Refresh / Read Session</a></p>';
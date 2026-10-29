<?php
// admin/debug.php - DELETE THIS FILE AFTER DEBUGGING

require_once '../includes/db.php';

echo "<h2>🔍 Login Debugger</h2>";

// Step 1: Check DB connection
echo "<p>✅ Database connected successfully.</p>";

// Step 2: Fetch the admin user
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin' OR email = 'admin@nevergiveupworld.com' LIMIT 1");
$stmt->execute();
$admin = $stmt->fetch();

if (!$admin) {
    die("<p style='color:red;'>❌ <strong>No admin user found in the database.</strong> Please run <code>setup-admin.php</code> first.</p>");
}

echo "<p>✅ Admin user found: <strong>" . htmlspecialchars($admin['username']) . "</strong></p>";
echo "<p>📧 Email: " . htmlspecialchars($admin['email']) . "</p>";
echo "<p>🔐 Role: " . htmlspecialchars($admin['role']) . "</p>";
echo "<p>📌 Status: " . htmlspecialchars($admin['status']) . "</p>";
echo "<p>🔑 Stored Password Hash: <br><code style='background:#eee;padding:5px;display:block;word-break:break-all;'>" . htmlspecialchars($admin['password']) . "</code></p>";

// Step 3: Test a password against the stored hash
$testPassword = 'Admin@123'; // <-- Change this to whatever you are typing in the login form
$verify = password_verify($testPassword, $admin['password']);

if ($verify) {
    echo "<p style='color:green;'>✅ <strong>SUCCESS!</strong> The password '<em>$testPassword</em>' matches the stored hash.</p>";
    echo "<p>If the login page still fails, the issue is elsewhere (check <code>login.php</code> for <code>header()</code> errors or output before redirect).</p>";
} else {
    echo "<p style='color:red;'>❌ <strong>FAILURE!</strong> The password '<em>$testPassword</em>' does NOT match the stored hash.</p>";
    echo "<p>👉 <strong>The hash stored in your database is invalid.</strong> You need to regenerate it.</p>";
    echo "<hr>";
    
    // Generate a fresh hash for the admin password
    $newHash = password_hash('Admin@123', PASSWORD_BCRYPT);
    echo "<h3>🛠️ Fix: Run this SQL in your database</h3>";
    echo "<pre style='background:#f4f4f4;padding:15px;border-radius:8px;overflow:auto;'>UPDATE admin_users SET password = '$newHash' WHERE username = 'admin';</pre>";
    echo "<p>Or simply visit <a href='../setup-admin.php'><strong>setup-admin.php</strong></a> to auto-update the password.</p>";
}

echo "<hr>";
echo "<p><em>Delete this file (debug.php) once you've fixed the issue.</em></p>";
?>
<?php
// setup-admin.php - Run ONCE to create the admin account, then DELETE this file.

require_once 'includes/db.php';

// Credentials for the first admin
$name     = 'Super Admin';
$email    = 'admin@nevergiveupworld.com';
$username = 'admin';
$plainPassword = 'Admin@123'; // Change this after first login!

// Generate secure password hash
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

try {
    // Check if admin already exists
    $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = :username OR email = :email LIMIT 1");
    $check->execute([':username' => $username, ':email' => $email]);

    if ($check->fetch()) {
        // Update existing admin's password (in case the previous hash was invalid)
        $update = $pdo->prepare("UPDATE admin_users SET password = :password WHERE username = :username");
        $update->execute([':password' => $hashedPassword, ':username' => $username]);
        echo "✅ Admin password updated successfully!<br>";
    } else {
        // Insert new admin
        $sql = "INSERT INTO admin_users (name, email, username, password, role, status) 
                VALUES (:name, :email, :username, :password, 'super_admin', 'active')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':username' => $username,
            ':password' => $hashedPassword
        ]);
        echo "✅ Admin account created successfully!<br>";
    }

    echo "<br><strong>Login Credentials:</strong><br>";
    echo "Username: <code>{$username}</code><br>";
    echo "Password: <code>{$plainPassword}</code><br>";
    echo "<br>🔒 <strong>Please DELETE this file (setup-admin.php) immediately!</strong><br>";
    echo "<br><a href='admin/login.php'>Go to Login Page →</a>";

} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
?>
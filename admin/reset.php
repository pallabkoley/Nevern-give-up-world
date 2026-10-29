<?php
// admin/reset.php
$host = 'localhost';
$dbname = 'ldslczmy_nguw';
$username = 'ldslczmy_nguw';
$password = 'admin2016';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // নতুন পাসওয়ার্ড
    $new_password = 'Admin@123';
    
    // পিএইচপি দিয়ে নতুন হ্যাশ তৈরি
    $hash = password_hash($new_password, PASSWORD_BCRYPT);

    // ডাটাবেসে আপডেট করা
    $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE email = ? OR username = ?");
    $stmt->execute([$hash, 'admin@nevergiveupworld.com', 'admin']);

    echo "<h2 style='color:green;'>সফলভাবে পাসওয়ার্ড আপডেট হয়েছে!</h2>";
    echo "এখন আপনি এই তথ্য দিয়ে লগইন করুন:<br>";
    echo "<b>Username/Email:</b> admin@nevergiveupworld.com<br>";
    echo "<b>Password:</b> Admin@123<br>";
    echo "<br><a href='/admin/admin-login'>এখানে ক্লিক করে লগইন পেজে যান</a>";
    
} catch (PDOException $e) {
    die("ডাটাবেস এরর: " . $e->getMessage());
}
?>
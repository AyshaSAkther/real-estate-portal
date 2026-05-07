<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName    = trim($_POST['userName']);
    $contactInfo = trim($_POST['contactInfo']);
    $password    = $_POST['password'];
    $userType    = $_POST['userType'];

    if ($userName && $password && $userType) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Use the stored procedure to add user
        try {
            $stmt = $pdo->prepare("CALL AddOrUpdateUser(NULL, ?, ?, ?, ?)");
            $stmt->execute([$userName, $contactInfo, $passwordHash, $userType]);
            $success = "Account created! You can now login.";
        } catch (PDOException $e) {
            $error = "Username already exists or an error occurred.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding: 60px 20px; }
        .box { background: #fff; padding: 36px; border-radius: 8px; width: 360px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h2 { margin: 0 0 24px; color: #2c3e50; }
        label { display: block; margin-bottom: 4px; font-size: 13px; color: #555; }
        input, select { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 16px; box-sizing: border-box; font-size: 14px; }
        button { width: 100%; background: #2980b9; color: #fff; border: none; padding: 11px; border-radius: 4px; font-size: 15px; cursor: pointer; }
        button:hover { background: #1f6fa0; }
        .error   { color: #c0392b; font-size: 13px; margin-bottom: 14px; }
        .success { color: #27ae60; font-size: 13px; margin-bottom: 14px; }
        a { color: #2980b9; font-size: 13px; }
        .links { margin-top: 16px; text-align: center; }
    </style>
</head>
<body>
<div class="box">
    <h2>Create Account</h2>
    <?php if ($error)  echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
        <label>Username *</label>
        <input type="text" name="userName" required>

        <label>Contact Info (email or phone)</label>
        <input type="text" name="contactInfo">

        <label>Password *</label>
        <input type="password" name="password" required>

        <label>Account Type *</label>
        <select name="userType" required>
            <option value="">-- Select --</option>
            <option value="agent">Agent</option>
            <option value="buyer">Buyer</option>
            <option value="renter">Renter</option>
        </select>

        <button type="submit">Register</button>
    </form>
    <div class="links">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>
</body>
</html>

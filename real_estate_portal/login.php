<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['userName']);
    $password = $_POST['password'];

    if ($userName && $password) {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE userName = ?");
        $stmt->execute([$userName]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['passwordHash'])) {
            $_SESSION['userId']   = $user['userId'];
            $_SESSION['userName'] = $user['userName'];
            $_SESSION['userType'] = $user['userType'];

            // Role-based redirect
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter your username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding: 80px 20px; }
        .box { background: #fff; padding: 36px; border-radius: 8px; width: 340px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h2 { margin: 0 0 24px; color: #2c3e50; }
        label { display: block; margin-bottom: 4px; font-size: 13px; color: #555; }
        input { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 16px; box-sizing: border-box; font-size: 14px; }
        button { width: 100%; background: #2980b9; color: #fff; border: none; padding: 11px; border-radius: 4px; font-size: 15px; cursor: pointer; }
        button:hover { background: #1f6fa0; }
        .error { color: #c0392b; font-size: 13px; margin-bottom: 14px; }
        .links { margin-top: 16px; text-align: center; font-size: 13px; }
        a { color: #2980b9; }
    </style>
</head>
<body>
<div class="box">
    <h2>Login</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="userName" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <div class="links">
        No account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>

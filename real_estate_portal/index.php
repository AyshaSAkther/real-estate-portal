<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Real Estate Agency Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        nav { background: #2c3e50; padding: 14px 30px; display: flex; gap: 20px; align-items: center; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; }
        nav a:hover { text-decoration: underline; }
        .hero { background: #2980b9; color: #fff; text-align: center; padding: 60px 20px; }
        .hero h1 { margin: 0 0 10px; font-size: 36px; }
        .hero p  { font-size: 16px; margin: 0 0 24px; }
        .hero a  { background: #fff; color: #2980b9; padding: 12px 28px; border-radius: 4px; font-weight: bold; text-decoration: none; }
        .cards { display: flex; gap: 20px; justify-content: center; padding: 40px 20px; flex-wrap: wrap; }
        .card { background: #fff; border-radius: 8px; padding: 24px; width: 220px; box-shadow: 0 2px 6px rgba(0,0,0,.1); text-align: center; }
        .card h3 { margin: 0 0 8px; color: #2c3e50; }
        .card p  { font-size: 13px; color: #666; margin: 0 0 16px; }
        .card a  { background: #2980b9; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>
<nav>
    <a href="index.php"><strong>RealEstate Portal</strong></a>
    <a href="properties.php">Properties</a>
    <?php if (isset($_SESSION['userId'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($_SESSION['userType'] === 'agent'): ?>
            <a href="add_property.php">Add Listing</a>
        <?php endif; ?>
        <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['userName']) ?>)</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>

<div class="hero">
    <h1>Find Your Perfect Property</h1>
    <p>Browse listings, connect with agents, and make your move.</p>
    <a href="properties.php">Browse Properties</a>
</div>

<div class="cards">
    <div class="card">
        <h3>Browse Listings</h3>
        <p>View all available properties for sale or rent.</p>
        <a href="properties.php">View All</a>
    </div>
    <div class="card">
        <h3>Register</h3>
        <p>Create an account as an agent, buyer, or renter.</p>
        <a href="register.php">Sign Up</a>
    </div>
    <div class="card">
        <h3>Login</h3>
        <p>Access your dashboard and manage your activity.</p>
        <a href="login.php">Login</a>
    </div>
</div>
</body>
</html>

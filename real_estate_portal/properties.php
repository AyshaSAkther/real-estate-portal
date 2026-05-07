<?php
session_start();
require 'db.php';

// Fetch all properties from the view
$stmt = $pdo->query("SELECT * FROM PropertyListingView");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Properties — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        nav { background: #2c3e50; padding: 14px 30px; display: flex; gap: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; }
        h1 { text-align: center; color: #2c3e50; margin: 30px 0 20px; }
        .grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; padding: 0 20px 40px; }
        .card { background: #fff; border-radius: 8px; padding: 20px; width: 260px; box-shadow: 0 2px 6px rgba(0,0,0,.1); }
        .card h3 { margin: 0 0 8px; color: #2c3e50; font-size: 16px; }
        .card p  { font-size: 13px; color: #666; margin: 4px 0; }
        .price { font-size: 18px; font-weight: bold; color: #27ae60; margin: 10px 0; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .available { background: #d4efdf; color: #1a7a3d; }
        .sold      { background: #fadbd8; color: #922b21; }
        .rented    { background: #fdebd0; color: #a04000; }
        .btn { display: inline-block; margin-top: 12px; background: #2980b9; color: #fff; padding: 7px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
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
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
</nav>

<h1>Available Properties</h1>

<div class="grid">
    <?php if (empty($properties)): ?>
        <p>No properties found.</p>
    <?php else: ?>
        <?php foreach ($properties as $p): ?>
        <div class="card">
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= htmlspecialchars($p['propertyType']) ?> — <?= htmlspecialchars($p['city']) ?></p>
            <p>Agent: <?= htmlspecialchars($p['agentName']) ?></p>
            <div class="price">$<?= number_format($p['price'], 2) ?></div>
            <span class="badge <?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
            <br>
            <a class="btn" href="property_details.php?id=<?= $p['propertyId'] ?>">View Details</a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>

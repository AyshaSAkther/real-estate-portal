<?php
session_start();
require 'db.php';

// Only agents can access this page
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'agent') {
    header("Location: login.php");
    exit;
}

$error   = '';
$success = '';

function addProperty($pdo, $title, $propertyType, $address, $city, $price, $status, $agentId) {
    $stmt = $pdo->prepare(
        "INSERT INTO Properties (title, propertyType, address, city, price, status, agentId)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$title, $propertyType, $address, $city, $price, $status, $agentId]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title']);
    $propertyType = trim($_POST['propertyType']);
    $address      = trim($_POST['address']);
    $city         = trim($_POST['city']);
    $price        = trim($_POST['price']);
    $status       = $_POST['status'];

    if ($title && $propertyType && $address && $city && $price) {
        addProperty($pdo, $title, $propertyType, $address, $city, $price, $status, $_SESSION['userId']);
        $success = "Property listing added successfully!";
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        nav { background: #2c3e50; padding: 14px 30px; display: flex; gap: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; }
        .container { max-width: 500px; margin: 40px auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h2 { margin: 0 0 24px; color: #2c3e50; }
        label { display: block; margin-bottom: 4px; font-size: 13px; color: #555; }
        input, select { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 16px; box-sizing: border-box; font-size: 14px; }
        button { background: #27ae60; color: #fff; border: none; padding: 11px 24px; border-radius: 4px; font-size: 15px; cursor: pointer; }
        button:hover { background: #1e8449; }
        .error   { color: #c0392b; font-size: 13px; margin-bottom: 14px; }
        .success { color: #27ae60; font-size: 13px; margin-bottom: 14px; }
    </style>
</head>
<body>
<nav>
    <a href="index.php"><strong>RealEstate Portal</strong></a>
    <a href="properties.php">Properties</a>
    <a href="add_property.php">Add Listing</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <h2>Add New Property</h2>
    <?php if ($error)   echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <label>Title *</label>
        <input type="text" name="title" required>

        <label>Property Type *</label>
        <input type="text" name="propertyType" placeholder="e.g. House, Apartment, Studio" required>

        <label>Address *</label>
        <input type="text" name="address" required>

        <label>City *</label>
        <input type="text" name="city" required>

        <label>Price ($) *</label>
        <input type="number" name="price" step="0.01" min="0" required>

        <label>Status</label>
        <select name="status">
            <option value="available">Available</option>
            <option value="sold">Sold</option>
            <option value="rented">Rented</option>
        </select>

        <button type="submit">Add Property</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
require 'db.php';

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId   = $_SESSION['userId'];
$userType = $_SESSION['userType'];

function getUserDetails($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE userId = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$user = getUserDetails($pdo, $userId);

// Get inquiries made by this user
$inquiries = [];
if (in_array($userType, ['buyer', 'renter'])) {
    $stmt = $pdo->prepare(
        "SELECT i.*, p.title AS propertyTitle FROM Inquiries i
         JOIN Properties p ON i.propertyId = p.propertyId
         WHERE i.userId = ? ORDER BY i.inquiryDate DESC"
    );
    $stmt->execute([$userId]);
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get favorites
$favorites = [];
if (in_array($userType, ['buyer', 'renter'])) {
    $stmt = $pdo->prepare(
        "SELECT f.*, p.title AS propertyTitle, p.city, p.price FROM Favorites f
         JOIN Properties p ON f.propertyId = p.propertyId
         WHERE f.userId = ?"
    );
    $stmt->execute([$userId]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get agent's listings
$listings = [];
if ($userType === 'agent') {
    $stmt = $pdo->prepare("SELECT * FROM Properties WHERE agentId = ?");
    $stmt->execute([$userId]);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        nav { background: #2c3e50; padding: 14px 30px; display: flex; gap: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .welcome { background: #2980b9; color: #fff; padding: 20px 28px; border-radius: 8px; margin-bottom: 24px; }
        .welcome h2 { margin: 0 0 4px; }
        .welcome p  { margin: 0; font-size: 14px; opacity: .85; }
        .section { background: #fff; border-radius: 8px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
        .section h3 { margin: 0 0 16px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #f0f0f0; }
        th { color: #888; font-weight: normal; }
        .empty { color: #aaa; font-size: 13px; }
        .btn { display: inline-block; background: #27ae60; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .available { background: #d4efdf; color: #1a7a3d; }
        .sold      { background: #fadbd8; color: #922b21; }
        .rented    { background: #fdebd0; color: #a04000; }
    </style>
</head>
<body>
<nav>
    <a href="index.php"><strong>RealEstate Portal</strong></a>
    <a href="properties.php">Properties</a>
    <?php if ($userType === 'agent'): ?>
        <a href="add_property.php">Add Listing</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <div class="welcome">
        <h2>Welcome, <?= htmlspecialchars($user['userName']) ?>!</h2>
        <p>Role: <?= ucfirst($userType) ?> &nbsp;|&nbsp; Contact: <?= htmlspecialchars($user['contactInfo'] ?? 'N/A') ?></p>
    </div>

    <?php if ($userType === 'agent'): ?>
    <!-- AGENT: show their listings -->
    <div class="section">
        <h3>My Listings <a class="btn" href="add_property.php" style="float:right;margin-top:-4px">+ Add New</a></h3>
        <?php if (empty($listings)): ?>
            <p class="empty">You have not added any listings yet.</p>
        <?php else: ?>
        <table>
            <tr><th>Title</th><th>City</th><th>Price</th><th>Status</th><th></th></tr>
            <?php foreach ($listings as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['title']) ?></td>
                <td><?= htmlspecialchars($l['city']) ?></td>
                <td>$<?= number_format($l['price'], 2) ?></td>
                <td><span class="badge <?= $l['status'] ?>"><?= ucfirst($l['status']) ?></span></td>
                <td><a href="property_details.php?id=<?= $l['propertyId'] ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- BUYER / RENTER: inquiries and favorites -->
    <div class="section">
        <h3>My Inquiries</h3>
        <?php if (empty($inquiries)): ?>
            <p class="empty">You have not submitted any inquiries yet.</p>
        <?php else: ?>
        <table>
            <tr><th>Property</th><th>Message</th><th>Date</th></tr>
            <?php foreach ($inquiries as $i): ?>
            <tr>
                <td><?= htmlspecialchars($i['propertyTitle']) ?></td>
                <td><?= htmlspecialchars($i['message']) ?></td>
                <td><?= $i['inquiryDate'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>My Favorites</h3>
        <?php if (empty($favorites)): ?>
            <p class="empty">You have not saved any properties yet.</p>
        <?php else: ?>
        <table>
            <tr><th>Property</th><th>City</th><th>Price</th><th>Saved</th><th></th></tr>
            <?php foreach ($favorites as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['propertyTitle']) ?></td>
                <td><?= htmlspecialchars($f['city']) ?></td>
                <td>$<?= number_format($f['price'], 2) ?></td>
                <td><?= date('M d, Y', strtotime($f['savedDate'])) ?></td>
                <td><a href="property_details.php?id=<?= $f['propertyId'] ?>">View</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

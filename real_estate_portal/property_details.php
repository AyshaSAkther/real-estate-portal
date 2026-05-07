<?php
session_start();
require 'db.php';

$propertyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error   = '';
$success = '';

// Get property details
$stmt = $pdo->prepare("SELECT p.*, u.userName AS agentName FROM Properties p JOIN Users u ON p.agentId = u.userId WHERE p.propertyId = ?");
$stmt->execute([$propertyId]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Property not found.");
}

function addInquiry($pdo, $userId, $propertyId, $message) {
    $stmt = $pdo->prepare(
        "INSERT INTO Inquiries (userId, propertyId, message, inquiryDate) VALUES (?, ?, ?, NOW())"
    );
    $stmt->execute([$userId, $propertyId, $message]);
}

// Handle inquiry submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if (!isset($_SESSION['userId'])) {
        $error = "You must be logged in to do that.";
    } elseif ($_POST['action'] === 'inquiry') {
        $message = trim($_POST['message']);
        if ($message) {
            addInquiry($pdo, $_SESSION['userId'], $propertyId, $message);
            $success = "Inquiry submitted successfully!";
        } else {
            $error = "Please enter a message.";
        }
    } elseif ($_POST['action'] === 'favorite') {
        $stmt = $pdo->prepare("INSERT INTO Favorites (userId, propertyId, savedDate) VALUES (?, ?, NOW())");
        $stmt->execute([$_SESSION['userId'], $propertyId]);
        $success = "Property saved to favorites!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($property['title']) ?> — Real Estate Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        nav { background: #2c3e50; padding: 14px 30px; display: flex; gap: 20px; }
        nav a { color: #fff; text-decoration: none; font-size: 14px; }
        .container { max-width: 640px; margin: 40px auto; }
        .card { background: #fff; border-radius: 8px; padding: 28px; box-shadow: 0 2px 8px rgba(0,0,0,.1); margin-bottom: 20px; }
        h2 { margin: 0 0 6px; color: #2c3e50; }
        .price { font-size: 26px; color: #27ae60; font-weight: bold; margin: 10px 0; }
        p { color: #555; margin: 5px 0; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .available { background: #d4efdf; color: #1a7a3d; }
        .sold      { background: #fadbd8; color: #922b21; }
        .rented    { background: #fdebd0; color: #a04000; }
        h3 { color: #2c3e50; margin: 0 0 14px; }
        textarea { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; resize: vertical; font-size: 14px; box-sizing: border-box; }
        .btn { display: inline-block; border: none; padding: 10px 20px; border-radius: 4px; font-size: 14px; cursor: pointer; margin-top: 10px; color: #fff; }
        .btn-blue  { background: #2980b9; }
        .btn-green { background: #27ae60; }
        .error   { color: #c0392b; font-size: 13px; margin-bottom: 10px; }
        .success { color: #27ae60; font-size: 13px; margin-bottom: 10px; }
    </style>
</head>
<body>
<nav>
    <a href="index.php"><strong>RealEstate Portal</strong></a>
    <a href="properties.php">Properties</a>
    <?php if (isset($_SESSION['userId'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</nav>

<div class="container">
    <?php if ($error)   echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>

    <div class="card">
        <h2><?= htmlspecialchars($property['title']) ?></h2>
        <span class="badge <?= $property['status'] ?>"><?= ucfirst($property['status']) ?></span>
        <div class="price">$<?= number_format($property['price'], 2) ?></div>
        <p><strong>Type:</strong> <?= htmlspecialchars($property['propertyType']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($property['address']) ?>, <?= htmlspecialchars($property['city']) ?></p>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property['agentName']) ?></p>
    </div>

    <?php if (isset($_SESSION['userId']) && in_array($_SESSION['userType'], ['buyer', 'renter'])): ?>
    <div class="card">
        <h3>Send an Inquiry</h3>
        <form method="POST">
            <input type="hidden" name="action" value="inquiry">
            <textarea name="message" rows="4" placeholder="Write your message here..."></textarea>
            <button class="btn btn-blue" type="submit">Submit Inquiry</button>
        </form>
    </div>

    <div class="card">
        <h3>Save this Property</h3>
        <form method="POST">
            <input type="hidden" name="action" value="favorite">
            <button class="btn btn-green" type="submit">Add to Favorites</button>
        </form>
    </div>
    <?php endif; ?>

    <a href="properties.php" style="color:#2980b9; font-size:14px;">← Back to listings</a>
</div>
</body>
</html>

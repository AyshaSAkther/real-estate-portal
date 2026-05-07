<?php
// db.php — Database connection
// Update host, username, password if needed

$host     = 'localhost';
$dbname   = 'real_estate_portal_db';
$username = 'root';
$password = ''; // change to your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

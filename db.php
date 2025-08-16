<?php
// Database connection settings
$dbHost = "localhost";
$dbName = "codersma_emailbulk";
$dbUser = "codersma_emailbulk";
$dbPass = "codersma_emailbulk";

try {
    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>

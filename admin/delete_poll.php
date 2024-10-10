<?php
session_start();
include '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $poll_id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM polls WHERE id = ?");
    $stmt->execute([$poll_id]);
}
header("Location: index.php");
exit();
?>

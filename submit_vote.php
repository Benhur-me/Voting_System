<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_id = $_POST['poll_id'];
    $option_id = $_POST['option_id'];

    // Update the votes for the selected option
    $query = $pdo->prepare("UPDATE options SET votes = votes + 1 WHERE id = ?");
    $query->execute([$option_id]);

    // Redirect to results page
    header("Location: results.php?poll_id=$poll_id");
    exit();
}
?>

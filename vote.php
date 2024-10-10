<?php
session_start();
include 'db.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_id = $_POST['poll_id']; // Assuming you're passing the poll ID
    $candidate_id = $_POST['candidate_id']; // Assuming you're passing the candidate ID

    // Insert the vote into the database
    $stmt = $pdo->prepare("INSERT INTO votes (poll_id, candidate_id) VALUES (?, ?)");
    $stmt->execute([$poll_id, $candidate_id]);

    // Redirect to the results page
    header("Location: results.php?poll_id=" . $poll_id); // Redirect to results page with poll_id
    exit();
}
?>

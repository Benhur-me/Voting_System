<?php
session_start();
include '../db.php'; // Ensure this path is correct

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch results for a specific poll
if (isset($_GET['poll_id'])) {
    $poll_id = $_GET['poll_id'];

    // Update your SQL query to reference 'candidate_id' instead of 'option_id'
    $query = $pdo->prepare("
        SELECT candidates.name AS candidate_name, COUNT(votes.candidate_id) AS vote_count
        FROM candidates
        LEFT JOIN votes ON candidates.id = votes.candidate_id
        WHERE candidates.poll_id = ?
        GROUP BY candidates.id
    ");
    
    $query->execute([$poll_id]);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "No poll ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poll Results</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>
    <header>
        <h1>Poll Results</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Results for Poll ID: <?php echo htmlspecialchars($poll_id); ?></h2>
        <table>
            <tr>
                <th>Candidate Name</th>
                <th>Vote Count</th>
            </tr>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['candidate_name']); ?></td>
                    <td><?php echo htmlspecialchars($result['vote_count']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>

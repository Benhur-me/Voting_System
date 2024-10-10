<?php
session_start();
include '../db.php'; // Adjust the path to your database connection

// Check if the poll ID is set
if (!isset($_GET['id'])) {
    echo "No poll selected.";
    exit();
}

$poll_id = $_GET['id'];

// Fetch the poll details
$pollQuery = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
$pollQuery->execute([$poll_id]);
$poll = $pollQuery->fetch(PDO::FETCH_ASSOC);

// Fetch candidates for the poll
$candidatesQuery = $pdo->prepare("SELECT * FROM candidates WHERE poll_id = ?");
$candidatesQuery->execute([$poll_id]);
$candidates = $candidatesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($poll['title']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .candidate {
            margin: 20px 0;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .candidate img {
            width: 100px; /* Adjust image size */
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($poll['title']); ?></h1>

    <h3>Candidates:</h3>
    <?php foreach ($candidates as $candidate): ?>
        <div class="candidate">
            <h4><?php echo htmlspecialchars($candidate['name']); ?></h4>
            <?php if (!empty($candidate['image'])): ?>
                <img src="<?php echo htmlspecialchars($candidate['image']); ?>" alt="Candidate Image">
            <?php else: ?>
                <p>No image available.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>

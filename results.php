<?php
session_start();
include 'db.php'; // Ensure this path is correct

// Fetch results from the database (assuming you have a 'votes' table)
$query = $pdo->query("SELECT candidates.name, COUNT(votes.candidate_id) AS vote_count 
                       FROM votes 
                       JOIN candidates ON votes.candidate_id = candidates.id 
                       GROUP BY candidates.name");
$results = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        h1 {
            color: #333;
        }

        .result {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Voting Results</h1>
    <?php foreach ($results as $result): ?>
        <div class="result">
            <h2><?php echo htmlspecialchars($result['name']); ?></h2>
            <p>Votes: <?php echo htmlspecialchars($result['vote_count']); ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>

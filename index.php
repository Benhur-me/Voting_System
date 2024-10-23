<?php
session_start();
include 'db.php'; // Include the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch polls from the database
$query = $pdo->query("SELECT * FROM polls");
$polls = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Polls</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout-button {
            background-color: #d9534f;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #c9302c;
        }

        main {
            padding: 20px;
        }

        .poll {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .candidates {
            display: flex;
            flex-wrap: wrap;
        }

        .candidate {
            flex: 1 1 30%;
            margin: 10px;
            text-align: center;
        }

        .candidate img {
            max-width: 100px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Available Polls</h1>
        <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </header>

    <main>
        <?php foreach ($polls as $poll): ?>
            <div class="poll">
                <h2><?php echo htmlspecialchars($poll['title']); ?></h2>
                <h3>Candidates:</h3>
                <div class="candidates">
                    <?php
                    $candidate_query = $pdo->prepare("SELECT * FROM candidates WHERE poll_id = ?");
                    $candidate_query->execute([$poll['id']]);
                    $candidates = $candidate_query->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($candidates as $candidate): ?>
                        <div class="candidate">
                            <img src="uploads/<?php echo htmlspecialchars($candidate['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                            <p><?php echo htmlspecialchars($candidate['name']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>

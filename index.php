<?php
session_start();
include 'db.php'; // Ensure this path is correct

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit();
}

// Fetch polls from the database
$query = $pdo->query("SELECT * FROM polls");
$polls = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote'])) {
    $poll_id = $_POST['poll_id'];
    $candidate_id = $_POST['candidate_id'];
    $user_id = $_SESSION['user_id']; // Ensure user_id is set

    // Check if the user has already voted in this poll
    $checkVoteQuery = $pdo->prepare("SELECT * FROM votes WHERE poll_id = ? AND user_id = ?");
    $checkVoteQuery->execute([$poll_id, $user_id]);
    
    if ($checkVoteQuery->rowCount() > 0) {
        // User has already voted for this poll
        echo "<script>alert('You have already voted in this poll.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Record the vote
    $voteQuery = $pdo->prepare("INSERT INTO votes (poll_id, candidate_id, user_id) VALUES (?, ?, ?)");
    $voteQuery->execute([$poll_id, $candidate_id, $user_id]);

    // Redirect to results
    echo "<script>alert('You voted for candidate ID: $candidate_id.'); window.location.href = 'results.php';</script>";
    exit();
}
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
            display: flex; /* Use flexbox */
            justify-content: space-between; /* Space between elements */
            align-items: center; /* Center items vertically */
        }

        .logout-button {
            background-color: #d9534f; /* Bootstrap danger color */
            color: white;
            padding: 10px 15px; /* Add some padding */
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #c9302c; /* Darker red on hover */
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
            flex: 1 1 30%; /* Adjust the width as needed */
            margin: 10px;
            text-align: center;
        }

        .candidate img {
            max-width: 100px; /* Set a max width for images */
            border-radius: 5px;
        }
        
        footer {
            text-align: center;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            position: relative; 
            bottom: 0; 
            width: 100%; 
        }
    </style>
</head>
<body>
    <header>
        <h1>Available Polls</h1>

        <!-- Add Logout Button -->
        <form action="logout.php" method="POST" style="display: inline;">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </header>

    <main>
        <?php foreach ($polls as $poll): ?>
            <div class="poll">
                <h2><?php echo htmlspecialchars($poll['title'] ?? ''); ?></h2>
                <h3>Candidates:</h3>
                <form method="POST" action="index.php">
                    <input type="hidden" name="poll_id" value="<?php echo $poll['id']; ?>">
                    <div class="candidates">
                        <?php
                        $candidate_query = $pdo->prepare("SELECT * FROM candidates WHERE poll_id = ?");
                        $candidate_query->execute([$poll['id']]);
                        $candidates = $candidate_query->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($candidates as $candidate): ?>
                            <div class="candidate">
                                <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                                <label><?php echo htmlspecialchars($candidate['name']); ?></label>
                                <img src="uploads/<?php echo htmlspecialchars($candidate['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($candidate['name'] ?? 'Unknown Candidate'); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="vote">Vote</button>
                </form>
            </div>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; 2021 Online Voting System Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
session_start();
include '../db.php'; // Include the database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch polls from the database
$query = $pdo->query("SELECT * FROM polls");
$polls = $query->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_GET['delete'])) {
    $poll_id = intval($_GET['delete']);
    
    // Delete related candidates and votes first
    $delete_candidates_query = $pdo->prepare("DELETE FROM candidates WHERE poll_id = ?");
    $delete_candidates_query->execute([$poll_id]);

    $delete_votes_query = $pdo->prepare("DELETE FROM votes WHERE poll_id = ?");
    $delete_votes_query->execute([$poll_id]);

    // Now delete the poll itself
    $delete_query = $pdo->prepare("DELETE FROM polls WHERE id = ?");
    $delete_query->execute([$poll_id]);

    // Redirect back to admin panel
    header("Location: index.php"); 
    exit();
}

// Store welcome message for display
$welcome_message = isset($_SESSION['welcome_message']) ? $_SESSION['welcome_message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            text-align: center;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin-right: 20px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
        }

        nav .logout-button {
            background-color: #dc3545;
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
        }

        nav .logout-button:hover {
            background-color: #c82333;
        }

        main {
            padding: 20px;
        }

        section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
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
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="create_poll.php">Create Poll</a></li>
                <li><a href="logout.php" class="logout-button">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <?php if ($welcome_message): ?>
                <div id="welcome-message" style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
                    <?php echo htmlspecialchars($welcome_message); ?>
                </div>
            <?php endif; ?>
            <h2>Manage Polls</h2>
            <table>
                <thead>
                    <tr>
                        <th>Poll Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($polls as $poll): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($poll['title']); ?></td>
                            <td>
                                <a href="edit_poll.php?id=<?php echo $poll['id']; ?>">Edit</a>
                                <a href="index.php?delete=<?php echo $poll['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2021 Online Voting System Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>

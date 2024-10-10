<?php
session_start();
include '../db.php';

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
    $delete_query = $pdo->prepare("DELETE FROM polls WHERE id = ?");
    $delete_query->execute([$poll_id]);
    header("Location: index.php"); // Redirect after deletion
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
            background-color: #007BFF; /* Bootstrap Primary Color */
            color: white;
            padding: 20px;
            text-align: center;
        }

        nav ul {
            list-style: none; /* Remove default list styling */
            padding: 0;
        }

        nav ul li {
            display: inline; /* Horizontal list */
            margin-right: 20px; /* Space between items */
        }

        nav ul li a {
            color: white;
            text-decoration: none; /* No underline */
        }

        nav .logout-button {
            background-color: #dc3545; /* Bootstrap Danger Color */
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
        }

        nav .logout-button:hover {
            background-color: #c82333; /* Darker shade on hover */
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
            width: 100%; /* Full width */
            border-collapse: collapse; /* Remove space between borders */
        }

        th, td {
            border: 1px solid #ddd; /* Border for table cells */
            padding: 8px; /* Padding inside table cells */
            text-align: left; /* Align text to the left */
        }

        th {
            background-color: #f2f2f2; /* Light gray background for header */
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #007BFF; /* Same color as header */
            color: white;
            position: relative; /* To position the footer */
            bottom: 0; /* Align footer to bottom */
            width: 100%; /* Full width */
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
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($polls as $poll): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($poll['title']); ?></td>
                            <td>
                                <?php if (!empty($poll['image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($poll['image']); ?>" alt="<?php echo htmlspecialchars($poll['title']); ?>" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
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
        <p>&copy; <?php echo date('Y'); ?> Your Company Name. All Rights Reserved.</p>
    </footer>
</body>
</html>

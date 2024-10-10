<?php
session_start();
include '../db.php';

// Check if candidate ID is provided
if (!isset($_GET['candidate_id'])) {
    echo "No candidate selected.";
    exit();
}

$candidate_id = $_GET['candidate_id'];

// Fetch the candidate details
$candidateQuery = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
$candidateQuery->execute([$candidate_id]);
$candidate = $candidateQuery->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    echo "Candidate not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'];

    // Update candidate in the database
    $updateQuery = $pdo->prepare("UPDATE candidates SET name = ? WHERE id = ?");
    $updateQuery->execute([$newName, $candidate_id]);

    // Redirect to a success page or back to the candidate list
    header("Location: candidates.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Candidate</h2>
        <form method="POST">
            <div>
                <label for="name">Candidate Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
            </div>
            <button type="submit">Update Candidate</button>
        </form>
    </div>
</body>
</html>

<?php
// Assuming you have a query to fetch candidates
$candidateQuery = $pdo->query("SELECT * FROM candidates");
$candidates = $candidateQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <thead>
        <tr>
            <th>Candidate Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidates as $candidate): ?>
            <tr>
                <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                <td>
                    <a href="edit_candidates.php?candidate_id=<?php echo $candidate['id']; ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

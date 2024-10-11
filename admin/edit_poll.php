<?php
session_start();
include '../db.php'; 

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update poll title
    $title = $_POST['title'];
    $updatePoll = $pdo->prepare("UPDATE polls SET title = ? WHERE id = ?");
    $updatePoll->execute([$title, $poll_id]);

    // Update candidates
    foreach ($candidates as $candidate) {
        $candidate_id = $candidate['id'];
        $candidate_name = $_POST['candidate'][$candidate_id] ?? ''; // Get candidate name from the submitted form

        // Check if image file is uploaded
        if (isset($_FILES['image'][$candidate_id]) && $_FILES['image'][$candidate_id]['error'] == UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image'][$candidate_id]['tmp_name'];
            $imageName = $_FILES['image'][$candidate_id]['name'];
            $imagePath = 'uploads/' . basename($imageName); // Ensure the uploads directory exists

            // Move the uploaded file
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                // Update the candidate image path in the database
                $updateCandidate = $pdo->prepare("UPDATE candidates SET name = ?, image = ? WHERE id = ?");
                $updateCandidate->execute([$candidate_name, $imagePath, $candidate_id]);
            }
        } else {
            // Update the candidate name only if no new image is uploaded
            $updateCandidate = $pdo->prepare("UPDATE candidates SET name = ? WHERE id = ?");
            $updateCandidate->execute([$candidate_name, $candidate_id]);
        }
    }

    // Redirect after update
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Poll</title>
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

        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <h1>Edit Poll</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Poll Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($poll['title']); ?>" required>

        <h3>Candidates:</h3>
        <?php foreach ($candidates as $candidate): ?>
            <div>
                <label>
                    Candidate Name:
                    <input type="text" name="candidate[<?php echo $candidate['id']; ?>]" value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                </label>
                <label>
                    Candidate Image:
                    <input type="file" name="image[<?php echo $candidate['id']; ?>]">
                </label>
                <?php if (!empty($candidate['image'])): ?>
                    <img src="<?php echo htmlspecialchars($candidate['image']); ?>" alt="Candidate Image" width="100">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit">Update Poll</button>
    </form>
</body>
</html>

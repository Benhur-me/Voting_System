<?php
session_start();
include '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Handle poll creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poll_title = $_POST['title'];
    $candidates = $_POST['candidates'];
    $candidate_images = $_FILES['candidate_images']; // Get the uploaded files

    // Insert the poll into the database
    $stmt = $pdo->prepare("INSERT INTO polls (title) VALUES (?)");
    $stmt->execute([$poll_title]);
    $poll_id = $pdo->lastInsertId();

    // Insert candidates into the database
    foreach ($candidates as $index => $candidate) {
        $candidate = trim($candidate);
        $image_name = null;

        if (!empty($candidate) && isset($candidate_images['name'][$index])) {
            $image = $candidate_images['name'][$index];
            $image_tmp = $candidate_images['tmp_name'][$index];
            $upload_dir = '../uploads/';
            $image_name = basename($image);
            $upload_file = $upload_dir . $image_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($image_tmp, $upload_file)) {
                // File upload successful
            } else {
                echo "<script>alert('Failed to upload image for candidate: $candidate');</script>";
            }

            // Insert the candidate into the database
            $candidate_stmt = $pdo->prepare("INSERT INTO candidates (poll_id, name, image) VALUES (?, ?, ?)"); // Assuming the candidates table has an image column
            $candidate_stmt->execute([$poll_id, $candidate, $image_name]);
        }
    }

    // Redirect back to the admin dashboard
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Poll</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        #app {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .candidate {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div id="app">
        <h1>Create Poll</h1>
        <form method="POST" enctype="multipart/form-data"> <!-- Add enctype for file upload -->
            <input type="text" name="title" placeholder="Poll Title" required>
            <div id="candidates-container">
                <div class="candidate">
                    <input type="text" name="candidates[]" placeholder="Candidate Name" required>
                    <input type="file" name="candidate_images[]" accept="image/*" required> <!-- Image upload for each candidate -->
                </div>
            </div>
            <button type="button" id="add-candidate">Add Another Candidate</button>
            <button type="submit">Create Poll</button>
        </form>
    </div>

    <script>
        document.getElementById('add-candidate').onclick = function() {
            var container = document.getElementById('candidates-container');
            var candidateDiv = document.createElement('div');
            candidateDiv.className = 'candidate';

            var inputName = document.createElement('input');
            inputName.type = 'text';
            inputName.name = 'candidates[]';
            inputName.placeholder = 'Candidate Name';
            inputName.required = true;

            var inputImage = document.createElement('input');
            inputImage.type = 'file';
            inputImage.name = 'candidate_images[]';
            inputImage.accept = 'image/*';
            inputImage.required = true;

            candidateDiv.appendChild(inputName);
            candidateDiv.appendChild(inputImage);
            container.appendChild(candidateDiv);
        };
    </script>
</body>
</html>

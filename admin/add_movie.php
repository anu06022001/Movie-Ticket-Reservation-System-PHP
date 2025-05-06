<?php
session_start();
include '../database/db_connection.php'; 

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Ensure admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : null;
    $duration = isset($_POST['duration']) ? intval($_POST['duration']) : null;
    $language = isset($_POST['language']) ? trim($_POST['language']) : null;
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $poster = isset($_POST['poster']) ? trim($_POST['poster']) : null;

    if ($name && $genre && $duration && $language && $description && $poster) {
        $stmt = $conn->prepare("INSERT INTO movie (name, genre, duration, language, description, poster) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $name, $genre, $duration, $language, $description, $poster);

        if ($stmt->execute()) {
            header("Location: admin_dashboard.php#manage-movies");
            exit;
        } else {
            $error = "Failed to add movie: " . $conn->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill all fields.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        /* Centered Container */
        .add-movie-container {
            width: 50%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 2rem;
            color: #444;
            margin-bottom: 20px;
        }

        /* Error Message */
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        label {
            font-weight: bold;
            text-align: left;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        /* Submit Button */
        button {
            width: 100%;
            padding: 10px;
            background: #6e8efb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #5a7bd1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .add-movie-container {
                width: 90%;
                padding: 15px;
            }

            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="add-movie-container">
        <h1>Add New Movie</h1>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post">
            <label>Movie Name:</label>
            <input type="text" name="name" required> 
            
            <label>Genre:</label>
            <input type="text" name="genre" required> 
            
            <label>Duration (minutes):</label>
            <input type="number" name="duration" required> 
            
            <label>Language:</label>
            <input type="text" name="language" required> 
            
            <label>Description:</label>
            <textarea name="description" required></textarea> 
            
            <label>Poster URL:</label>
            <input type="text" name="poster" required> 
            
            <button type="submit" name="submit">Add Movie</button>
        </form>
    </div>
</body>
</html>

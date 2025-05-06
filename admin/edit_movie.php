<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['movie_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$movie_id = $_GET['movie_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $duration = $_POST['duration'];
    $language = $_POST['language'];
    $description = $_POST['description'];
    $poster = $_POST['poster'];

    $stmt = $conn->prepare("UPDATE movie SET name=?, genre=?, duration=?, language=?, description=?, poster=? WHERE movie_id=?");
    $stmt->bind_param("ssisssi", $name, $genre, $duration, $language, $description, $poster, $movie_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php#movies");
        exit;
    } else {
        $error = "Failed to update movie.";
    }

    $stmt->close();
    $conn->close();
} else {
    $stmt = $conn->prepare("SELECT * FROM movie WHERE movie_id=?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
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
        .edit-movie-container {
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

        /* Submit and Back Buttons */
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        button {
            padding: 10px 15px;
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

        .back-btn {
            background: #ccc;
            color: black;
        }

        .back-btn:hover {
            background: #aaa;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .edit-movie-container {
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
    <div class="edit-movie-container">
        <h1>Edit Movie</h1>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post">
            <label>Movie Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($movie['name']) ?>" required>
            
            <label>Genre:</label>
            <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required>
            
            <label>Duration (minutes):</label>
            <input type="number" name="duration" value="<?= htmlspecialchars($movie['duration']) ?>" required>
            
            <label>Language:</label>
            <input type="text" name="language" value="<?= htmlspecialchars($movie['language']) ?>" required>
            
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($movie['description']) ?></textarea>
            
            <label>Poster URL:</label>
            <input type="text" name="poster" value="<?= htmlspecialchars($movie['poster']) ?>" required>
            
            <div class="btn-container">
                <button type="submit">Update Movie</button>
                <a href="admin_dashboard.php#movies" class="back-btn" style="text-decoration: none; padding: 10px 15px; border-radius: 5px; border: none; display: inline-block;">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>

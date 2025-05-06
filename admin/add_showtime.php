<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie_id = $_POST['movie_id'];
    $show_time = $_POST['show_time'];

    $stmt = $conn->prepare("INSERT INTO showtime (movie_id, show_time) VALUES (?, ?)");
    $stmt->bind_param("is", $movie_id, $show_time);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php#showtimes");
        exit;
    } else {
        $error = "Failed to add showtime.";
    }

    $stmt->close();
    $conn->close();
} else {
    $movies = $conn->query("SELECT * FROM movie");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Showtime</title>
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
        .add-showtime-container {
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

        select, input {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
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
            text-decoration: none;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
        }

        .back-btn:hover {
            background: #aaa;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .add-showtime-container {
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
    <div class="add-showtime-container">
        <h1>Add Showtime</h1>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="post">
            <label>Movie:</label>
            <select name="movie_id" required>
                <?php while ($movie = $movies->fetch_assoc()) { ?>
                    <option value="<?= $movie['movie_id'] ?>"><?= htmlspecialchars($movie['name']) ?></option>
                <?php } ?>
            </select>
            
            <label>Showtime:</label>
            <input type="time" name="show_time" required>
            
            <div class="btn-container">
                <button type="submit">Add Showtime</button>
                <a href="admin_dashboard.php#showtimes" class="back-btn">Back to Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>

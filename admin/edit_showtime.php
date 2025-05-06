<?php
session_start();
include '../database/db_connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$showtime_id = $_GET['showtime_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $movie_id = $_POST['movie_id'];
    $show_time = $_POST['show_time'];
    $total_seats = $_POST['total_seats'];

    $stmt = $conn->prepare("UPDATE showtime SET movie_id=?, show_time=?, total_seats=? WHERE showtime_id=?");
    $stmt->bind_param("isii", $movie_id, $show_time, $total_seats, $showtime_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php#showtimes");
        exit;
    } else {
        $error = "Failed to update showtime.";
    }
}

$stmt = $conn->prepare("SELECT * FROM showtime WHERE showtime_id=?");
$stmt->bind_param("i", $showtime_id);
$stmt->execute();
$showtime = $stmt->get_result()->fetch_assoc();

$movies = $conn->query("SELECT * FROM movie");

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Showtime</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        h1 {
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        select, input[type="time"], input[type="number"], button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Showtime</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label>Movie:</label>
            <select name="movie_id" required>
                <?php while ($movie = $movies->fetch_assoc()) { ?>
                    <option value="<?= $movie['movie_id'] ?>" <?= $movie['movie_id'] == $showtime['movie_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($movie['name']) ?>
                    </option>
                <?php } ?>
            </select>
            
            <label>Showtime:</label>
            <input type="time" name="show_time" value="<?= $showtime['show_time'] ?>" required>
            
            <label>Total Seats:</label>
            <input type="number" name="total_seats" value="<?= $showtime['total_seats'] ?>" required min="1">
            
            <button type="submit">Update Showtime</button>
        </form>
    </div>
</body>
</html>

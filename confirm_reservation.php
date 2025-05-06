<?php
session_start();
include 'database/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

if (isset($_GET['movie_id'], $_GET['showtime_id'])) {
    $movie_id = $_GET['movie_id'];
    $showtime_id = $_GET['showtime_id'];

    $stmt = $conn->prepare("SELECT name FROM movie WHERE movie_id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
    $movie = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("SELECT show_time FROM showtime WHERE showtime_id = ?");
    $stmt->bind_param("i", $showtime_id);
    $stmt->execute();
    $showtime = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $seat_query = "SELECT (50 - IFNULL(SUM(no_of_seats), 0)) AS available_seats FROM ticket WHERE showtime_id = ?";
    $seat_stmt = $conn->prepare($seat_query);
    $seat_stmt->bind_param("i", $showtime_id);
    $seat_stmt->execute();
    $available_seats = $seat_stmt->get_result()->fetch_assoc()['available_seats'] ?? 50;
    $seat_stmt->close();
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Reservation</title>
    <link rel="stylesheet" href="css/style_confirm.css">
</head>
<body>

    <div class="container">
        <h1>Confirm Your Reservation</h1>
        <p><strong>Movie:</strong> <?php echo htmlspecialchars($movie['name']); ?></p>
        <p><strong>Showtime:</strong> <?php echo htmlspecialchars($showtime['show_time']); ?></p>
        <p><strong>Ticket Price:</strong> ₹200</p>
        <p><strong>Available Seats:</strong> <?php echo $available_seats; ?></p>
        
        <form action="ticket_info.php" method="post">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">
            <input type="hidden" name="showtime_id" value="<?php echo $showtime_id; ?>">
            <label for="seats">Number of Seats:</label>
            <input type="number" name="seats" id="seats" min="1" max="<?php echo $available_seats; ?>" required onchange="calculateTotal()">
            
            <label for="payment_status">Payment Method:</label>
            <select name="payment_status" id="payment_status" required>
                <option value="cash">Cash</option>
                <option value="online">Online Payment</option>
            </select>
            
            <p><strong>Total Amount: ₹<span id="total_amount">0</span></strong></p>
            <button type="submit">Confirm Reservation</button>
        </form>
    </div>

    <script>
        function calculateTotal() {
            let seats = document.getElementById('seats').value;
            document.getElementById('total_amount').innerText = seats * 200;
        }
    </script>
</body>
</html>

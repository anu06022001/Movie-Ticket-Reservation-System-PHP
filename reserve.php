<?php
include 'database/db_connection.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure 'movie_id' is provided
if (!isset($_POST['movie_id'])) {
    die("No movie selected.");
}

$movie_id = $_POST['movie_id'];

// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM movie WHERE movie_id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if movie exists
if ($result->num_rows === 0) {
    die("Movie not found.");
}

$movie = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve - <?php echo htmlspecialchars($movie['name']); ?></title>
    <link rel="stylesheet" href="css/style_reserve.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .movie-details { display: none; }
        img { max-width: 200px; border-radius: 10px; transition: transform 0.5s; }
        button { margin-top: 10px; padding: 10px 20px; cursor: pointer; }
        .fade-in { opacity: 0; transition: opacity 1s ease-in-out, transform 0.5s; }
    </style>
</head>
<body>
    <h1>Reserve Your Ticket</h1>
    
    <div class="movie-details">
        <img src="<?php echo htmlspecialchars($movie['poster']); ?>" alt="Poster for <?php echo htmlspecialchars($movie['name']); ?>" class="fade-in">
        <h2 class="fade-in"><?php echo htmlspecialchars($movie['name']); ?></h2>
        <p class="fade-in"><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
        <p class="fade-in"><strong>Duration:</strong> <?php echo htmlspecialchars($movie['duration']); ?> minutes</p>
        <p class="fade-in"><strong>Language:</strong> <?php echo htmlspecialchars($movie['language'] ?? 'N/A'); ?></p>
        <p class="fade-in"><strong>Description:</strong> <?php echo htmlspecialchars($movie['description'] ?? 'No description available'); ?></p>

        <form action="confirm_reservation.php" method="get">
            <input type="hidden" name="movie_id" value="<?php echo $movie_id; ?>">

            <label for="showtime_id">Select Showtime:</label>
            <select name="showtime_id" id="showtime_id" required>
                <?php
                $showtime_stmt = $conn->prepare("SELECT * FROM showtime WHERE movie_id = ?");
                $showtime_stmt->bind_param("i", $movie_id);
                $showtime_stmt->execute();
                $showtime_result = $showtime_stmt->get_result();
                while ($showtime = $showtime_result->fetch_assoc()) {
                    echo "<option value='" . $showtime['showtime_id'] . "'>" . $showtime['show_time'] . "</option>";
                }
                $showtime_stmt->close();
                ?>
            </select><br>

            <button type="submit">Proceed to Reserve</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Automatically show the movie details container with animation
            $('.movie-details').slideDown(500, function() {
                // Add fade-in and transform effect to text and image elements
                $('.fade-in').each(function(index) {
                    $(this).delay(index * 200).css({ 
                        opacity: 1, 
                        transform: 'translateY(0)' 
                    });
                });
            });

            // Add a hover effect to the movie poster
            $('img').hover(
                function() { $(this).css('transform', 'scale(1.1)'); },
                function() { $(this).css('transform', 'scale(1)'); }
            );
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>

<?php
include 'database/db_connection.php';
session_start();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch movies by language
function fetchMoviesByLanguage($conn, $language) {
    $sql = "SELECT movie_id, name, poster FROM movie WHERE language = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $language);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="movie-container" data-language="' . htmlspecialchars($language) . '">';
        echo '<h6>' . htmlspecialchars($language) . ' Movies</h6>';
        echo '<ul class="animate-container">';
        
        while ($row = $result->fetch_assoc()) {
            echo '<li class="movie">';
            echo '<img src="' . htmlspecialchars($row["poster"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
            echo '<form action="reserve.php" method="post" onsubmit="return checkLogin()">';
            echo '<input type="hidden" name="movie_id" value="' . htmlspecialchars($row["movie_id"]) . '">';
            echo '<input type="submit" value="Book Now">';
            echo '</form>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Reservation</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.7.1.min.js"></script>
    <script>
        // Check if the user is logged in before allowing booking
        function checkLogin() {
            <?php if (!isset($_SESSION['username'])): ?>
                alert("Please login first to book a ticket.");
                window.location.href = "login.php";
                return false;
            <?php endif; ?>
            return true;
        }

        $(document).ready(function() {
            // Initially hide all movie containers
            $('.movie-container').hide();

            // Show movie containers with a fade-in effect and delay
            $('.movie-container').each(function(index) {
                $(this).delay(300 * index).fadeIn(800);
            });

            // Filter movies based on selected language
            $('.filter-buttons button').click(function() {
                const language = $(this).data('language');

                // Hide all containers first
                $('.movie-container').hide();

                if (language === 'all') {
                    // Show all containers with a slide-down effect
                    $('.movie-container').each(function(index) {
                        $(this).delay(300 * index).slideDown(500);
                    });
                } else {
                    // Show only the selected language with a fade-in effect
                    $('.movie-container').each(function(index) {
                        if ($(this).data('language') === language) {
                            $(this).delay(300 * index).fadeIn(800);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Anmol's Cineplex</h1>
        <div class="login-section">
            <?php if (isset($_SESSION['username'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p><br>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Filter Buttons -->
    <div class="filter-buttons">
        <button data-language="Marathi">Marathi Movies</button>
        <button data-language="Hindi">Hindi Movies</button>
        <button data-language="English">English Movies</button>
        <button data-language="all">Show All</button>
    </div>

    <!-- Display Movies by Language -->
    <?php
    fetchMoviesByLanguage($conn, "Marathi");
    fetchMoviesByLanguage($conn, "Hindi");
    fetchMoviesByLanguage($conn, "English");
    ?>

</body>
</html>

<?php
$conn->close();
?>

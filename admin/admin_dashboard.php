<?php
session_start();
include '../database/db_connection.php'; // Corrected include path

// Ensure only admin can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch data for the dashboard
$movies = $conn->query("SELECT * FROM movie");
$showtimes = $conn->query(
    "SELECT s.showtime_id, s.show_time, s.total_seats, m.name AS movie_name 
     FROM showtime s
     JOIN movie m ON s.movie_id = m.movie_id"
);
$users = $conn->query("SELECT * FROM users");
$tickets = $conn->query("SELECT * FROM ticket");

// Delete functionality for movies, showtimes, users, and tickets
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_movie'])) {
        $movie_id = $_POST['movie_id'];
        $conn->query("DELETE FROM movie WHERE movie_id = $movie_id");
        header("Location: admin_dashboard.php");
        exit;
    } elseif (isset($_POST['delete_showtime'])) {
        $showtime_id = $_POST['showtime_id'];
        $conn->query("DELETE FROM showtime WHERE showtime_id = $showtime_id");
        header("Location: admin_dashboard.php");
        exit;
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $conn->query("DELETE FROM users WHERE user_id = $user_id");
        header("Location: admin_dashboard.php");
        exit;
    } elseif (isset($_POST['delete_ticket'])) {
        $ticket_id = $_POST['ticket_id'];
        $conn->query("DELETE FROM ticket WHERE ticket_id = $ticket_id");
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
    <h1>Welcome, Admin</h1>
    <nav>
        <ul>
            <li><a href="#manage-movies">Manage Movies</a></li>
            <li><a href="#manage-showtimes">Manage Showtimes</a></li>
            <li><a href="#manage-users">Manage Users</a></li>
            <li><a href="#manage-tickets">Manage Tickets</a></li>
            <li><a href="ticket_reports.php">Ticket Reports</a></li>
            <li><a href="../main.php">Logout</a></li>
        </ul>
    </nav>

    <section id="manage-movies">
        <h2>Manage Movies</h2>
        <form action="add_movie.php" method="get">
            <button type="submit">Add New Movie</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Genre</th>
                    <th>Duration</th>
                    <th>Language</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($movie = $movies->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($movie['name']); ?></td>
                    <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                    <td><?php echo htmlspecialchars($movie['duration']); ?> min</td>
                    <td><?php echo htmlspecialchars($movie['language']); ?></td>
                    <td>
                        <form action="edit_movie.php" method="get" style="display:inline;">
                            <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                            <button type="submit">Edit</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="movie_id" value="<?php echo $movie['movie_id']; ?>">
                            <button type="submit" name="delete_movie">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>

    <section id="manage-showtimes">
            <h2>Manage Showtimes</h2>
            <form action="add_showtime.php" method="get">
                <button type="submit">Add New Showtime</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Showtime</th>
                        <th>Movie</th>
                        <th>Total Seats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($showtime = $showtimes->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($showtime['show_time']); ?></td>
                        <td><?php echo htmlspecialchars($showtime['movie_name']); ?></td>
                        <td><?php echo htmlspecialchars($showtime['total_seats']); ?></td>
                        <td>
                            <!-- Edit Showtime Button -->
                            <form action="edit_showtime.php" method="get" style="display:inline;">
                                <input type="hidden" name="showtime_id" value="<?php echo $showtime['showtime_id']; ?>">
                                <button type="submit">Edit</button>
                            </form>

                            <!-- Delete Showtime Button -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="showtime_id" value="<?php echo $showtime['showtime_id']; ?>">
                                <button type="submit" name="delete_showtime">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
    </section>

    <section id="manage-users">
        <h2>Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </section>

    <section id="manage-tickets">
    <h2>Manage Tickets</h2>

    <!-- Month Selection Form -->
    <form method="get" action="admin_dashboard.php#manage-tickets">
        <label for="month">Select Month:</label>
        <select name="month" id="month">
            <option value="">All</option>
            <option value="01">January</option>
            <option value="02">February</option>
            <option value="03">March</option>
            <option value="04">April</option>
            <option value="05">May</option>
            <option value="06">June</option>
            <option value="07">July</option>
            <option value="08">August</option>
            <option value="09">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <?php
    // Get selected month from the form
    $selected_month = isset($_GET['month']) ? $_GET['month'] : '';

    // Construct query with month filter if selected
    $query = "SELECT t.ticket_id, u.name AS user_name, m.name AS movie_name, s.show_time, 
                    t.date, t.no_of_seats, t.payment_status, t.total_amount 
            FROM ticket t
            JOIN users u ON t.user_id = u.user_id
            JOIN showtime s ON t.showtime_id = s.showtime_id
            JOIN movie m ON s.movie_id = m.movie_id";

    if (!empty($selected_month)) {
        $query .= " WHERE MONTH(t.date) = '$selected_month'";
    }

    $query .= " ORDER BY t.date ASC"; // Order tickets by date
    $tickets = $conn->query($query);

    // Variables for payment summary
    $cash_total = 0;
    $online_total = 0;

    // Variables for day-wise summary
    $daily_summary = [];
    ?>

    <table>
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>User Name</th>
                <th>Movie Name</th>
                <th>Showtime</th>
                <th>Date</th>
                <th>Seats</th>
                <th>Payment Status</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ticket = $tickets->fetch_assoc()) { 
                // Calculate totals based on payment type
                if ($ticket['payment_status'] === 'cash') {
                    $cash_total += $ticket['total_amount'];
                } elseif ($ticket['payment_status'] === 'online') {
                    $online_total += $ticket['total_amount'];
                }

                // Store day-wise summary
                $date = $ticket['date'];
                if (!isset($daily_summary[$date])) {
                    $daily_summary[$date] = ['cash' => 0, 'online' => 0, 'total' => 0];
                }
                if ($ticket['payment_status'] === 'cash') {
                    $daily_summary[$date]['cash'] += $ticket['total_amount'];
                } else {
                    $daily_summary[$date]['online'] += $ticket['total_amount'];
                }
                $daily_summary[$date]['total'] += $ticket['total_amount'];
            ?>
            <tr>
                <td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                <td><?php echo htmlspecialchars($ticket['user_name']); ?></td>
                <td><?php echo htmlspecialchars($ticket['movie_name']); ?></td>
                <td><?php echo htmlspecialchars($ticket['show_time']); ?></td>
                <td><?php echo htmlspecialchars($ticket['date']); ?></td>
                <td><?php echo htmlspecialchars($ticket['no_of_seats']); ?></td>
                <td><?php echo htmlspecialchars($ticket['payment_status']); ?></td>
                <td><?php echo htmlspecialchars($ticket['total_amount']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                        <button type="submit" name="delete_ticket">Delete</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h3>Day-wise Ticket Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Cash Payments</th>
                <th>Online Payments</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($daily_summary as $date => $summary) { ?>
            <tr>
                <td><?php echo htmlspecialchars($date); ?></td>
                <td><?php echo htmlspecialchars($summary['cash']); ?></td>
                <td><?php echo htmlspecialchars($summary['online']); ?></td>
                <td><strong><?php echo htmlspecialchars($summary['total']); ?></strong></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <h3>Payment Summary for 
        <?php echo !empty($selected_month) ? date('F', mktime(0, 0, 0, $selected_month, 1)) : 'All Months'; ?>
    </h3>
    <p>Total Cash Payments: <?php echo $cash_total; ?></p>
    <p>Total Online Payments: <?php echo $online_total; ?></p>
    <p><strong>Total Revenue: <?php echo $cash_total + $online_total; ?></strong></p>

</section>



</body>
</html>

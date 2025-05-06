<?php
session_start();
include 'database/db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/PHPMailer.php';

define('TICKET_PRICE', 200);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to complete the reservation.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['movie_id'], $_POST['showtime_id'], $_POST['seats'], $_POST['payment_status'])) {
    $movie_id = $_POST['movie_id'];
    $showtime_id = $_POST['showtime_id'];
    $seats = $_POST['seats'];
    $payment_status = $_POST['payment_status'];
    $user_id = $_SESSION['user_id'];

    // Check available seats
    $seat_query = "SELECT (50 - IFNULL(SUM(no_of_seats), 0)) AS available_seats FROM ticket WHERE showtime_id = ?";
    $seat_stmt = $conn->prepare($seat_query);
    $seat_stmt->bind_param("i", $showtime_id);
    $seat_stmt->execute();
    $available_seats = $seat_stmt->get_result()->fetch_assoc()['available_seats'] ?? 50;
    $seat_stmt->close();

    if ($seats > $available_seats) {
        die("Only $available_seats seats are available.");
    }

    $total_amount = $seats * TICKET_PRICE;

    // Insert ticket booking into the database
    $stmt = $conn->prepare("INSERT INTO ticket (user_id, showtime_id, date, no_of_seats, payment_status, total_amount) VALUES (?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param("iiisi", $user_id, $showtime_id, $seats, $payment_status, $total_amount);

    if ($stmt->execute()) {
        $ticket_id = $stmt->insert_id;

        // Fetch user details for the email
        $user_query = "SELECT name, username, email FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user = $user_stmt->get_result()->fetch_assoc();
        $user_stmt->close();

        // Send Confirmation Email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'add_Here_your_mail_id'; // SMTP username
            $mail->Password = 'your_key_pass'; // SMTP password (use app password for Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('add_Here_your_mail_id', 'Admin');
            $mail->addAddress($user['email'], $user['name']); // Fetching user email from the database

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Movie Ticket Confirmation';
            $mail->Body = "
                <h2>Ticket Confirmation</h2>
                <p>Dear {$user['name']},</p>
                <p>Your ticket has been successfully booked. Below are your ticket details:</p>
                <ul>
                    <li><strong>Ticket ID:</strong> {$ticket_id}</li>
                    <li><strong>Number of Seats:</strong> {$seats}</li>
                    <li><strong>Total Amount:</strong> ₹{$total_amount}</li>
                    <li><strong>Payment Method:</strong> {$payment_status}</li>
                </ul>
                <p>Thank you for choosing our service. Enjoy your movie!</p>
            ";

            $mail->send();
            echo '<script src="js/jquery-3.7.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            
            echo "<script>
                    $(document).ready(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reservation Confirmed!',
                            text: 'A confirmation email has been sent to your registered email address.',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
        } catch (Exception $e) {
            echo "Ticket booked, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Display ticket information on the screen
        echo '<link rel="stylesheet" href="css/ticket_info.css">';
        echo '<link rel="stylesheet" href="css/print.css" media="print">';

        echo "<center><div class='container' id='ticket-section'>";
        echo "<div class='ticket'>";
        echo "<h1>🎬 Movie Ticket 🎟️</h1>";
        echo "<p><strong>Reservation Confirmed!</strong></p>";
        echo "<p>Your reservation has been successfully processed.</p>";
        echo "<p><strong>Ticket ID:</strong> $ticket_id</p>";
        echo "<p><strong>Seats:</strong> $seats</p>";
        echo "<p><strong>Payment Method:</strong> $payment_status</p>";
        echo "<p><strong>Total Amount:</strong> ₹$total_amount</p>";
        echo "<p><em>Thank you for choosing our service! Enjoy the show!</em></p>";
        echo "</div>";

        echo "<form action='index.php' method='post'><button type='submit'>Go to Main Page</button></form>";
        echo "<button onclick='printTicket()'>🖨️ Print Ticket </button>";
        echo "</div></center>";
       
        // use for this jquery
        echo "<script>
            function printTicket() {
                window.print();
            }
        </script>";

    } else {
        echo "<p>Error while processing your reservation. Please try again.</p>";
    }
    $stmt->close();

} else {
    echo "<p>Invalid request.</p>";
}
$conn->close();
?>

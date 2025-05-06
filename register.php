<?php
// Start session
session_start();

// Include database connection file 
include 'database/db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/PHPMailer.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // No password hashing

    // Server-side validations
    if (!preg_match("/^[a-zA-Z\s]{3,}$/", $name)) {
        $error = "Name must be at least 3 characters long and contain only letters and spaces.";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        $error = "Phone number must be a valid 10-digit number.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if the username or phone already exists
        $checkQuery = "SELECT * FROM users WHERE username = ? OR phone = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $username, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or phone number already exists. Please choose different ones.";
        } else {
            // Insert the user into the database with plain text password
            $insertQuery = "INSERT INTO users (name, phone, email, address, username, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssss", $name, $phone, $email, $address, $username, $password);

            if ($stmt->execute()) {
                // Send Confirmation Email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'add_Here_your_mail_id';
                    $mail->Password = 'key_pass'; //add your mail key password which is you genereted
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('add_Here_your_mail_id', 'Admin');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Successful!';
                    $mail->Body = "<h2>Welcome, {$name}!</h2><p>Your registration was successful. Here are your login details:</p><ul><li><strong>Username:</strong> {$username}</li><li><strong>Password:</strong> {$password}</li></ul><p>Please keep this information safe.</p>";

                    $mail->send();
                    $successMessage = "Registration successful! A confirmation email has been sent.";
                } catch (Exception $e) {
                    $error = "Registration successful, but email could not be sent.";
                }
            } else {
                $error = "Error: Could not register user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/new_register.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <form action="register.php" method="post">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required>

            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="address">Address:</label>
            <input type="text" name="address" required>

            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Register">
        </form>
        <p>Already registered? <a href="login.php">Login here</a></p>
    </div>

    <?php if (isset($successMessage)): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: '<?php echo $successMessage; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location = 'login.php';
            });
        </script>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $error; ?>',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

</body>
</html>

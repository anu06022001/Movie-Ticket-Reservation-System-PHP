<?php
session_start();
include 'database/db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userType = $_POST['user_type'];

    // Determine the table based on the user type
    if ($userType == 'admin') {
        $query = "SELECT * FROM admin WHERE name = ?";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();

        // Simple string comparison for password validation
        if ($password === $row['password']) 
        {
            // Set session variables
            $_SESSION['user_id'] = $row[$userType == 'admin' ? 'admin_id' : 'user_id'];
            $_SESSION['username'] = $username;
            $_SESSION['user_type'] = $userType;

            // Redirect based on the user type
            if ($userType == 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-image: url('img/theater_BG2.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 1rem;
            font-size: 2rem;
            color: #444;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .login-container label {
            font-size: 0.9rem;
            text-align: left;
            color: #555;
        }

        .login-container input, .login-container select {
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .login-container input:focus, .login-container select:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 4px rgba(110, 142, 251, 0.6);
        }

        .login-container input[type="submit"] {
            background: #6e8efb;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background: #5a7bd1;
        }

        .login-container a {
            color: #6e8efb;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <label for="user_type">Login As:</label>
            <select name="user_type" id="user_type" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            
            <input type="submit" value="Login">
        </form>
        <p>New User? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

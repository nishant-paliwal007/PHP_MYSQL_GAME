<?php
session_start();
require_once('connection.php');

// Process login
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Optional: Uncomment for SQL injection prevention
    // $username = mysqli_real_escape_string($conn, $username);
    // $password = mysqli_real_escape_string($conn, $password);

    // Query to fetch user with given username, password, and role='user'
    $query = "SELECT * FROM user WHERE username='$username' AND password='$password' AND role='user'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        // Query execution failed
        die("Query failed: " . mysqli_error($conn));
    }

    // Check if any user with given credentials exists
    if (mysqli_num_rows($result) > 0) {
        // Login successful
        // For simplicity, assuming the first user found is sufficient
        $row = mysqli_fetch_assoc($result);

        // Store user information in session variables
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role'];

        // Redirect to dashboard or any protected page
        header("Location: game.php");
        exit();
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid username, password, or user role.');</script>";
    }
}

mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        /* Center the form vertically and horizontally */
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            /* Optional: Adding a light background color */
        }

        .container {
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card shadow-sm bg-secondary">
            <div class="card-body">
                <h3 class="card-title font-bold text-center text-info mb-4">Login</h3>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label text-white">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label text-white">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="d-grid gap-2 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary w-50" name="submit">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
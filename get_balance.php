<?php
// Include necessary files for database connection and session management
include "./connection.php";
include "./session_check.php";

// Check if the user is logged in and get username from session
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    echo json_encode(array("status" => "error", "message" => "User session not found."));
    exit;
}

$username = $_SESSION["username"];

// Fetch user's current balance from the database
$query_balance = "SELECT balance FROM balance WHERE username = '$username'";
$result_balance = mysqli_query($conn, $query_balance);

if ($result_balance && mysqli_num_rows($result_balance) > 0) {
    $row_balance = mysqli_fetch_assoc($result_balance);
    $currentBalance = $row_balance['balance'];

    echo json_encode(array("status" => "success", "balance" => $currentBalance));
    exit;
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to fetch user balance."));
    exit;
}
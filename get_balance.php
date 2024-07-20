<?php
// get_balance.php

include "./connection.php";
include "./session_check.php";

// Ensure the session is started
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    echo json_encode(array("status" => "error", "message" => "User session not found."));
    exit;
}

$username = $_SESSION["username"];

// Query to get the current balance
$query_balance = "SELECT balance FROM balance WHERE username = '$username'";
$result_balance = mysqli_query($conn, $query_balance);

if ($result_balance) {
    $row_balance = mysqli_fetch_assoc($result_balance);
    $current_balance = $row_balance['balance'];
    echo json_encode(array("status" => "success", "balance" => $current_balance));
} else {
    echo json_encode(array("status" => "error", "message" => "Error fetching user balance."));
}
?>

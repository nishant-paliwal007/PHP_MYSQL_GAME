<?php
include "./connection.php";
include "./session_check.php";

// Check if user session exists
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    echo json_encode(array("status" => "error", "message" => "User session not found."));
    exit;
}

$username = $_SESSION["username"];

// Fetch the current balance of the user
$query = "SELECT balance FROM balance WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        echo json_encode(array("status" => "success", "balance" => $row['balance']));
    } else {
        echo json_encode(array("status" => "error", "message" => "Balance not found."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Error querying balance."));
}

mysqli_close($conn);
?>

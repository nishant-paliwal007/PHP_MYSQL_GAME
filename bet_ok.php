<?php
// Include database connection
include "./connection.php";
include "./session_check.php";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["bet_ok"]) && isset($_POST["total_bet_amount"])) {
    // Retrieve session username (ensure session is started in session_check.php)
    if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
        echo json_encode(array("status" => "error", "message" => "User session not found."));
        exit;
    }
    $username = $_SESSION["username"];

    // Validate and sanitize input data (if needed)
    $total_bet_amount = floatval($_POST["total_bet_amount"]);
    $tickets = isset($_POST['tickets']) ? mysqli_real_escape_string($conn, $_POST['tickets']) : '';

    // Check user's balance before placing the bet
    $query_balance = "SELECT balance FROM balance WHERE username = '$username'";
    $result_balance = mysqli_query($conn, $query_balance);

    if ($result_balance) {
        $row_balance = mysqli_fetch_assoc($result_balance);
        $current_balance = $row_balance['balance'];

        if ($current_balance < $total_bet_amount) {
            echo json_encode(array("status" => "error", "message" => "Insufficient balance."));
            exit;
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Error fetching user balance."));
        exit;
    }

    if ($total_bet_amount < 10) {
        echo json_encode(array("status" => "error", "message" => "Minimum bet amount should be 10."));
        exit;
    }

    // Determine current time and calculate next draw time
    date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST
    $current_time = date("h:i A"); // Example: 06:05 PM
    $current_time_timestamp = strtotime($current_time);

    // Calculate next draw time in 5-minute intervals
    $draw_time_timestamp = ceil($current_time_timestamp / (5 * 60)) * (5 * 60);

    // If the bet is placed after the calculated draw_time, increase draw_time by 5 minutes
    if ($current_time_timestamp >= $draw_time_timestamp) {
        $draw_time_timestamp += (5 * 60);
    }

    $draw_time = date("h:i A", $draw_time_timestamp);

    // Ticket time should not be equal to draw time
    $ticket_time = ($current_time_timestamp >= $draw_time_timestamp) ? date("h:i A", $draw_time_timestamp + (5 * 60)) : $current_time;
    // function to generate the barcode 
    function generateBarcode() {
        $barcode = "";
        for ($i = 0; $i < 10; $i++) {
            $barcode .= (string) rand(1, 9);
        }
        return $barcode;
    }
    $code = generateBarcode();
    $escaped_code = mysqli_real_escape_string($conn, $code);
    $query = "INSERT INTO record_game (username, draw_time, ticket_time, tickets, ticket_date, ticket_qty, ticket_amt, ticket_winning_qty, ticket_winning_amt, winning_number, barcode) 
    VALUES ('$username', '$draw_time', '$ticket_time', '$tickets', CURDATE(), '$total_bet_amount', $total_bet_amount, '', '', '', '$escaped_code')";

    if (mysqli_query($conn, $query)) {
        // Deduct the bet amount from user's balance
        $new_balance = $current_balance - $total_bet_amount;
        $update_balance_query = "UPDATE balance SET balance = $new_balance WHERE username = '$username'";
        if (mysqli_query($conn, $update_balance_query)) {
            echo json_encode(array("status" => "success", "message" => "Bet placed successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update user balance."));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Error placing the bet."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request."));
}

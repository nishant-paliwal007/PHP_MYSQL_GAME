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

    if ($total_bet_amount < 10) {
        echo json_encode(array("status" => "error", "message" => "Minimum bet amount should be 10."));
        exit;
    }

    // Determine current time and calculate next draw time
    date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST
    $current_time = date("h:i A"); // Example: 01:07 PM
    $current_time_timestamp = strtotime($current_time);

    // Calculate next draw time in 5-minute intervals
    $draw_time_timestamp = ceil($current_time_timestamp / (5 * 60)) * (5 * 60);
    $draw_time = date("h:i A", $draw_time_timestamp);

    // Ticket time is the current time when the bet is placed
    $ticket_time = $current_time;

    // Prepare to handle betting numbers (1 to 12)
    $betting_numbers = [];
    for ($i = 1; $i <= 12; $i++) {
        $input_name = 'bet_input_' . $i;
        if (isset($_POST[$input_name])) {
            $betting_numbers[$i] = floatval($_POST[$input_name]);
        }
    }

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

    // Insert bet into record_game table
    $tickets = '';
    $ticket_qty = '';
    $ticket_winning_qty = '';
    $ticket_winning_amt = '';
    $winning_number = '';
    $barcode = '';

    foreach ($betting_numbers as $number => $amount) {
        $tickets .= "$number * $amount, ";
        $ticket_qty .= "1, "; // Assuming each bet is one ticket
        $ticket_winning_qty .= "0, "; // Assuming no winning quantity initially
        $ticket_winning_amt .= "0, "; // Assuming no winning amount initially
        $winning_number .= "'', "; // Assuming no winning number initially
        $barcode .= "'', "; // Assuming no barcode initially
    }

    $tickets = rtrim($tickets, ", ");
    $ticket_qty = rtrim($ticket_qty, ", ");
    $ticket_winning_qty = rtrim($ticket_winning_qty, ", ");
    $ticket_winning_amt = rtrim($ticket_winning_amt, ", ");
    $winning_number = rtrim($winning_number, ", ");
    $barcode = rtrim($barcode, ", ");

    $query_insert_bet = "INSERT INTO record_game (username, draw_time, ticket_time, tickets, ticket_date, ticket_qty, ticket_amt, ticket_winning_qty, ticket_winning_amt, winning_number, barcode) VALUES ('$username', '$draw_time', '$ticket_time', '$tickets', CURDATE(), '$ticket_qty', '$total_bet_amount', '$ticket_winning_qty', '$ticket_winning_amt', '$winning_number', '$barcode')";

    $result_insert = mysqli_query($conn, $query_insert_bet);
    if ($result_insert) {
        // Deduct the bet amount from user's balance
        $new_balance = $current_balance - $total_bet_amount;
        $query_update_balance = "UPDATE balance SET balance = '$new_balance' WHERE username = '$username'";
        $result_update = mysqli_query($conn, $query_update_balance);

        if ($result_update) {
            // Return success response
            echo json_encode(array("status" => "success", "message" => "Bet placed successfully. Ticket Amount: $total_bet_amount"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update user balance after bet placement."));
        }
    } else {
        // Return error response if insert failed
        echo json_encode(array("status" => "error", "message" => "Failed to place bet."));
    }

    // Close database connection
    mysqli_close($conn);
} else {
    // Handle if bet_ok parameter is not set
    echo json_encode(array("status" => "error", "message" => "Invalid request."));
}
?>

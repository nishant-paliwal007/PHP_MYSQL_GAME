<?php
include "./connection.php";
include "./session_check.php";

// Check if user session exists
if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
    echo json_encode(array("status" => "error", "message" => "User session not found."));
    exit;
}

$username = $_SESSION["username"];

// Retrieve the current time in 'h:i A' format
date_default_timezone_set('Asia/Kolkata');
$current_time = date('h:i A');
$current_date = date('Y-m-d');

// Display the current time
echo "Current Time: $current_time<br>";

// Adjust the logic to get the correct draw_time
$draw_time_query = "
    SELECT draw_time 
    FROM record_game 
    WHERE ticket_date = '$current_date'
      AND username = '$username'
      AND draw_time = '$current_time'
    LIMIT 1
";

// Display the draw time query
echo "Draw Time Query: $draw_time_query<br>";

$draw_time_result = mysqli_query($conn, $draw_time_query);

if ($draw_time_result && mysqli_num_rows($draw_time_result) > 0) {
    $draw_time_row = mysqli_fetch_assoc($draw_time_result);
    $draw_time = $draw_time_row['draw_time'];

    // Display retrieved draw_time
    echo "Retrieved draw_time: $draw_time<br>";

    // Fetch result from result_single
    $result_query = "SELECT res FROM result_single WHERE res_time = '$draw_time' AND res_date = '$current_date'";
    $result = mysqli_query($conn, $result_query);

    // Display the result query
    echo "Result Query: $result_query<br>";

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $winning_number = $row['res'];

        // Display retrieved winning_number
        echo "Retrieved winning_number: $winning_number<br>";

        // Update the winning_number in record_game table
        $update_query = "
            UPDATE record_game 
            SET winning_number = '$winning_number' 
            WHERE draw_time = '$draw_time' 
              AND username = '$username'
              AND ticket_date = '$current_date'
        ";

        // Display the update query
        echo "Update Query: $update_query<br>";

        if (mysqli_query($conn, $update_query)) {
            // Display success message
            echo "Successfully updated winning number for draw_time: $draw_time<br>";

            // Calculate total winning amount
            $tickets_query = "
                SELECT tickets 
                FROM record_game 
                WHERE draw_time = '$draw_time' 
                  AND username = '$username'
                  AND ticket_date = '$current_date'
            ";

            $tickets_result = mysqli_query($conn, $tickets_query);
            $total_winning_amount = 0;

            while ($ticket_row = mysqli_fetch_assoc($tickets_result)) {
                $tickets = $ticket_row['tickets'];
                $ticket_list = explode(', ', $tickets);

                foreach ($ticket_list as $ticket) {
                    list($amount, $number) = explode('*', $ticket);

                    if ($number == $winning_number) {
                        $total_winning_amount += $amount * 11; // Assuming a fixed multiplier of 11
                    }
                }
            }

            // Update balance for the user
            $update_balance_query = "
                UPDATE balance 
                SET balance = balance + $total_winning_amount 
                WHERE username = '$username'
            ";

            if (mysqli_query($conn, $update_balance_query)) {
                echo json_encode(array(
                    "status" => "success",
                    "winning_number" => $winning_number,
                    "winning_amount" => $total_winning_amount
                ));
            } else {
                echo "Error updating user balance: " . mysqli_error($conn) . "<br>";
                echo json_encode(array("status" => "error", "message" => "Error updating user balance."));
            }
        } else {
            // Display SQL error
            echo "Error updating winning number: " . mysqli_error($conn) . "<br>";
            echo json_encode(array("status" => "error", "message" => "Error updating winning number."));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "No result found for current time."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "No draw time found for the specified criteria."));
}

// Close connection
mysqli_close($conn);
?>

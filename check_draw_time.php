<?php
// include "./connection.php";
// include "./session_check.php";

// // Check if user session exists
// if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
//     echo json_encode(array("status" => "error", "message" => "User session not found."));
//     exit;
// }

// $username = $_SESSION["username"];

// // Retrieve the current time in 'h:i A' format
// date_default_timezone_set('Asia/Kolkata');
// $current_time = date('h:i A');
// $current_date = date('Y-m-d');

// // Display the current time
// echo "Current Time: $current_time<br>";

// // Calculate the next draw time based on the current time
// $current_time_timestamp = strtotime($current_time);
// $draw_time_timestamp = ceil($current_time_timestamp / (5 * 60)) * (5 * 60);
// $draw_time = date("h:i A", $draw_time_timestamp);

// // Display the calculated draw_time
// echo "Calculated draw_time: $draw_time<br>";

// // Check if the current time matches the draw time
// if ($current_time === $draw_time) {
//     // Fetch result from result_single
//     $result_query = "SELECT res FROM result_single WHERE res_time = '$draw_time' AND res_date = '$current_date'";
//     $result = mysqli_query($conn, $result_query);

//     // Display the result query
//     echo "Result Query: $result_query<br>";

//     if ($result && mysqli_num_rows($result) > 0) {
//         $row = mysqli_fetch_assoc($result);
//         $winning_number = $row['res'];

//         // Display retrieved winning_number
//         echo "Retrieved winning_number: $winning_number<br>";

//         // Update winning_number and ticket_winning_amt in record_game table only if not processed
//         $update_query = "
//             UPDATE record_game 
//             SET winning_number = '$winning_number',
//                 ticket_winning_amt = CASE 
//                     WHEN tickets LIKE '%$winning_number%' THEN ticket_qty * 11 
//                     ELSE ticket_winning_amt
//                 END,
//                 is_processed = 1
//             WHERE draw_time = '$draw_time' 
//               AND username = '$username'
//               AND ticket_date = '$current_date'
//               AND is_processed = 0
//         ";

//         // Display the update query
//         echo "Update Query: $update_query<br>";

//         if (mysqli_query($conn, $update_query)) {
//             if (mysqli_affected_rows($conn) > 0) {
//                 // Display success message
//                 echo "Successfully updated winning number and ticket_winning_amt for draw_time: $draw_time<br>";

//                 // Calculate total winning amount
//                 $tickets_query = "
//                     SELECT tickets 
//                     FROM record_game 
//                     WHERE draw_time = '$draw_time' 
//                       AND username = '$username'
//                       AND ticket_date = '$current_date'
//                       AND is_processed = 1
//                 ";

//                 $tickets_result = mysqli_query($conn, $tickets_query);
//                 $total_winning_amount = 0;

//                 while ($ticket_row = mysqli_fetch_assoc($tickets_result)) {
//                     $tickets = $ticket_row['tickets'];
//                     $ticket_list = explode(', ', $tickets);

//                     foreach ($ticket_list as $ticket) {
//                         list($amount, $number) = explode('*', $ticket);

//                         if ($number == $winning_number) {
//                             $total_winning_amount += $amount * 11; // Assuming a fixed multiplier of 11
//                         }
//                     }
//                 }

//                 // Update balance for the user
//                 $update_balance_query = "
//                     UPDATE balance 
//                     SET balance = balance + $total_winning_amount 
//                     WHERE username = '$username'
//                 ";

//                 if (mysqli_query($conn, $update_balance_query)) {
//                     echo json_encode(array(
//                         "status" => "success",
//                         "winning_number" => $winning_number,
//                         "winning_amount" => $total_winning_amount
//                     ));
//                 } else {
//                     echo "Error updating user balance: " . mysqli_error($conn) . "<br>";
//                     echo json_encode(array("status" => "error", "message" => "Error updating user balance."));
//                 }
//             } else {
//                 echo json_encode(array("status" => "error", "message" => "No new updates to process."));
//             }
//         } else {
//             // Display SQL error
//             echo "Error updating winning number and ticket_winning_amt: " . mysqli_error($conn) . "<br>";
//             echo json_encode(array("status" => "error", "message" => "Error updating winning number and ticket_winning_amt."));
//         }
//     } else {
//         echo json_encode(array("status" => "error", "message" => "No result found for current time."));
//     }
// } else {
//     echo json_encode(array("status" => "error", "message" => "Not yet draw time."));
// }

// // Close connection
// mysqli_close($conn);
?>

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

// Calculate the next draw time based on the current time
$current_time_timestamp = strtotime($current_time);
$draw_time_timestamp = ceil($current_time_timestamp / (5 * 60)) * (5 * 60);
$draw_time = date("h:i A", $draw_time_timestamp);

// Check if the current time matches the draw time
if ($current_time === $draw_time) {
    // Fetch result from result_single
    $result_query = "SELECT res FROM result_single WHERE res_time = '$draw_time' AND res_date = '$current_date'";
    $result = mysqli_query($conn, $result_query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $winning_number = $row['res'];

        // Update winning_number and ticket_winning_amt in record_game table only if not processed
        $update_query = "
            UPDATE record_game 
            SET winning_number = '$winning_number',
                ticket_winning_amt = CASE 
                    WHEN tickets LIKE '%$winning_number%' THEN ticket_qty * 11 
                    ELSE ticket_winning_amt
                END,
                is_processed = 1
            WHERE draw_time = '$draw_time' 
              AND username = '$username'
              AND ticket_date = '$current_date'
              AND is_processed = 0
        ";

        if (mysqli_query($conn, $update_query)) {
            if (mysqli_affected_rows($conn) > 0) {
                // Calculate total winning amount
                $tickets_query = "
                    SELECT tickets 
                    FROM record_game 
                    WHERE draw_time = '$draw_time' 
                      AND username = '$username'
                      AND ticket_date = '$current_date'
                      AND is_processed = 1
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
                    echo json_encode(array("status" => "error", "message" => "Error updating user balance."));
                }
            } else {
                echo json_encode(array("status" => "error", "message" => "No new updates to process."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Error updating winning number and ticket_winning_amt."));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "No result found for current time."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Not yet draw time."));
}

// Close connection
mysqli_close($conn);
?>


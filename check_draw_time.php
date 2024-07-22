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

// // Calculate the next draw time based on the current time
// $current_time_timestamp = strtotime($current_time);
// $draw_time_timestamp = ceil($current_time_timestamp / (5 * 60)) * (5 * 60);
// $draw_time = date("h:i A", $draw_time_timestamp);

// // Check if the current time matches the draw time
// if ($current_time === $draw_time) {
//     // Fetch result from result_single
//     $result_query = "SELECT res FROM result_single WHERE res_time = '$draw_time' AND res_date = '$current_date'";
//     $result = mysqli_query($conn, $result_query);

//     if ($result && mysqli_num_rows($result) > 0) {
//         $row = mysqli_fetch_assoc($result);
//         $winning_number = $row['res'];

//         // Fetch all relevant records for the user
//         $fetch_tickets_query = "
//             SELECT id, tickets 
//             FROM record_game 
//             WHERE draw_time = '$draw_time' 
//               AND username = '$username'
//               AND ticket_date = '$current_date'
//               AND is_processed = 0
//         ";

//         $tickets_result = mysqli_query($conn, $fetch_tickets_query);
//         $total_winning_amount = 0;

//         while ($ticket_row = mysqli_fetch_assoc($tickets_result)) {
//             $record_id = $ticket_row['id'];
//             $tickets = $ticket_row['tickets'];
//             $ticket_list = explode(',', $tickets);
//             $winning_amount = 0;

//             foreach ($ticket_list as $ticket) {
//                 list($amount, $number) = explode('*', trim($ticket));

//                 if ($number == $winning_number) {
//                     $winning_amount += $amount * 11;
//                 }
//             }

//             // Update the winning amount for this record
//             $update_record_query = "
//                 UPDATE record_game 
//                 SET winning_number = '$winning_number',
//                     ticket_winning_amt = $winning_amount,
//                     is_processed = 1
//                 WHERE id = $record_id
//             ";

//             if (mysqli_query($conn, $update_record_query)) {
//                 $total_winning_amount += $winning_amount;
//             } else {
//                 echo json_encode(array("status" => "error", "message" => "Error updating record ID $record_id."));
//                 mysqli_close($conn);
//                 exit;
//             }
//         }

//         // Update balance for the user
//         $update_balance_query = "
//             UPDATE balance 
//             SET balance = balance + $total_winning_amount 
//             WHERE username = '$username'
//         ";

//         if (mysqli_query($conn, $update_balance_query)) {
//             echo json_encode(array(
//                 "status" => "success",
//                 "winning_number" => $winning_number,
//                 "winning_amount" => $total_winning_amount
//             ));
//         } else {
//             echo json_encode(array("status" => "error", "message" => "Error updating user balance."));
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

        // Fetch all relevant records for the user
        $fetch_tickets_query = "
            SELECT id, tickets 
            FROM record_game 
            WHERE draw_time = '$draw_time' 
              AND username = '$username'
              AND ticket_date = '$current_date'
              AND is_processed = 0
        ";

        $tickets_result = mysqli_query($conn, $fetch_tickets_query);
        $total_winning_amount = 0;
        $bet_placed = false;

        while ($ticket_row = mysqli_fetch_assoc($tickets_result)) {
            $record_id = $ticket_row['id'];
            $tickets = $ticket_row['tickets'];
            $ticket_list = explode(',', $tickets);
            $winning_amount = 0;

            foreach ($ticket_list as $ticket) {
                list($amount, $number) = explode('*', trim($ticket));

                if ($number == $winning_number) {
                    $winning_amount += $amount * 11;
                    $bet_placed = true; // A bet was placed
                }
            }

            // Update the winning amount for this record
            $update_record_query = "
                UPDATE record_game 
                SET winning_number = '$winning_number',
                    ticket_winning_amt = $winning_amount,
                    is_processed = 1
                WHERE id = $record_id
            ";

            if (mysqli_query($conn, $update_record_query)) {
                $total_winning_amount += $winning_amount;
            } else {
                echo json_encode(array("status" => "error", "message" => "Error updating record ID $record_id."));
                mysqli_close($conn);
                exit;
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
                "winning_amount" => $total_winning_amount,
                "bet_placed" => $bet_placed // Include bet status in the response
            ));
        } else {
            echo json_encode(array("status" => "error", "message" => "Error updating user balance."));
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

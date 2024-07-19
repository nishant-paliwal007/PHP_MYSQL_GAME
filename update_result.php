<?php
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i'); // 24-hour format

// Initialize the response array
$response = [];

if ($conn) {
    // Find the latest result for the current date and time
    $query = "SELECT * FROM result_single WHERE res_date = '$current_date' AND STR_TO_DATE(res_time, '%h:%i %p') <= STR_TO_DATE('$current_time', '%H:%i') ORDER BY STR_TO_DATE(res_time, '%h:%i %p') DESC LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $response['winner'] = $row['res']; // Assuming 'res' column holds the winning number
        } else {
            $response['error'] = "No results found for the current date and time.";
        }
    } else {
        $response['error'] = "Query failed: " . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
} else {
    $response['error'] = "Failed to connect to the database.";
}

// Set content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);

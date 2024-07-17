<?php
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get the current date
$current_date = date('Y-m-d');

// Check if data for the current date is already present
$query_check = "SELECT COUNT(*) as count FROM result_single WHERE res_date = '$current_date'";
$result_check = mysqli_query($conn, $query_check);
$row_check = mysqli_fetch_assoc($result_check);

if ($row_check['count'] == 0) {
    // Data not present for the current date, insert data
    $start_time = strtotime('08:00 AM');
    $end_time = strtotime('10:00 PM');
    $interval = 5 * 60; // 5 minutes in seconds

    for ($time = $start_time; $time <= $end_time; $time += $interval) {
        $res_time = date('h:i A', $time); // Format time as hh:mm AM/PM
        $res = rand(1, 12); // Random number between 1 and 12

        // Insert data into the database
        $query_insert = "INSERT INTO result_single (res, res_time, res_date) VALUES ('$res', '$res_time', '$current_date')";
        if (mysqli_query($conn, $query_insert)) {
            error_log("Inserted: $res at $res_time on $current_date");
        } else {
            error_log("Error inserting data: " . mysqli_error($conn));
        }
    }
} else {
    error_log("Data for $current_date already exists.");
}

// Close connection
mysqli_close($conn);
?>

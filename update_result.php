<?php
include './connection.php';

date_default_timezone_set('Asia/Kolkata');

$current_time = new DateTime();
$current_hour = (int) $current_time->format('H');

if ($current_hour >= 8 && $current_hour < 22) {
    // Determine the time interval (e.g., every 5 minutes)
    $interval_minutes = 5;
    $current_minute = (int) $current_time->format('i');
    $rounded_minute = floor($current_minute / $interval_minutes) * $interval_minutes;

    // Set the exact result time
    $res_time = $current_time->format('h:i A');
    $res_date = $current_time->format('Y-m-d');

    // Check if current time matches the next expected interval
    if ($current_minute % $interval_minutes == 0) {
        // Check if a result already exists for this exact time
        $check_query = "SELECT * FROM result_single WHERE res_time = '$res_time'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) == 0) {
            // Generate a new random number
            $random_number = rand(1, 12);

            // Insert the new result
            $insert_query = "INSERT INTO result_single (res, res_time, res_date) VALUES ('$random_number', '$res_time', '$res_date')";
            mysqli_query($conn, $insert_query);

            echo json_encode(['winner' => $random_number]);
        } else {
            echo json_encode(['message' => 'Result already exists for this time']);
        }
    } else {
        echo json_encode(['message' => 'Not yet time for next interval']);
    }
} else {
    echo json_encode(['message' => 'Outside operating hours']);
}
?>

<?php
include './connection.php';

date_default_timezone_set('Asia/Kolkata');

$current_time = new DateTime();
$current_hour = (int) $current_time->format('H');

if ($current_hour >= 8 && $current_hour < 22) {
    $random_number = rand(1, 12);

    $res_time = $current_time->format('h:i A');
    $res_date = $current_time->format('Y-m-d');

    $query = "INSERT INTO result_single (res, res_time, res_date) VALUES ('$random_number', '$res_time', '$res_date')";
    mysqli_query($conn, $query);

    echo json_encode(['winner' => $random_number]);
}
?>

<?php
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('h:i A'); // Format time as hh:mm AM/PM

// Find the latest result for the current date and time
$query = "SELECT * FROM result_single WHERE res_date = '$current_date' AND STR_TO_DATE(res_time, '%h:%i %p') <= STR_TO_DATE('$current_time', '%h:%i %p') ORDER BY STR_TO_DATE(res_time, '%h:%i %p') DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$response = [];

if ($row) {
    $response['winner'] = $row['res'];
    echo $response['winner'];
}

header('Content-Type: application/json');

// Close connection
mysqli_close($conn);
?>
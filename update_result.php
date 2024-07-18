<?php
// include "./connection.php";

// date_default_timezone_set('Asia/Kolkata');

// // Get the current date and time
// $current_date = date('Y-m-d');
// $current_time = date('H:i:s');

// // Find the latest result for the current date and time
// $query = "SELECT * FROM result_single WHERE res_date = '$current_date' AND res_time <= '$current_time' ORDER BY res_time DESC LIMIT 1";
// $result = mysqli_query($conn, $query);
// $row = mysqli_fetch_assoc($result);

// $response = [];

// if ($row) {
//     $response['winner'] = $row['res'];
//     echo $response['winner'];
// }

// header('Content-Type: application/json');
// // echo json_encode($response);

// // Close connection
// mysqli_close($conn);
?>

<?php
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Find the latest result for the current date and time
$query = "SELECT * FROM result_single WHERE res_date = '$current_date' AND res_time <= '$current_time' ORDER BY res_time DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$response = [];

if ($row) {
    $response['winner'] = $row['res']; // Assuming 'res' column holds the winning number
}

// Close connection
mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode($response);
?>


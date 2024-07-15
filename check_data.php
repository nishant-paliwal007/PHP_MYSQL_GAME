<?php
// this script is for checking that if data is present for today/tomorrow date or not!!
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get tomorrow's date
$current_date = date('Y-m-d');
$tomorrow_date = date('Y-m-d', strtotime('+1 day'));

// Check if data for tomorrow's date is already present
$query_check = "SELECT COUNT(*) as count FROM result_single WHERE res_date = '$current_date'";
$result_check = mysqli_query($conn, $query_check);
$row_check = mysqli_fetch_assoc($result_check);

if ($row_check['count'] > 0) {
    echo "Data for tomorrow's date ($current_date) exists in the database.";
} else {
    echo "No data found for tomorrow's date ($current_date) in the database.";
}

// Close connection
mysqli_close($conn);
?>

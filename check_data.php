<?php
// this script is for checking that if data is present for today/tomorrow date or not!!
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get today's date
$current_date =  date('Y-m-d');
$tomorrow_date = date('Y-m-d', strtotime('+1 day'));


// Check if data for today's date is already present
$query_check = "SELECT COUNT(*) as count FROM result_single WHERE res_date = '$current_date'";
$result_check = mysqli_query($conn, $query_check);
$row_check = mysqli_fetch_assoc($result_check);

if ($row_check['count'] > 0) {
    echo "<h3 style='text-align: center;'>" . "Data for today's date ($current_date) exists in the database." . "</h3>";

    // Fetch all results for today
    $query_results = "SELECT * FROM result_single WHERE res_date = '$current_date'";
    $result_results = mysqli_query($conn, $query_results);

    if (mysqli_num_rows($result_results) > 0) {
        echo "<center>";
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Result</th>
                    <th>Result Time</th>
                    <th>Date</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result_results)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['res'] . "</td>";
            echo "<td>" . $row['res_time'] . "</td>";
            echo "<td>" . $row['res_date'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</center>";
    } else {
        echo "No results found for today.";
    }
} else {
    echo "No data found for today's date ($current_date) in the database.";
}

// Close connection
mysqli_close($conn);

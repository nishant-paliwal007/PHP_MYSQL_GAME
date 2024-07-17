<?php
include "./connection.php";

date_default_timezone_set('Asia/Kolkata');

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i'); // 24-hour format

// Check if it's before 08:00 AM
if ($current_time < '08:00') {
    // Calculate previous day
    $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));

    // Fetch the latest 5 results for the previous day based on ID descending
    $query = "SELECT * FROM result_single WHERE res_date = '$previous_date' ORDER BY id DESC LIMIT 5";
} else {
    // Fetch the latest 5 results for the current day and time range
    $query = "SELECT * FROM result_single WHERE res_date = '$current_date' AND STR_TO_DATE(res_time, '%h:%i %p') <= STR_TO_DATE('$current_time', '%H:%i') ORDER BY STR_TO_DATE(res_time, '%h:%i %p') DESC LIMIT 5";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    // Debugging output
    error_log("Error executing query: " . mysqli_error($conn));
    die("Error executing query: " . mysqli_error($conn));
}

$results = [];

while ($row = mysqli_fetch_assoc($result)) {
    $results[] = $row;
}

// Debugging output
error_log("Fetched results: " . json_encode($results));

// Close connection
mysqli_close($conn);
?>

<table class="result-table">
    <thead>
        <tr>
            <th style="padding: 5px; font-style: italic; color: red; text-decoration: underline;">Time</th>
            <th style="font-style: italic; color: red; text-decoration: underline;">Winner</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $row) : ?>
            <tr>
                <td><?php echo date('h:i A', strtotime($row['res_time'])); ?></td>
                <td><img style="width: 50px; height:50px;" src="./images/<?php echo $row['res']; ?>.png" alt="<?php echo $row['res']; ?>"></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
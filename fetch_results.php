<?php
include "./connection.php";

// Fetch the 5 latest results based on id in descending order
$query = "SELECT * FROM result_single ORDER BY id DESC LIMIT 5";
$result = mysqli_query($conn, $query);

// Check if there are results
if (mysqli_num_rows($result) > 0) {
    // Start the table and header row
    echo '<table border="1">';
    echo '<tr>';
    echo '<th style="padding: 5px; text-decoration: underline; font-style: italic; color: red;">Time</th>';
    echo '<th style="padding: 5px; text-decoration: underline; font-style: italic; color: red;">Winner</th>';
    echo '</tr>';

    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        // Format time to HH:MM AM/PM
        $formatted_time = date("h:i A", strtotime($row["res_time"]));
        
        // Get image URL based on result number
        $image_url = "./images/{$row["res"]}.png";

        // Output table row with time on left and image on right
        echo '<tr>';
        echo "<td style='padding: 5px;'>$formatted_time</td>";
        echo "<td style='padding: 5px;'><img src='$image_url' alt='{$row["res"]}' style='max-width: 50px;'></td>";
        echo '</tr>';
    }

    // Close the table
    echo '</table>';
} else {
    echo "No results found";
}

// Close connection
mysqli_close($conn);
?>

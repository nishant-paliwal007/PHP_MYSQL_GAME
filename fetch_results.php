<?php
include './connection.php';

$query = "SELECT res, res_time FROM result_single ORDER BY id DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $time = $row['res_time'];
    $res = $row['res'];
    echo "<tr><td><div><span>{$time}</span><img class='result-num-img-tb' src='./images/{$res}.png' alt=''></div></td></tr>";
}
?>

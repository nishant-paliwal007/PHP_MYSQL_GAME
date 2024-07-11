<?php
// initializing variables for the connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "my_game";
// make connection
$conn = mysqli_connect($servername, $username, $password, $database);
// check connection
if(!$conn) {
    die("connection failed");
}

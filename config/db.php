<?php
$host = "localhost";
$user = "yash";
$pass = "Yash@123";
$db   = "onlinejobportal_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

?>


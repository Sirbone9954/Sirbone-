<?php
$host = "localhost";
$db = "las_vegas_hostel";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("DB connection failed: " . $conn->connect_error);
?>
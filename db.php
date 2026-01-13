<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sugarcloudcafe"; // Pastikan nama database ini betul

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
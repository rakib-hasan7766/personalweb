<?php
$host = "dpg-d85q0mnavr4c73d62fog-a";
$user = "perwebb";
$password = "NGeh1PNCgSo3e36xH5oTrQqUBFPXMsx3";
$dbname = "perweb";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>

<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "myfreshtaste_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_errno) {
    die("DB connect failed: " . $conn->connect_error);
}
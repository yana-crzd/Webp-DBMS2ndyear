<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "myfreshtaste_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error)
  die("Connection failed: " . $conn->connect_error);

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
  echo "SUCCESS";
} else {
  echo "ERROR";
}

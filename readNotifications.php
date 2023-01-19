<?php

// include("dbconnection.php");
$conn = mysqli_connect("localhost", "root", "", "icueriou_dashrep") or die("Database connection failed");

$sql = "UPDATE comment SET status='0'";
$res = mysqli_query($conn, $sql);
if ($res) {
  echo "Success";
} else {
  echo "Failed";
}

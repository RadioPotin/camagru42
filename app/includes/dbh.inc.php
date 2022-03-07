<?php
$serverNAME = "localhost";
$dbUIDs = "root";
$dbPWD = "";
$dbNAME = "login";

// Create connection
$conn = mysqli_connect($serverNAME, $dbUIDs, $dbPWD, $dbNAME);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_close($conn);

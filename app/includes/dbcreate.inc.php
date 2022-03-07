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

// Create database
$sql = "CREATE DATABASE $dbName";

if (mysqli_query($conn, $sql)) {
  echo "Database created successfully";
} else {
  echo "Error creating database: " . mysqli_error($conn);
}

$create_table =
  "CREATE TABLE users (
    login_id INT NOT NULL AUTOINCREMENT,
    username varchar(128) NOT NULL,
    email varchar(128) NOT NULL,
    userid varchar(128) NOT NULL,
    userpwd varchar (128) NOT NULL,
    PRIMARY KEY ( login_id, email )
);";
$mytable = mysqli_query($conn, $create_table);

mysqli_close($conn);

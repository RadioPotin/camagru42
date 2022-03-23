<?php
include_once 'include.php';
$username = $_SESSION["username"];
$br = "<br />";
$delete = "<p>This will be a link to lead to account deletion</p>";
$welcome = "<h1>Welcome, " . $username . "!</h1>" . $br;
$information = "Some HTML displaying user information" . $br;
$preferences = "Some HTML displaying user preferences" . $br;
$body = $welcome . $information . $preferences . $delete ;
include_once "template.php";
?>

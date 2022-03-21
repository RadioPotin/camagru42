<?php
include_once 'include.php';
$username = $_SESSION["username"];
$body = '<h1>THIS WILL HOLD ALL OF '.$username.'\'S INFORMATION AND PREFERENCES!</h1>';
include ('template.php');
?>

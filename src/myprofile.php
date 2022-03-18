<?php
if(!isset($_SESSION))
{
    session_start();
}
$username = $_SESSION["username"];
$body = '<h1>THIS WILL HOLD ALL OF '.$username.'\'S INFORMATION AND PREFERENCES!</h1>';
include ('template.php');
?>

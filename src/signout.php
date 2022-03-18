<?php
if(!isset($_SESSION))
{
    session_start();
}
unset($_SESSION["user"]);
unset($_SESSION["username"]);

include("index.php");
?>

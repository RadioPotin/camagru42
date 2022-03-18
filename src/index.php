<?php
include "dbh.php";
if (!isset($_SESSION))
{
    session_start();
}
if (!isset($_SESSION["db"]) || empty($_SESSION["db"]))
{
    connect_todb();
}
$body = '<h1>Uuuuuh</h1><h3>stuff i guess</h3>';
include("template.php");
?>

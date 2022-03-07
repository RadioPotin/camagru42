<?php

if (isset($_POST["submit"]))
{
  $name = $_POST["name"];
  $email = $_POST["email"];
  $username = $_POST["uid"];
  $pwd = $_POST["pwd"];
  $pwdd = $_POST["pwdd"];
  require_once 'dbh.inc.php';
  require_once 'functions.inc.php';

  if (empty_input_signup($name, $email, $username, $pwd, $pwdd) !== false)
  {
    header("location: ../signup.php?error=emptyinput");
    exit();
  }
  if (invalid_uid($username) !== false)
  {
    header("location: ../signup.php?error=invaliduid");
    exit();
  }
  if (invalid_email($email) !== false)
  {
    header("location: ../signup.php?error=invalidemail");
    exit();
  }
  if (pwdmatch($pwd, $pwdd) !== false)
  {
    header("location: ../signup.php?error=pwddontmatch");
    exit();
  }
  if (uidexists(conn, $username, $email) !== false)
  {
    header("location: ../signup.php?error=usernametaken");
    exit();
  }
  createuser($conn, $name, $email, $username, $pwd);
}
else {
  header("location: ../signup.php");
  exit();
}

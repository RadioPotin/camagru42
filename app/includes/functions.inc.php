<?php

function empty_input_signup ($name, $email, $username, $pwd, $pwdd)
{
  $result;
  if (empty($name) || empty($email) || empty($email)
    || empty($email) || empty($email))
  {
    $result = true;
  }
  else {
    $result = false;
  }
}

function invalid_uid ($username)
{
  $result;
  if (!preg_match("/^[a-zA-Z0-9]*$/", $username))
  {
    $result = true;
  }
  else {
    $result = false;
  }
}

function invalid_email ($email)
{
  $result;
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
  {
    $result = true;
  }
  else {
    $result = false;
  }
}

function pwdmatch ($pwd, $pwdd)
{
  $result;
  if ($pwd !== $pwdd)
  {
    $result = true;
  }
  else {
    $result = false;
  }
}

function uidexists ($conn, $username, $email)
{
  $sql = "SELECT * FROM users WHERE userid = ? OR email = ?;";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql))
  {
    header("location : ../signup.php?error=sqlstatementfailed");
    exit();
  }

  mysqli_stmt_bind_param($stmt, "ss", $username, $email);
  mysqli_stmt_execute($stmt);

  $resultdata = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($resultdata))
  {
    return $row;
  }
  else
  {
    $result = false;
    return $result;
  }
  mysqli_stmt_close($stmt);
}

function createuser ($conn, $name, $email, $username, $pwd)
{
  $sql = "INSERT INTO users (username, email, userid, userpwd) VALUES (?, ?, ?, ?);";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql))
  {
    header("location : ../signup.php?error=sqlstatementfailed");
    exit();
  }

  $hashpwd = password_hash($pwd, PASSWORD_DEFAULT);
  mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $userid, $hashpwd);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  header("location: ../signup.php?error=none");
  exit();
}

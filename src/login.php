<?php
if(!isset($_SESSION))
{
  session_start();
}
include_once 'lib.php';

// if you get to the login.php without actually
// filling the required form
if (isset($_POST["submit"])) {
  //if you did, but token does NOT match
  if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
    // TODO PROPER ERROR PAGE
    // for now return 405 http status code
    echo "EXITING LOGIN BAD TOKEN";
    exit;
  } else {
    //SUBMITTED AND TOKEN MATCHED
    //
    // process the form
    $username = $_POST["name"];
    $pwd = $_POST["pwd"];

    //Error handlers
    if (empty($username) || empty($pwd)) {
      err("Empty field");
    }
    if (!preg_match("/^[a-zA-Z0-9_\-]*$/", $username)) {
      err("Invalid characters in your username");
    }

    include_once 'user.php';
    $email = "";
    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);
    $user = new User($username, $hashedpwd, $email);
    // verify if the user already exists in the database
    // check for its email/username
    if (!$user->exists()) {
      err('You should create an account!');
    }
    else
    {
      // TODO LOGGED IN STUFF
      // ALSO CSRF
      $_SESSION["user"] = true;
      $_SESSION["username"] = $username;
      $body = "<h1>LOGGED IN!</h1>";
      include 'cam.php';
    }
  }
} else {
  $_SESSION["token"] = generate_csrf_token();
  $body = '<section class="signup-form">
    <h1>Log in</h1>
    <form action="login.php" method="post">
    <input type="hidden" name="token" value="'.$_SESSION["token"].'">
    <input type="text" name="name" placeholder="Your username/email...">
    <input type="password" name="pwd" placeholder="Your password...">
    <button type="submit" name="submit">LOG IN !</button>
    </form>
    </section>';
  include("template.php");
}
?>

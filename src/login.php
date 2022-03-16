<?php
include_once 'lib.php';

// if you get to the login.php without actually
// filling the required form
if (isset($_POST["submit"]))
{
  $username = $_POST["name"];
  $pwd = $_POST["pwd"];

  //Error handlers
  if (empty($username) || empty($pwd)) {
    err("Empty field");
  }
  if (!preg_match("/^[a-zA-Z0-9_\-]*$/", $username)) {
    err("Invalid characters in your username");
  }

  $email = "";
  $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);
  $user = new User($username, $hashedpwd, $email);
  include_once 'user.php';
  // verify if the user already exists in the database
  // check for its email/username
  if (!$user->exists()) {
    err('You should create an account!');
  }
  else
  {
    // TODO LOGGED IN STUFF
    $body = "<h1>LOGGED IN!</h1>";
    include 'template.php';
  }
}
else {
  $body = '<section class="signup-form">
    <h1>Log in</h1>
    <form action="login.php" method="post">
    <input type="text" name="name" placeholder="Your username/email...">
    <input type="password" name="pwd" placeholder="Your password...">
    <button type="submit" name="submit">LOG IN !</button>
    </form>
    </section>';
  include("template.php");
}
?>

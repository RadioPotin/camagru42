<?php
include_once 'lib.php';
include_once 'dbh.php';
include_once 'include.php';
include_once 'user.php';

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
    $username_or_email = $_POST["name"];
    $pwd = $_POST["pwd"];

    //Error handlers
    validate_login_form($username_or_email, $pwd);

    if (!($row = fetch_user_info($username_or_email))){
      err("No such user, you should signup");
    }

    $username = $row[0]["username"];
    $email = $row[0]["email"];
    $hashedpwd = $row[0]["userpwd"];
    $user = new User($username, $hashedpwd, $email);

    // verify if the user already exists in the database
    // check for its email/username
    if (!$user->exists() || !$user->check_user_pwd($pwd)) {
      err('Incorrect password or username/email! Please retry.');
    } else {
      $_SESSION["user"] = true;
      $_SESSION["username"] = $username;
      $_SESSION["email"] = $email;
      $body = "<h1>LOGGED IN!</h1>";
      include_once 'template.php';
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
    <br/>
    <a href="send_reset_pwd.php">Have you forgotten your password ?</a>
    </form>
    </section>';
  include_once "template.php";
}
?>

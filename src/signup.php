<?php
include_once 'lib.php';
include_once 'include.php';

// if you get to the signup.php without actually
// filling the required form
if (isset($_POST["submit"])) {
  //if you did, but token does NOT match
  if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
    err('Token invalid, sneaky access rejected');
  } else {
    //SUBMITTED AND TOKEN MATCHED
    //
    // process the form
    $email = $_POST["email"];
    $username = $_POST["username"];
    $pwd = $_POST["pwd"];
    $pwdd = $_POST["pwdd"];

    //Error handlers
    validate_nonempty_form($email, $username, $pwd, $pwdd);
    validate_username($username);
    validate_email($email);
    match_pwds($pwd, $pwdd);
    validate_pwd($pwd);

    // if form is well filled, so use user's info to
    // create a new user in the database
    include_once 'user.php';
    // create a new instance of the User object
    $hashedpwd = password_hash($pwd, PASSWORD_DEFAULT);
    $user = new User($username, $hashedpwd, $email);
    // verify if the user already exists in the database
    // check for its email/username
    if ($user->exists()) {
      err('Username or email is already taken');
    } else {
      // actually create user by inserting information, hashing pwd, activation code, etc
      $user->create_pending_user();
      $user->send_activation_email();
      $body = "<h2>Verification email has been sent ! Please check your inbox and click the link in order to activate your account</h2>";
      include_once 'template.php';
    }
  }
} else {
  // if signup.php has been reached without filling a form
  // send the form to fill again
  $_SESSION["token"] = generate_csrf_token();
  $body = '<section class="signup-form">
    <h1>Sign Up</h1>
    <form action="signup.php" method="post">
    <input type="hidden" name="token" value="'.$_SESSION["token"].'">
    <input type="text" name="name" placeholder="Your name...">
    <input type="text" name="email" placeholder="Your email...">
    <input type="text" name="username" placeholder="Your username...">
    <input type="password" name="pwd" placeholder="Your password...">
    <input type="password" name="pwdd" placeholder="Confirm password...">
    <button type="submit" name="submit">Sign up !</button>
    </form>
    </section>';
  include_once "template.php";
}
?>

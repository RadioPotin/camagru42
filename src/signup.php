<?php

// err function that prints the error message
// the body and the template
function err($body)
{
  include("template.php");
  exit ();
}

// if you get to the signup.php without actually
// filling the required form
if (isset($_POST["submit"]))
{
  $email = $_POST["email"];
  $username = $_POST["username"];
  $pwd = $_POST["pwd"];
  $pwdd = $_POST["pwdd"];

  //Error handlers
  if (empty($email) || empty($username)
    || empty($pwdd) || empty($pwd))
  {
    err("Empty field");
  }
  if (!preg_match("/^[a-zA-Z0-9]*$/", $username))
  {
    err("Invalid characters in your username");
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
  {
    err('Invalid email');
  }
  if ($pwd !== $pwdd)
  {
    err('PWD dont match');
  }

  // if form is well filled, use user's info to
  // create a new user in the database
  include_once 'user.php';
  // create a new instance of the User object
  $user = new User($username, $pwd, $email);
  // verify if the user already exists in the database
  // check for its email/username
  if ($user->exists())
  {
    err('Username or email is already taken');
  }
  // actually create user by inserting information, hashing pwd
  $user->create_user();
  $body =
    '<h1>You have signed up ! Now Log in !</h1>
      <section class="signup-form">
        <form action="login.php" method="post">
          <input type="text" name="name" placeholder="Your username/email...">
          <input type="password" name="pwd" placeholder="Your password...">
          <button type="submit" name="submit">LOG IN !</button>
        </form>
      </section>';
  include 'template.php';
}
else {
  // if signup.php has been reached without filling a form
  // send the form to fill again
  $body =
    '<section class="signup-form">
    <h1>Sign Up</h1>
    <form action="signup.php" method="post">
    <input type="text" name="name" placeholder="Your name...">
    <input type="text" name="email" placeholder="Your email...">
    <input type="text" name="username" placeholder="Your username...">
    <input type="password" name="pwd" placeholder="Your password...">
    <input type="password" name="pwdd" placeholder="Confirm password...">
    <button type="submit" name="submit">Sign up !</button>
    </form>
    </section>';
  include("template.php");
}
?>

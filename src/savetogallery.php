<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'lib.php';
include_once 'user.php';

// if you get to the savetogallery.php without actually
// filling the required form
if (isset($_POST["submit"])) {
  //if you did, but token does NOT match
  if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
    err('Token invalid, sneaky access rejected');
  } else {
    $username = $_SESSION["username"];
    $email = $_SESSION["email"];
    $b64_image = str_replace(" ","+",$_POST["entry"]);
    // MAKE DB BLOB STUFSJFJLDKF
    $user = new User($username, "", $email);
    $user->add_pic_to_gallery($b64_image);
  }
} else {
  err('Trying to be sneaky again ?');
}
?>

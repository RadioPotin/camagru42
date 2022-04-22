<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'lib.php';
include_once 'user.php';

// if you get to the savelike.php without actually
// filling the required form
if (isset($_POST["submit"])) {
  //if you did, but token does NOT match
  if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
    err('Token invalid, sneaky access rejected');
  } else {
    $liker = $_POST["liker"];
    $liked_imgid = $_POST["img_id"];

    $userinfo = fetch_user_info($liker);
    $userid = $userinfo[0]["userid"];

    if (is_liking($liked_imgid, $liker) !== null) {
      delete_like($liked_imgid, $userid);
    } else {
      save_like($liked_imgid, $userid);
    }
  }
} else {
  err('Trying to be sneaky again ?');
}
?>

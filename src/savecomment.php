<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'lib.php';
include_once 'user.php';

// if you get to the savecomment.php without actually
// filling the required form
if (isset($_POST["submit"])) {
  //if you did, but token does NOT match
  if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
    // TODO PROPRE ERROR PAGE
    echo "NO TOKEN SAVE COMMENT";
    exit;
  } else {
    $username = $_SESSION["username"];
    $email = $_SESSION["email"];
    $author = $_POST["author"];

    $userinfo = fetch_user_info($author);
    $img_id = $_POST["img_id"];
    $userid = $userinfo[0]["userid"];
    $content = $_POST["comment"];

    save_comment($img_id, $userid, $content);
  }
} else {
  err('<h1>trying to be sneaky again ?</h1>');
}
?>

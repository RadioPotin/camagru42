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
    $author_of_comment = $_POST["author"];

    $userinfo = fetch_user_info($author_of_comment);
    $img_id = $_POST["img_id"];
    $userid = $userinfo[0]["userid"];
    $content = $_POST["comment"];
    $author_of_comment_email = $userinfo[0]["email"];

    save_comment($img_id, $userid, $content);
    $authorinfo = fetch_author_from_img_id($img_id);
    if ($authorinfo[0]["notifications"] === 1
      && $authorinfo[0]["email"] !== $author_of_comment_email)
    {
      $author = new User($authorinfo[0]["username"], "", $authorinfo[0]["email"]);
      $img_nb = $author->return_image_number_usr_specific($img_id);
      $author->send_comment_notification_email($author_of_comment, $author_of_comment_email, $content, $img_nb);
    }
  }
} else {
  err('<h1>trying to be sneaky again ?</h1>');
}
?>

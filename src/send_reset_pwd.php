<?php
if (!isset($_SESSION))
{
    session_start();
}
include_once "lib.php";
include_once "dbh.php";
include_once "user.php";

// if you get to the reset_pwd.php with actuallu
// filling a form
if (isset($_POST["submit"])) {
    //if you did, but token does NOT match
    if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
        // TODO PROPER ERROR PAGE
        echo "EXITING PWDRESET BAD TOKEN";
        exit;
    } else {
        //SUBMITTED AND TOKEN MATCHED
        //
        // process the form
        $email_or_uid = $_POST["name"];
        $row = fetch_user_info($email_or_uid);
        if ( $row === null )
        {
            echo "no such user is verified on our website.";
        }
        $username = $row[0]["username"];
        $email = $row[0]["email"];
        $user = new User($username, "", $email);
    }
} else {
    $_SESSION["token"] = generate_csrf_token();
    $body = '<section class="signup-form">
        <h1>Reset Password</h1>
        <form action="send_reset_pwd.php" method="post">
        <input type="hidden" name="token" value="'.$_SESSION["token"].'">
        <input type="text" name="name" placeholder="Your username/email...">
        <button type="submit" name="submit">Reset password</button>
        <p>We will send a password reset link to your email address</p>
        </form>
        </section>';
    include("template.php");
}

?>

<?php
include_once "dbh.php";
include_once "include.php";
include_once "lib.php";
include_once "user.php";

if (isset($_POST["submit"])) {
    if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
        err('Token invalid, sneaky access rejected');
    } else {
        //SUBMITTED AND TOKEN MATCHED
        // process the form
        $username = $_POST["username"];
        $newemail = $_POST["newemail"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];

        //Error handlers
        validate_nonempty_form($email, $username, $pwd, $newemail);
        check_session_rights($username, $email);
        check_user_existence($username);
        $userinfo = fetch_user_info($username);
        $user = new User($userinfo[0]["username"], $userinfo[0]["userpwd"], $userinfo[0]["email"]);
        check_user_pwd($user, $pwd);
        //check if newusername if not taken
        if (fetch_user_info($newemail) !== null) {
            err("Cannot change email, new one is already taken.");
        }

        //change username
        $user->change_email($newemail);

        //unset session
        $_SESSION["email"] = $newemail;
        include_once("myprofile.php");
    }
} else {
    if (isset($_SESSION["username"]))
    {
        $_SESSION["token"] = generate_csrf_token();
        $body = '<section class="signup-form">
            <h1>You are about to change your email.</h1>
            <form action="changeemail.php" method="post">
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
            <input type="text" name="username" placeholder="Your username">
            <input type="text" name="email" placeholder="Your current email">
            <input type="text" name="newemail" placeholder="Your new email">
            <input type="text" name="pwd" placeholder="Your Password">
            <button type="submit" name="submit">Confirm new email</button>
            </form>
            </section>';
    } else {
        $body = '<h1>You need to be logged into an account to change its affiliated email.</h1>';
    }
    include_once 'template.php';
}
?>

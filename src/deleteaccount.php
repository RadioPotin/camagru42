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
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];
        $pwdd = $_POST["pwdd"];

        //Error handlers
        validate_nonempty_form($email, $username, $pwd, $pwdd);
        match_pwds($pwd, $pwdd);
        check_session_rights($username, $email);
        check_user_existence($username);
        $userinfo = fetch_user_info($username);
        $user = new User($userinfo[0]["username"], $userinfo[0]["userpwd"], $userinfo[0]["email"]);
        check_user_pwd($user, $pwd);
        $id = $userinfo[0]["userid"];

        //delete account
        $gallery = $user->return_all_gallery();

        $user->delete_comments_of_gallery($gallery);
        $user->delete_gallery($id);
        $user->delete_account($id);

        //unset session
        unset($_SESSION["user"]);
        unset($_SESSION["username"]);
        unset($_SESSION["email"]);
        include_once("index.php");
    }
} else {
    if (isset($_SESSION["username"]))
    {
        $_SESSION["token"] = generate_csrf_token();
        $body = '<section class="signup-form">
            <h2 class="warning">You are about to delete your account.</h2>
            <form action="deleteaccount.php" method="post">
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
            <input type="text" name="username" placeholder="Your username">
            <input type="text" name="email" placeholder="Your email">
            <input type="text" name="pwd" placeholder="Your Password">
            <input type="text" name="pwdd" placeholder="Confirm your Password">
            <button type="submit" name="submit">Confirm deletion</button>
            <p class="warning">There will be NO turning back beyond this point.</p>
            <br />
            <p class="warning">Your entire gallery will be deleted along with your account and comments</p>
            </form>
            </section>';
    } else {
        $body = '<h1>You need to be logged into an account to delete it.</h1>';
    }
    include_once 'template.php';
}
?>

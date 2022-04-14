<?php
include_once "dbh.php";
include_once "include.php";
include_once "lib.php";
include_once "user.php";

if (isset($_POST["submit"])) {
    if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
        // TODO PROPER ERROR PAGE
        echo "EXITING CHANGE USERNAME BAD TOKEN";
        exit;

    } else {
        //SUBMITTED AND TOKEN MATCHED
        // process the form
        $username = $_POST["username"];
        $newname = $_POST["newname"];
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];

        //Error handlers
        validate_nonempty_form($email, $username, $pwd, $newname);
        check_session_rights($username, $email);
        check_user_existence($username);
        $userinfo = fetch_user_info($username);
        $user = new User($userinfo[0]["username"], $userinfo[0]["userpwd"], $userinfo[0]["email"]);
        check_user_pwd($user, $pwd);
        //check if newusername if not taken
        if (fetch_user_info($newname) !== null) {
            err("<h2>Cannot change username, it is already taken.</h2>");
        }

        //change username
        $user->change_username($newname);

        //unset session
        $_SESSION["username"] = $newname;
        include_once("myprofile.php");
    }
} else {
    if (isset($_SESSION["username"]))
    {
        $_SESSION["token"] = generate_csrf_token();
        $body = '<section class="signup-form">
            <h1>You are about to change your username.</h1>
            <form action="changeusername.php" method="post">
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
            <input type="text" name="username" placeholder="Your current username">
            <input type="text" name="newname" placeholder="Your new username">
            <input type="text" name="email" placeholder="Your email">
            <input type="text" name="pwd" placeholder="Your Password">
            <button type="submit" name="submit">Confirm new username</button>
            </form>
            </section>';
    } else {
        $body = '<h2>You need to be logged into an account to change its affiliated username.</h2>';
    }
    include_once 'template.php';
}
?>

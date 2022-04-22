<?php
include_once "dbh.php";
include_once "include.php";
include_once "lib.php";
include_once "user.php";

// if you get to the reset.php by
// filling a form
if (isset($_POST["submit"])) {
    //if you did, but token does NOT match
    if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
        err('Token invalid, sneaky access rejected');
    } else {
        $pwd = $_POST["pwd"];
        $pwdd = $_POST["pwdd"];
        // both field must match
        if (match_pwds($pwd, $pwdd)) {
            // pwd and pwdd shouldn't match with previous password though
            // get previous password and compare it to new one
            $username = $_SESSION["username"];
            $row = fetch_user_info($username);
            if (!password_verify($pwd, $row[0]["userpwd"] && password_verify($pwd))) {
                $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);
                $user = new User($row[0]["username"], $hashed_pwd, $row[0]["email"]);
                $user->change_pwd($row[0]["userid"]);

                $body = "<h1>PASSWORD HAS BEEN RESET PAL, GO CHECK IT OUT</h1>";
                include_once 'template.php';
            } else {
                //NEW PASSWORD MATCHES OLD ONE
                $_SESSION["token"] = generate_csrf_token();
                $body = '<section class="signup-form">
                    <h1>Reset Password</h1>
                    <form action="reset.php" method="post">
                    <input type="hidden" name="token" value="'.$_SESSION["token"].'">
                    <input type="text" name="pwd" placeholder="New password">
                    <input type="text" name="pwdd" placeholder="Confirm new password">
                    <button type="submit" name="submit">Reset password</button>
                    <p>New password cannot be the old one!</p>
                    </form>
                    </section>';
                include_once 'template.php';

            }
        } else {
            //PASSWORDS DONT MATCH
            $_SESSION["token"] = generate_csrf_token();
            $body = '<section class="signup-form">
                <h1>Reset Password</h1>
                <form action="reset.php" method="post">
                <input type="hidden" name="token" value="'.$_SESSION["token"].'">
                <input type="text" name="pwd" placeholder="New password">
                <input type="text" name="pwdd" placeholder="Confirm new password">
                <button type="submit" name="submit">Reset password</button>
                <p>Passwords must match!</p>
                </form>
                </section>';
            include_once 'template.php';
        }
    }
} else {
    $_SESSION["token"] = generate_csrf_token();
    $body = '<section class="signup-form">
        <h1>Reset Password</h1>
        <form action="reset.php" method="post">
        <input type="hidden" name="token" value="'.$_SESSION["token"].'">
        <input type="text" name="pwd" placeholder="New password">
        <input type="text" name="pwdd" placeholder="Confirm new password">
        <button type="submit" name="submit">Reset password</button>
        </form>
        </section>';
    include_once 'template.php';
}

?>

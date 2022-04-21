<?php
include_once "dbh.php";
include_once "include.php";
include_once "lib.php";
include_once "user.php";

if (isset($_POST["submit"])) {
    if (!$_POST["token"] || $_POST["token"] !== $_SESSION["token"]) {
        // TODO PROPER ERROR PAGE
        echo "EXITING DELETE PICTURE BAD TOKEN";
        exit;

    } else {
        $imgid = $_POST["imgid"];
        $row = return_specific_img($imgid);

        if (isset($_POST["pwd"]))
        {
            $userinfo = fetch_user_info($_POST["username"]);
            if (password_verify($_POST["pwd"], $userinfo[0]["userpwd"])) {
                delete_specific_pic_comments($imgid);
                delete_specific_pic($imgid);
                $body= "<h1>Picture deleted</h1>";
                include_once 'template.php';
            }
        }
        $body = '<h2>You are about to delete the following pic</h2>
    <img src="'.$row[0]["img"].'">
    <section class="signup-form">
        <p>Please confirm your password</p>
        <form action="deletepic.php" method="post">
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
            <input type="hidden" name="imgid" value="'.$imgid.'">
            <input type="hidden" name="username" value="'.$_SESSION["username"].'">
            <input type="password" name="pwd" placeholder="Your password...">
            <button type="submit" name="submit" style="color:red;">DELETE</button>
        </form>
    </section>';
        include_once 'template.php';
    }
} else {
    $body = "<h2>To delete a picture go to one of your images and press the DELETE button</h2>";
    include_once 'template.php';
}
?>

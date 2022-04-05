<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'lib.php';
include_once 'user.php';

//if you did, but token does NOT match
if (!isset($_SESSION["user"]) && !isset($_SESSION["username"])) {
    // TODO PROPRE ERROR PAGE
    err("<h1>You need to register or log in !</h1>");
    exit;
} else {
    $username = $_SESSION["username"];
    $email = $_SESSION["email"];
    $user = new User($username, "", $email);
    $gallery = $user->return_all_gallery();
    if (empty($gallery))
    {
        err("<h1>nothing here, pal, try taking a pic or two</h1>");
    }
    else {
        // output_gallery is in lib.php
        $body = output_gallery($gallery);
        include_once 'template.php';
        return;
    }
    include_once 'index.php';
}
?>

<?php
include_once 'dbh.php';
include_once 'include.php';


const SENDER_EMAIL_ADDRESS = 'no-reply@email.com';

const APP_URL = 'http://localhost:8888';

const salt = "zDVnWz2X1guMUNW9IhlXX.4UfOOIJtfFB83ZdhQtQE7JsWRSZFif";

// returns a random hex for account activation code generation
function generate_activation_code(): string {
    return bin2hex(random_bytes(16));
}

// err function that prints the error message
// the body and the template
function err($body) {
    include_once("template.php");
    exit ();
}

function generate_csrf_token () {
    return bin2hex(random_bytes(32));
}

//Error handlers
function validate_nonempty_form($email, $username, $pwd, $pwdd) {
    if (empty($email) || empty($username)
        || empty($pwdd) || empty($pwd)) {
        err("Empty field");
    }
    return TRUE;
}

function validate_login_form($email_or_username, $pwd) {
    if (empty($email_or_username) || empty($pwd)) {
        err("Empty field");
    }
    return TRUE;
}

function validate_username($username) {
    if (!preg_match("/^[a-zA-Z0-9_\-]*$/", $username)) {
        err("Invalid characters in your username");
    }
    return TRUE;
}

function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        err('Invalid email');
    }
    return TRUE;
}

function match_pwds($pwd, $pwdd) {
    if ($pwd !== $pwdd) {
        err('PWD dont match');
    }
    return TRUE;
}

function validate_pwd($pwd) {
    if (preg_match('@[A-Z]@', $pwd) || preg_match('@[a-z]@', $pwd)
        || preg_match('@[0-9]@', $pwd) || preg_match('@[^\w]@', $pwd))
    {
        err('Password should be at least 8 characters long and have the followin characters: one uppercase, one lowercase, one number and one special.');
    }
    return TRUE;
}

function check_session_rights($username, $email) {
    if ($username !== $_SESSION["username"] || $email !== $_SESSION["email"]) {
        err("<h1>You do not have the rights</h1>");
    }
    return TRUE;
}

function check_user_existence($username_or_email_or_uid) {
    if (fetch_user_info($username_or_email_or_uid) === null) {
        err("<h1>No such user</h1>");
    }
    return TRUE;
}

function check_user_pwd($user, $pwd) {
    if (!$user->check_user_pwd($pwd)) {
        err("<h1>Incorrect password</h1>");
    }
    return TRUE;
}

function display_comment_box($img_id) {
    if (isset($_SESSION["user"]) && isset($_SESSION["username"])) {
        return '
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
            <input type="hidden" class="img_id" value="'.$img_id.'">
            <textarea class="comment_text" placeholder="Comment goes here"></textarea>
            <br />
            <button class="submit_comment" value="'.$_SESSION["username"].'">
                Submit comment
            </button>';
    } else {
        return '
            <div class="no_comment">
                <h4>
                    <a href="/login.php">Log in</a> or <a href="/signup.php"> Sign up</a> to post a comment
                </h4>
            </div>';
    }
    return "";
}

function display_comment_section($img_id) {
    $comment_section = return_comment_section($img_id);

    if (!empty($comment_section))
    {
        $body = '<button class="display_comments">Display Comments</button>';
        $body .= '
            <br />
            <div class="comment_section" style="display:none;">
                <ul>
                    ';
        foreach ($comment_section as $comment)
        {
            $body .= '<li class="comment">
                <p class="author">AUTHOR: '.$comment["author"].'</p>
                <p class="comment"> COMMENT: '.$comment["content"].'</p>
                <br />
                </li>';
        }
        $body .= '
                </ul>
            </div>';
        return $body;
    } else {
        $body = '<div class="no_comment">
            <p>There is no comment for this masterpiece yet !</p>
            </div>';
        return $body;
    }
}

function delete_pic_link($imgid) {
    $body = '<form action="deletepic.php" method="post">
        <input type="hidden" name="token" value="'.$_SESSION["token"].'">
        <input type="hidden" name="imgid" value="'.$imgid.'">
        <input type="hidden" name="username" value="'.$_SESSION["username"].'">
        <button type="submit" name="submit" style="color:red;">DELETE</button>
        </form>';
    return $body;
}

// need to add to this function a call to a function that returns a link to a specific user gallery when given $img["username"]
// a comment section for each pic
// a LIKE button for each pic
function output_gallery($gallery)
{
    $_SESSION["token"] = generate_csrf_token();
    $body = '<ul class="my_gallery">';
    foreach ($gallery as $img)
    {
        $body .='
        <li class="art_piece">
            <img id="'.$img["rowid"].'" src="'.$img["img"].'">
            <p class="creation_date">'.$img["creation_date"].'</p>
            <p class="pic_id">Pic id: '.$img["rowid"].'</p>
            <p class="artist">Creator: '.$img["username"].'</p>
            '. ($img["username"] === $_SESSION["username"] ? delete_pic_link($img["rowid"]) : "" ) .'
            '. display_comment_section($img["rowid"]) .
            display_comment_box($img["rowid"]) .'
        </li>';
    }
    $body .= "
    </ul>";
    return $body;
}
?>

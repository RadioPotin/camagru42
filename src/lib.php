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
function validate_signup_form($email, $username, $pwd, $pwdd)
{
    if (empty($email) || empty($username)
        || empty($pwdd) || empty($pwd)) {
        err("Empty field");
    }
    return TRUE;
}

function validate_username($username)
{
    if (!preg_match("/^[a-zA-Z0-9_\-]*$/", $username)) {
        err("Invalid characters in your username");
    }
    return TRUE;
}

function validate_email($email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        err('Invalid email');
    }
    return TRUE;
}

function match_pwds($pwd, $pwdd)
{
    if ($pwd !== $pwdd) {
        err('PWD dont match');
    }
    return TRUE;
}

function validate_pwd($pwd)
{
    if (preg_match('@[A-Z]@', $pwd) || preg_match('@[a-z]@', $pwd)
        || preg_match('@[0-9]@', $pwd) || preg_match('@[^\w]@', $pwd))
    {
        err('Password should be at least 8 characters long and have the followin characters: one uppercase, one lowercase, one number and one special.');
    }
    return TRUE;
}

?>

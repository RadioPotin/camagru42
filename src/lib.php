<?php
include_once 'dbh.php';
include_once 'include.php';


const SENDER_EMAIL_ADDRESS = 'no-reply@email.com';

const APP_URL = 'http://localhost:8888';

// returns a random hex for account activation code generation
function generate_activation_code(): string {
    return bin2hex(random_bytes(16));
}

const FILTERS = [
    'string' => FILTER_SANITIZE_STRING,
    'email' => FILTER_SANITIZE_EMAIL,
    'url' => FILTER_SANITIZE_URL
];

// err function that prints the error message
// the body and the template
function err($body) {
    include_once("template.php");
    exit ();
}

function generate_csrf_token () {
    return bin2hex(random_bytes(32));
}

?>

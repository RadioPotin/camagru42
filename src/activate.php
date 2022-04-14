<?php
include_once 'user.php';
include_once 'lib.php';
include_once 'include.php';

function find_unverified_user(string $activation_code) {
    $sql = "SELECT username, email, userpwd, activation_code,
        activation_expiry < datetime('now', 'localtime') as expired
        FROM pending_users WHERE activation_code=:activation_code";
    $pdo = connect_todb();
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':activation_code', $activation_code);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {

        $username = $row[0]['username'];
        $email = $row[0]['email'];
        $pwd = $row[0]['userpwd'];
        $user = new User($username, $pwd, $email);

        // already expired, delete the user from PENDING table
        if ((int)$row[0]['expired'] === 1) {
            $user->delete_user_by_usern();
            return null;
        }
        return $user;
    }
    // No such code in database
    return null;
}

$activation_code = $_GET['activation_code'];

if ($activation_code) {
    if (($user = find_unverified_user($activation_code)) !== null) {
        $user->activate_user();
        include_once "login.php";
    } else {
        $body = '<h2>Either activation code has expired or is not recognized. Please register again.</h2>';
        include_once 'template.php' ;
    }
} else {
    $body = '<h1>Are you trying to be sneaky?</h1>';
    include_once 'template.php';
}

?>

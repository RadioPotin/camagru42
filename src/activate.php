<?php

include_once 'user.php';

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
        $body = '<section class="signup-form">
            <h1>Thank you for registering! You may log in now</h1>
            <form action="login.php" method="post">
            <input type="text" name="name" placeholder="Your username/email...">
            <input type="password" name="pwd" placeholder="Your password...">
            <button type="submit" name="submit">LOG IN !</button>
            </form>
            </section>';
        include("template.php");
    } else {
        $body = '<h1>Either activation code has expired or is not recognized. Please register again.</h1>';
        include('template.php');
    }
} else {
    $body = '<h1>Are you trying to be sneaky?</h1>';
    include('template.php');
}

?>

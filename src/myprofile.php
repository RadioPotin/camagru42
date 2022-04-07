<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'user.php';

$username = $_SESSION["username"];
$userinfo = fetch_user_info($username);
$user = new User($userinfo[0]["username"], $userinfo[0]["userpwd"], $userinfo[0]["email"]);

if (isset($_GET["notifications"])) {
    $notifs = $_GET["notifications"];
    if ($notifs) {
        $user->activate_notifications();
    } else {
        $user->deactivate_notifications();
    }
}

$body = '<h1>Welcome, ' . $username . '!</h1>
    <br />
    <div id="usersettings">
        <h2>User Information</h2>
        <div class="usersettingsblock">
            <p>Your current username: '. $userinfo[0]["username"] . ' <a href="/changeusername.php">CHANGE MY USERNAME</a></p>
            <p>Your current email: '. $userinfo[0]["email"] . ' <a href="/changeemail.php">CHANGE MY EMAIL</a></p>
            <p>Your personal Gallery: <a href="/mygallery.php">HERE</a></p>
        </div>
    </div>
    <hr />
    <br />
    <div id="usersettings">
        <h2>User Preferences</h2>
        <div class="usersettingsblock">';
            $notifications = $user->has_notifications();
            if ($notifications) {
                $option = "<p>NOTIFICATIONS : ON ";
                $option .= '<a href="/myprofile.php?notifications=0">TURN OFF</a></p>';
            } else {
                $option = "<p>NOTIFICATIONS : OFF ";
                $option .= '<a href="/myprofile.php?notifications=1">TURN ON</a></p>';
            }
            $body .= $option;
        $body .= '</div>
    </div>
    <hr />
    <br />
    <div id="dangerzone">
        <h2>Danger zone &#x1F431</h2>
        <a href="/deletegallery.php" style="color:red">Delete all your pictures</a>
        <br />
        <a href="/deleteaccount.php" style="color:red">Delete my account</a>
    </div>
    ';

include_once "template.php";
?>

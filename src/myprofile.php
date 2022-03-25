<?php
include_once 'include.php';
include_once 'dbh.php';
include_once 'user.php';

$username = $_SESSION["username"];
$userinfo = fetch_user_info($username);
$user = new User($userinfo[0]["username"], $userinfo[0]["userpwd"], $userinfo[0]["email"]);

$body = '<h1>Welcome, ' . $username . '!</h1>
    <br />
    <div id="usersettings">
        <h2>User Information</h2>
        <div class="usersettingsblock">
            <p>Your current username: '. $userinfo[0]["username"] . '</p>
            <p>Your current email: '. $userinfo[0]["email"] . '</p>
            <p>Your personal Gallery: <a href="#">HERE</a></p>
        </div>
    </div>
    <hr />
    <br />
    <div id="usersettings">
        <h2>User Preferences</h2>
        <div class="usersettingsblock">
            <p>Activate/Deactivate email notifications on comment ? YES/NO</p>
        </div>
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

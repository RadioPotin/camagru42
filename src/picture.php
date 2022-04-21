<?php
include_once 'include.php';
include_once 'lib.php';

if (!isset($_SESSION["user"]) && !isset($_SESSION["username"])) {
    err('<h1>Try registering or logging in first</h1>');
    exit;
} else {
    $_SESSION["token"] = generate_csrf_token();
    $body =
        '<div class="contentarea">
        <h1>Try it out</h1>
        <div class="camera">
            <video id="video" >Video stream not available.</video>
            <canvas id="feed"></canvas>
            <br />
            <button id="takepic">Take photo</button>
            <button id="resetfeed">Reset video feed</button>
            <br />
            <input type="file" id="upload" accept="image/png, image/jpg">
        </div>
        <ul>
            <li><img src="assets/img/solaire.png" id="layer0" width="50" ></li>
            <li><img src="assets/img/zulipsolaire.png" id="layer1" width="50" /></li>
            <li><img src="assets/img/OCPxUA.png" id="layer2" width="50" /></li>
            <li><img src="assets/img/HMMMMM.png" id="layer3" width="50" /></li>
        </ul>
        <canvas id="canvas_saved" ></canvas>
        <button id="savepic">Save your picture to your gallery</button>
        <input type="hidden" name="token" value="'.$_SESSION["token"].'">
    </div>';
    $script = '<script type="text/javascript" src="assets/js/script.js" defer></script>';
    include_once("template.php");
}
// TODO
// 1. ADD RIGHT CLICK TO DELETE LAYER
// 3. LIKES
// 4. ULR ENCODE FOR httpRequests
?>

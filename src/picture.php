<?php
include_once 'include.php';
include_once 'lib.php';

$_SESSION["token"] = generate_csrf_token();
$body =
    '<div class="contentarea">
        <h1>Try it out</h1>
        <div class="camera">
            <video id="video">Video stream not available.</video>
            <button id="takepic">Take photo</button>
            <input type="file" id="upload" accept="image/png, image/jpg">
        </div>
        <canvas id="canvas" >
        </canvas>
        <div class="output" style="display:none">
            <img id="photo">
        </div>
        <button id="savepic" class="hide">Save your picture to your gallery</button>
        <input type="hidden" name="token" value="'.$_SESSION["token"].'">
    </div>';
$script = '<script type="text/javascript" src="assets/js/script.js" defer></script>';
include ("template.php");
?>

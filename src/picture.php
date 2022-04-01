<?php
include_once 'include.php';
include_once 'lib.php';

$_SESSION["token"] = generate_csrf_token();
$body =
    '<div class="contentarea">
        <h1>Try it out</h1>
        <div class="camera">
            <video id="video">Video stream not available.</video>
            <button id="startbutton">Take photo</button>
            <input type="file" id="upload" accept="image/png, image/jpg">
        </div>
        <canvas id="canvas">
            <div class="output">
                <img id="photo" alt="The screen capture will appear in this box.">
            </div>
        </canvas>
        <button id="savepic" class="hide">Save your picture to your gallery</button>
        <form id="hiddenform">
            <input type="hidden" name="token" value="'.$_SESSION["token"].'">
        </form>
    </div>';
$script = '<script defer type="text/javascript" src="assets/js/script.js"></script>';
include ("template.php");
?>

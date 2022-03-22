<?php
include_once 'include.php';
$body =
    '<div class="contentarea">
        <h1>Try it out</h1>
        <p>Ablabla</p>
        <div class="camera">
            <video id="video">Video stream not available.</video>
            <button id="startbutton">Take photo</button>
        </div>
        <canvas id="canvas">
            <div class="output">
                <img id="photo" alt="The screen capture will appear in this box.">
            </div>
        </canvas>
        <p>ABLOBLO</p>
    </div>';
$script = '<script defer type="text/javascript" src="assets/js/script.js"></script>';
include ("template.php");
?>

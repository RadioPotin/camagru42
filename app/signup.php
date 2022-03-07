<?php
  $body = "<section class=\"signup-form\">
    <h1>Sign Up</h1>
    <form action=\"includes/signup.inc.php\" method=\"post\">
      <input type=\"text\" name=\"name\" placeholder=\"Your name...\">
      <input type=\"text\" name=\"email\" placeholder=\"Your email...\">
      <input type=\"text\" name=\"uid\" placeholder=\"Your username...\">
      <input type=\"password\" name=\"pwd\" placeholder=\"Your password...\">
      <input type=\"password\" name=\"pwdd\" placeholder=\"Confirm password...\">
      <button type=\"submit\" name=\"submit\">Sign up !</button>
    </form>
  </section>";
include("template.php");
?>

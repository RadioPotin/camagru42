<?php
$body =
'<section class="signup-form">
  <h1>Log in</h1>
  <form action="login.php" method="post">
    <input type="text" name="name" placeholder="Your username/email...">
    <input type="password" name="pwd" placeholder="Your password...">
    <button type="submit" name="submit">LOG IN !</button>
  </form>
 </section>';
include("template.php");
?>
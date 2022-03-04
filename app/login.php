<?php
include_once 'header.php';
?>
  <section class="signup-form">
    <h2>Log in</h2>
    <form action="login.inc.php" method="post">
      <input type="text" name="name" placeholder="Your name/email...">
      <input type="password" name="pwd" placeholder="Your password...">
      <button type="submit" name="submit">LOG IN !</button>
    </form>
  </section>
<?php
include_once 'footer.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
  </head>
  <body>
    <nav>
      <ul>
        <li><a href="/"><img class="logo" src="assets/img/camagru_logo.png"></a></li>
        <li><a href="/">Home</a></li>
        <?php
          include_once "include.php";
          if (!isset($_SESSION["user"]) || empty($_SESSION["user"])) {
              echo '<li><a href="login.php">Log in</a></li>
                <li><a href="signup.php">Sign up</a></li>';
          }
          else {
            echo '<li><a href="picture.php">Cumagru</a></li>
        <li><a href="signout.php">Sign out</a></li>
        <li><a href="myprofile.php">My Profile</a></li>';
          }
        ?>

      </ul>
    </nav>
    <?php
      echo $body;
    ?>
  <?php
    if (!empty($script)){
      echo $script;
    }
  ?>

  </body>
</html>

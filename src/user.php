<?php
include_once "dbh.php";
include_once "auth.php";

class User {

   private $username;
   private $pwd;
   private $email;
   private $code;

   public function __construct($username, $pwd, $email) {
      $this->username = $username;
      $this->pwd = $pwd;
      $this->email = $email;
      $this->code = generate_activation_code();
   }

   // check if given USERNAME or EMAIL are already in login table
   public function exists()
   {
      $pdo = connect_todb();
      $sql =
         "SELECT * FROM login
         WHERE
         username = :usern
         OR
         email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':usern', $this->username);
      $stmt->bindParam(':email', $this->email);
      $stmt->execute();
      $result = $stmt->fetchAll();
      if (count($result) > 0)
      {
         return true;
      }
      else{
         return false;
      }
   }

   // create user
   // insert USERNAME, EMAIL, HASHED PWD
   // activation_code as well
   public function create_user()
   {
      $pdo = connect_todb();

      $hashedpwd = password_hash($this->pwd, PASSWORD_DEFAULT);
      $activation_code = password_hash($this->code, PASSWORD_DEFAULT);
      $expiry = 1 * 24 * 60 * 60;
      $stmt =
         $pdo->prepare("INSERT INTO login(
            username,
            email,
            userpwd,
            activation_code,
            activation_expiry)
            VALUES(
               :username,
               :email,
               :userpwd,
               :activation_code,
               :activation_expiry)");
      $stmt->execute([$this->username, $this->email, $hashedpwd, $activation_code, date('Y-m-d H:i:s', time() + $expiry)]);
   }
}

?>

<?php
include_once "dbh.php";

class User {

   private $username;
   private $pwd;
   private $email;

   public function __construct($username, $pwd, $email) {
      $this->username = $username;
      $this->pwd = $pwd;
      $this->email = $email;
   }

   // check if given USERNAME or EMAIL are already in login table
   public function exists()
   {
      $pdo = connect_todb();
      $sql = "SELECT * FROM login WHERE username = :usern OR email = :email";
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
   public function create_user()
   {
      $pdo = connect_todb();
      $hashedpwd = password_hash($this->pwd, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO login(username, email, userpwd) VALUES(:username, :email, :userpwd)");

      $stmt->execute([$this->username, $this->email, $hashedpwd]);
   }
}

?>

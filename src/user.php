<?php

include_once "dbh.php";
include_once "lib.php";

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
   public function exists_in_pending(): bool {
      $pdo = connect_todb();
      $sql = "SELECT * FROM pending_users
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

   public function exists_in_verified(): bool {
      $pdo = connect_todb();
      $sql =
         "SELECT * FROM verified_users
         WHERE
         username = :usern
         OR
         email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':usern', $this->username);
      $stmt->bindParam(':email', $this->email);
      $stmt->execute();
      $result = $stmt->fetchAll();
      if (count($result) > 0) {
         return true;
      } else {
         return false;
      }
   }

   public function exists() : bool {
      if ($this->exists_in_verified() || $this->exists_in_pending()) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   // create pending user
   // insert USERNAME, EMAIL, HASHED PWD
   // activation_code as well
   public function create_pending_user() {
      $pdo = connect_todb();

      $expiry = 1 * 24 * 60 * 60;
      $stmt =
         $pdo->prepare("INSERT INTO pending_users(
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
      $stmt->execute(
         [$this->username,
         $this->email,
         $this->pwd,
         $this->code,
         date('Y-m-d H:i:s', time() + $expiry)]
      );
   }

   // takes email of user and
   // prior generated activation code for
   // his account registration confirmation
   function send_activation_email(): void {
      // use percent encoding for email and activation code
      $encoded_activation_code = urlencode($this->code);
      // create the activation link
      $activation_link = APP_URL . "/activate.php?activation_code=$encoded_activation_code";
      // set email subject & body
      $subject = 'Please activate your account';
      $message = <<<MESSAGE
            Hi,
            Please click the following link to activate your account:
            $activation_link
MESSAGE;
      // email header
      $header = "From:" . SENDER_EMAIL_ADDRESS;
      // send the email
      $success = mail($this->email, $subject, nl2br($message), $header);
      if (!$success) {
         $errorMessage = error_get_last()['message'];
         die("ERROR, user.php: " . $errorMessage);
      }
   }

   // activate pending user
   function activate_user() : void {
      //first, add user to verified users
      $sql = 'INSERT INTO verified_users (username, email, userpwd)
         VALUES (:usern, :email, :pwd)';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->bindParam(':email', $this->email);
      $statement->bindParam(':pwd', $this->pwd);
      $statement->execute();
      //second, remove user from pending users
      $sql = 'DELETE FROM pending_users WHERE username=:usern';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      return ;
   }

   // deletes a user from pending_users table
   function delete_user_by_usern() {
      $sql = 'DELETE FROM pending_users WHERE username =:usern';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      return ;
   }

   function check_user_pwd(string $hashedpwd)
   {
      if ($hashedpwd === $this->pwd) {
         return TRUE;
      }
      return FALSE;
   }
}

?>

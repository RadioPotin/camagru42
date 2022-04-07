<?php

include_once "dbh.php";
include_once "lib.php";
include_once "include.php";

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
      $this->reset_hash = bin2hex(random_bytes(32));
   }

   // check if given USERNAME or EMAIL are already in login table
   public function exists_in_pending(): bool {
      $pdo = connect_todb();
      $sql =
         "SELECT * FROM pending_users
         WHERE
         username = :usern
         OR
         email = :email";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':usern', $this->username);
      $stmt->bindParam(':email', $this->email);
      $stmt->execute();
      $result = $stmt->fetchAll();
      return(count($result) > 0);
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
      return(count($result) > 0);
   }

   public function exists() : bool {
      return ($this->exists_in_verified()
         || $this->exists_in_pending());
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

   // deletes a user from pending_users table
   function delete_user_by_usern(): void {
      $sql = 'DELETE FROM pending_users WHERE username =:usern';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      return ;
   }

   //TODO, USE PASSWORD VEriFY
   function check_user_pwd(string $pwd): bool {
      return password_verify($pwd, $this->pwd);
   }

   // activate pending user
   function activate_user() : void {
      $default_notif_mode = TRUE;
      //first, add user to verified users
      $sql = 'INSERT INTO verified_users (username, email, notifications, userpwd)
         VALUES (:usern, :email, :notifs, :pwd)';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->bindParam(':email', $this->email);
      $statement->bindParam(':notifs', $default_notif_mode);
      $statement->bindParam(':pwd', $this->pwd);
      $statement->execute();
      //get userid from later Foreign Key for RESET_PWD_HASHES table
      $userid = $pdo->lastInsertId();
      //second, remove user from pending users
      $sql = 'DELETE FROM pending_users WHERE username=:usern';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      //third, add user's password reset hash to corresponding table
      $sql = 'INSERT INTO reset_pwd_hashes (reset_hash, userid) VALUES (:hash, :uid)';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':hash', $this->reset_hash);
      $statement->bindParam(':uid', $userid);
      $statement->execute();
      return ;
   }

   function change_pwd(string $id): void {
      // First update, pwd column in corresponding verified_users table entry
      $sql = 'UPDATE verified_users
         SET userpwd = :newpwd
         WHERE username=:usern';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':newpwd', $this->pwd);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      // Second update, reset_hash in corresponding reset_pwd_hashes table entry
      $sql = "UPDATE reset_pwd_hashes
         SET reset_hash=:hash
         WHERE userid=:userid";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':hash', $this->reset_hash);
      $statement->bindParam(':userid', $id);
      $statement->execute();
      return ;
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
      $success = mail($this->email, $subject, $message, $header);
      if (!$success) {
         $errorMessage = error_get_last()['message'];
         die("ERROR, user.php: " . $errorMessage);
      }
   }


   // takes email of user
   // sends a link to reset password
   function send_pwd_reset_email(): void {
      $pdo = connect_todb();
      // get userid based on email/username to get reset_hash
      $sql = 'SELECT reset_pwd_hashes.userid, reset_hash
         FROM verified_users, reset_pwd_hashes
         WHERE (email=:email OR username=:usern)
         AND (reset_pwd_hashes.userid = verified_users.userid)';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':email', $this->email);
      $statement->bindParam(':usern', $this->username);
      $statement->execute();
      $set = $statement->fetchAll();
      $id = $set[0]["userid"];
      $hash = $set[0]["reset_hash"];

      // use percent encoding for email and activation code
      $pwd_reset_key = urlencode($hash);
      // create the activation link
      $pwd_link = APP_URL . "/authorize_pwd_reset.php?id=$id&reset_pwd=$pwd_reset_key";
      // set email subject & body
      $subject = 'Forgotten your password ?';
      $message = <<<MESSAGE
Hi,
Please click the following link to reset the password of your account:
$pwd_link
MESSAGE;
      // email header
      $header = "From:" . SENDER_EMAIL_ADDRESS;
      // send the email
      $success = mail($this->email, $subject, $message, $header);
      if (!$success) {
         $errorMessage = error_get_last()['message'];
         die("ERROR, user.php: " . $errorMessage);
      }
   }

   function delete_gallery($id) {
      $pdo = connect_todb();
      $sql = 'DELETE FROM user_galleries
         WHERE userid=:id';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $id);
      $statement->execute();
      return ;
   }

   function return_all_gallery()
   {
      $pdo = connect_todb();
      $sql = 'SELECT user_galleries.rowid, img, creation_date, verified_users.username as username
         FROM user_galleries, verified_users
         WHERE user_galleries.userid=:id
         AND verified_users.userid=:id';
      $statement = $pdo->prepare($sql);

      $userinfo = fetch_user_info($this->username);
      $id = $userinfo[0]["userid"];

      $statement->bindParam(':id', $id);
      $statement->execute();
      return $statement->fetchAll();
   }

   function fetch_pagination_elements_from_given_user($offset, $limit)
   {
      $pdo = connect_todb();
      $sql = "SELECT user_galleries.rowid, img, creation_date, verified_users.username as username
         FROM user_galleries, verified_users
         WHERE user_galleries.userid=:id
         AND verified_users.userid=:id
         ORDER BY creation_date DESC
         LIMIT :offset, :limit";
      $statement = $pdo->prepare($sql);

      $userinfo = fetch_user_info($this->username);
      $id = $userinfo[0]["userid"];

      $statement->bindParam(':id', $id);
      $statement->bindParam(":offset", $offset);
      $statement->bindParam(":limit", $limit);
      $statement->execute();
      $row = $statement->fetchAll();
      if (!empty($row)) {
         return $row;
      } else {
         return null;
      }
   }

   function count_img_entries_of_user() {
      $pdo = connect_todb();
      $sql = "SELECT COUNT(*)
         FROM user_galleries, verified_users
         WHERE verified_users.username=:username
         AND user_galleries.userid=verified_users.userid";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(":username", $this->username);
      $statement->execute();
      $nb = $statement->fetchColumn();
      return $nb;
   }

   function add_pic_to_gallery(string $pic_b64) {
      $pdo = connect_todb();
      $sql = 'INSERT INTO user_galleries(img, creation_date, userid)
         VALUES (:img, :date, :id)';
      $statement = $pdo->prepare($sql);

      $userinfo = fetch_user_info($this->username);
      $id = $userinfo[0]["userid"];

      $date = date('Y-m-d H:i:s', time());
      $statement->bindParam(':id', $id);
      $statement->bindParam(':date', $date);
      $statement->bindParam(':img', $pic_b64);
      $statement->execute();
      return ;
   }

   function delete_account($id): void {
      $pdo = connect_todb();
      $sql = 'DELETE FROM reset_pwd_hashes
         WHERE userid=:id';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $id);
      $statement->execute();
      $sql = 'DELETE FROM verified_users
         WHERE userid=:id';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $id);
      $statement->execute();
      return ;
   }

   function change_username($newname) {
      // update username to new one
      $sql = 'UPDATE verified_users
         SET username = :newname
         WHERE username=:username';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':newname', $newname);
      $statement->bindParam(':username', $this->username);
      $statement->execute();
      return ;
   }

   function change_email($newemail) {
      // update email to new one
      $sql = 'UPDATE verified_users
         SET email = :newemail
         WHERE username=:username';
      $pdo = connect_todb();
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':newemail', $newemail);
      $statement->bindParam(':username', $this->username);
      $statement->execute();
      return ;
   }

   function has_notifications() {
      $pdo = connect_todb();
      $sql = "SELECT notifications
         FROM verified_users
         WHERE username=:usern";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(":usern", $this->username);
      $statement->execute();
      $bool = $statement->fetchColumn();
      return $bool;
   }

   function activate_notifications() {
      $pdo = connect_todb();
      $sql = "UPDATE verified_users
         SET notifications=TRUE
         WHERE username=:usern";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(":usern", $this->username);
      $statement->execute();
      $statement->fetchColumn();
      return ;
   }

   function deactivate_notifications() {
      $pdo = connect_todb();
      $sql = "UPDATE verified_users
         SET notifications=FALSE
         WHERE username=:usern";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(":usern", $this->username);
      $statement->execute();
      $statement->fetchColumn();
      return;
   }
}

?>

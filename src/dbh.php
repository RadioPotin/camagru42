<?php
include_once 'include.php';

function tables_exist($pdo) : bool {
    // Try a select statement against the table
    // Run it in try-catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $pdo->query("SELECT 1 FROM verified_users, pending_users LIMIT 1");
        //$pdo->query("SELECT 1 FROM overlayimages LIMIT 1");
    } catch (Exception $e) {
        // We got an exception (table not found)
        return FALSE;
    }
    return TRUE ;
}

function create_tables($pdo) : void {
    $commands =
        ["PRAGMA foreign_keys = ON",

        "CREATE TABLE IF NOT EXISTS verified_users (
            userid INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            userpwd TEXT NOT NULL)",

        "CREATE TABLE IF NOT EXISTS pending_users(
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            userpwd TEXT NOT NULL,
            activation_code varchar(255) NOT NULL,
            activation_expiry datetime NOT NULL)",

        "CREATE TABLE IF NOT EXISTS reset_pwd_hashes(
            reset_hash TEXT NOT NULL,
            userid INTEGER NOT NULL,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)",

        "CREATE TABLE IF NOT EXISTS user_galleries(
            img BLOB NOT NULL,
            creation_date datetime NOT NULL,
            userid INTEGER NOT NULL,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)"];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }
}

function connect_todb() : object {
    $pdo = new \PDO("sqlite:" . "cumagru.db");
    if ($pdo === null)
    {
        $body = 'CANT CONNECT TO DB';
        include 'template.php';
        exit();
    }
    if (tables_exist($pdo))
    {
        return $pdo;
    }
    else
    {
        create_tables($pdo);
        return ($pdo);
    }
    return ($pdo);
}

function fetch_user_info($email_or_uid) {
    $pdo = connect_todb();
    $sql = "SELECT username,email,userpwd,userid
        FROM verified_users
        WHERE username=:uid
        OR email=:email
        OR userid=:userid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":uid", $email_or_uid);
    $statement->bindParam(":email", $email_or_uid);
    $statement->bindParam(":userid", $email_or_uid);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }
}


?>

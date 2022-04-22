<?php
include_once 'include.php';

function tables_exist($pdo) : bool {
    // Try a select statement against the table
    // Run it in try-catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $pdo->query("SELECT 1 FROM verified_users, pending_users, reset_pwd_hashes, user_galleries, comments, likes LIMIT 1");
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

        //VERIFIED USERS
        "CREATE TABLE IF NOT EXISTS verified_users (
            userid INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            notifications INTEGER NOT NULL,
            userpwd TEXT NOT NULL)",

        //PENDING USERS
        "CREATE TABLE IF NOT EXISTS pending_users(
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            userpwd TEXT NOT NULL,
            activation_code varchar(255) NOT NULL,
            activation_expiry datetime NOT NULL)",

        //RESET PWD
        "CREATE TABLE IF NOT EXISTS reset_pwd_hashes(
            reset_hash TEXT NOT NULL,
            userid INTEGER NOT NULL,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)",

        //GALLERIES
        "CREATE TABLE IF NOT EXISTS user_galleries(
            img BLOB NOT NULL,
            creation_date datetime NOT NULL,
            userid INTEGER NOT NULL,
            users_img_id INTEGER NOT NULL,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)",

        //COMMENTS
        "CREATE TABLE IF NOT EXISTS comments(
            img_id INTEGER NOT NULL,
            userid INTEGER NOT NULL,
            content TEXT NOT NULL,

            CONSTRAINT img_id FOREIGN KEY (img_id)
            REFERENCES user_galleries(rowid)
            ON DELETE CASCADE,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)",

        //LIKES
        "CREATE TABLE IF NOT EXISTS likes(
            img_id INTEGER NOT NULL,
            userid INTEGER NOT NULL,

            UNIQUE (img_id, userid),

            CONSTRAINT img_id FOREIGN KEY (img_id)
            REFERENCES user_galleries(rowid)
            ON DELETE CASCADE,

            CONSTRAINT userid FOREIGN KEY (userid)
            REFERENCES verified_users(userid)
            ON DELETE CASCADE)"
        ];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }
}

function connect_todb() : object {
    $pdo = new \PDO("sqlite:" . "cumagru.db");
    if ($pdo === null) {
        $body = 'CANT CONNECT TO DB';
        include 'template.php';
        exit();
    }
    if (tables_exist($pdo)) {
        return $pdo;
    } else {
        create_tables($pdo);
        return ($pdo);
    }
    return ($pdo);
}

function count_img_entries() {
    $pdo = connect_todb();
    $sql = "SELECT COUNT(*)
        FROM user_galleries ";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $nb = $statement->fetchColumn();
    return $nb;
}

function fetch_user_info($email_or_uid) {
    $pdo = connect_todb();
    $sql = "SELECT username,email,userpwd,userid,notifications
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

function fetch_author_from_img_id($img_id) {
    $pdo = connect_todb();
    $sql = "SELECT
        verified_users.username,
        verified_users.email,
        verified_users.userid,
        verified_users.notifications
        FROM
        verified_users, user_galleries
        WHERE
        user_galleries.rowid=:img_id
        AND
        user_galleries.userid=verified_users.userid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }
}

function return_specific_img($imgid) {
    $pdo = connect_todb();
    $sql = "SELECT user_galleries.rowid, img, creation_date,
        verified_users.username as username
        FROM user_galleries, verified_users
        WHERE user_galleries.rowid=:imgid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":imgid", $imgid);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }
}

function fetch_all_galleries() {
    $pdo = connect_todb();
    $sql = "SELECT user_galleries.rowid, img, creation_date, verified_users.username as username
        FROM user_galleries, verified_users
        WHERE user_galleries.userid=verified_users.userid
        ORDER BY creation_date DESC";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }
}

function fetch_pagination_elements_from_all_galleries($offset, $limit) {
    $pdo = connect_todb();
    $sql = "SELECT user_galleries.rowid, img, creation_date, verified_users.username as username
        FROM user_galleries, verified_users
        WHERE user_galleries.userid=verified_users.userid
        ORDER BY creation_date DESC
        LIMIT :offset, :limit";
    $statement = $pdo->prepare($sql);
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

function return_comment_section($img_id) {
    $pdo = connect_todb();
    $sql = "SELECT content, verified_users.username as author
        FROM comments, verified_users
        WHERE img_id=:img_id
        AND comments.userid = verified_users.userid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return $row;
    } else {
        return null;
    }
}

function save_comment($img_id, $userid, $content){
    $pdo = connect_todb();
    $sql = "INSERT INTO comments(
        img_id,
        userid,
        content)
        VALUES(
            :img_id,
            :userid,
            :content)";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->bindParam(":userid", $userid);
    $statement->bindParam(":content", $content);
    $statement->execute();
    return ;
}

function return_like_button($img_id, $username) {
    $userinfo = fetch_user_info($username);
    $pdo = connect_todb();
    $sql = "SELECT 1
        FROM likes
        WHERE
        img_id=:img_id
        AND
        userid=:userid";
    $userid = $userinfo[0]["userid"];
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->bindParam(":userid", $userid);
    $statement->execute();
    $row = $statement->fetchAll();
    if (!empty($row)) {
        return '<button class="like" value="'.$username.'">UNLIKE</button>';
    } else {
        return '<button class="like" value="'.$username.'">LIKE</button>';
    }
}

function is_liking($img_id, $username) {
    $userinfo = fetch_user_info($username);
    $pdo = connect_todb();
    $sql = "SELECT *
        FROM likes
        WHERE
        img_id=:img_id
        AND
        userid=:userid";
    $userid = $userinfo[0]["userid"];
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->bindParam(":userid", $userid);
    $statement->execute();
    $row = $statement->fetchColumn();
    if (!$row) {
        return null;
    } else {
        return $row;
    }
}

function save_like($img_id, $userid) {
    $pdo = connect_todb();
    $sql = "INSERT INTO likes(
        img_id,
        userid)
        VALUES(
            :img_id,
            :userid)";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->bindParam(":userid", $userid);
    $statement->execute();
    return;
}

function delete_like($img_id, $userid) {
    $pdo = connect_todb();
    $sql = "DELETE FROM likes
        WHERE
        img_id=:img_id
        AND
        userid=:userid";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(":img_id", $img_id);
    $statement->bindParam(":userid", $userid);
    $statement->execute();
    return;
}

function delete_specific_pic($imgid) {
    $pdo = connect_todb();
    $sql = 'DELETE FROM user_galleries
        WHERE rowid=:imgid';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':imgid', $imgid);
    $statement->execute();
}

function delete_specific_pic_comments($imgid) {
    $pdo = connect_todb();
    $sql = 'DELETE FROM comments
        WHERE img_id=:imgid';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':imgid', $imgid);
    $statement->execute();
}

?>

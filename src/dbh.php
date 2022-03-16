<?php

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
        ROWID,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        userpwd TEXT NOT NULL,
        PRIMARY KEY (ROWID))",

        "CREATE TABLE IF NOT EXISTS pending_users(
        ROWID,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        userpwd TEXT NOT NULL,
        activation_code varchar(255) NOT NULL,
        activation_expiry datetime NOT NULL,
        PRIMARY KEY (ROWID))"];

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
}

?>

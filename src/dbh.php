<?php

function tables_exist($pdo)
{
    // Try a select statement against the table
    // Run it in try-catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $pdo->query("SELECT 1 FROM login LIMIT 1");
        //$pdo->query("SELECT 1 FROM overlayimages LIMIT 1");
    } catch (Exception $e) {
        // We got an exception (table not found)
        return FALSE;
    }
    return TRUE ;
}

function create_tables($pdo) {
    $commands =
        ["CREATE TABLE IF NOT EXISTS login (
            username TEXT NOT NULL,
            email TEXT NOT NULL,
            userpwd TEXT NOT NULL,
            active TINYINT(1) DEFAULT 0,
            activation_code   varchar(255) NOT NULL,
            activation_expiry datetime     NOT NULL,
            activated_at datetime DEFAULT NULL,
            PRIMARY KEY ( username, email ));"];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }
}

function connect_todb() {
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

<?php
include_once "dbh.php";
include_once "include.php";
include_once "lib.php";

function check_reset_hash(string $id, string $hash): bool {
    $pdo = connect_todb();
    $sql = "SELECT 1
        FROM reset_pwd_hashes
        WHERE userid=:id
        AND reset_hash =:hash
        LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $id);
    $statement->bindParam(':hash', $hash);
    $statement->execute();
    $res = $statement->fetchAll();
    return (count($res) === 1);
}

$id = $_GET['id'];
$hash = $_GET['reset_pwd'];

if ($id && $hash) {
    // verify id and reset_hash relation
    // if all good, make a secured form
    // if NOT, redirect to index, abort reset
    //
    if (check_reset_hash($id, $hash)) {
        $row = fetch_user_info($id);
        $_SESSION["username"] = $row[0]["username"];
        include_once "reset.php";

    } else {
        $body = "<h1>ID or HASH do NOT match...
            We all make mistakes in the heat of passion, jimbo</h1>";
        include_once "template.php";

    }
} else {
    $body = "<h1>ARE YOU TRYING TO BE SNEAKY ??</h1>";
    include_once "template.php";
}
?>

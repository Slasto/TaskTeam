<?php
session_start();

//header('Content-Type: text/html');

if (!isset($_SESSION["username"]) && !isset($_SESSION['logged_in']) && !isset($_SESSION['user_id'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

require_once "../../../private/Database.php";
$stmt = $pdo->prepare("DELETE FROM User WHERE Username = :username AND ID = :id");
$stmt->execute([
    "username" => $_SESSION["username"],
    "id" => $_SESSION['user_id']
]);
echo "1";
unset($pdo);
unset($stmt);

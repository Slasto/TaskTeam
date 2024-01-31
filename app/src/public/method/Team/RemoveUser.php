<?php
session_start();

header('Content-Type: text/html');
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_POST["TeamID"]) || !isset($_SESSION['logged_in'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

// è un intero?
$TeamID = $_POST["TeamID"];
if (!is_numeric($_POST["TeamID"])) {
    echo "0";
    exit();
}


// Rimozione del utente
$TeamID = intval($TeamID);
require_once "./../../../private/Database.php";
$stmt = $pdo->prepare("DELETE FROM UserInTeam WHERE TeamID = :Team AND UserID = :User");
$stmt->execute([
    "Team" => $TeamID,
    "User" => $_SESSION['user_id']
]);
echo "1";
unset($pdo);
unset($stmt);

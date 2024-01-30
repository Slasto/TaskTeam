<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_SESSION['logged_in']) || !isset($_POST["AttivitaID"]) || !isset($_POST["TeamID"])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}
//Verifica dati
$AttivitaID = $_POST["AttivitaID"];
$TeamID = $_POST["TeamID"];
require_once "./../../../private/ActivityDataValidation.php";
if (!is_numeric($TeamID) || !is_numeric($AttivitaID)) {
    echo 0;
    exit();
}
$TeamID = intval($TeamID);
$AttivitaID = intval($AttivitaID);


require_once "./../../../private/Database.php";
//Controllo che l'utente è nel team
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :u AND TeamID = :t");
$stmt->execute([
    "u" => $_SESSION["user_id"],
    "t" => $TeamID
]);

if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
    echo "-1"; //Utente non è nel team
    return;
}

$stmt = $pdo->prepare("DELETE FROM Attivita WHERE ID = :a AND FK_TeamID = :t");
$stmt->execute([
    "a" => $AttivitaID,
    "t" => $TeamID
]);
echo "1";
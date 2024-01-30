<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_POST["From"]) || !isset($_POST["User"]) || !isset($_SESSION['logged_in'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

// validazione dati
require_once "./../../../private/UserDataValidation.php";
$TeamID = $_POST["From"];
if (!is_numeric($_POST["From"]) || !is_valid_username($_POST["User"])) {
    echo "0";
    exit();
}
$TeamID = intval($TeamID);

require_once "./../../../private/Database.php";
// Prendo l'username del proprietario
$stmt = $pdo->prepare("SELECT FK_UsernameProprietario FROM Team WHERE ID = :Team ");
$stmt->execute([
    "Team" => $TeamID,
]);
$ProprietarioTeam = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$ProprietarioTeam) {
    echo "-1";
    exit();
}
$ProprietarioTeam =  $ProprietarioTeam[0]["FK_UsernameProprietario"];

//si sta cercando di rimuovere il proprietario dal Team? OR è il proprietario del team che sta facendo la richiesta?
if ($ProprietarioTeam === $_POST["User"] || $ProprietarioTeam !== $_SESSION["username"]) {
    echo "-1";
    exit();
};

//Prendo l'userID
$stmt = $pdo->prepare("SELECT ID FROM User WHERE Username = :user");
$stmt->execute([
    "user" => $_POST["User"],
]);
$userID = ($stmt->fetchAll(PDO::FETCH_ASSOC))[0]["ID"];
if (!$userID) {
    echo "0"; //l'utente non esiste
}

$stmt = $pdo->prepare("DELETE FROM UserInTeam WHERE TeamID = :Team AND UserID = :User");
$stmt->execute([
    "Team" => $TeamID,
    "User" => $userID
]);

echo "1";

unset($pdo);
unset($stmt);

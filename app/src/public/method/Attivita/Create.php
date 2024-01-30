<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_SESSION['logged_in']) || !isset($_POST["Titolo"]) || !isset($_POST["Descrizione"]) || !isset($_POST["Data"]) || !isset($_POST["TeamID"])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

$title = $_POST["Titolo"];
$description = ($_POST["Descrizione"] == "") ? "None" : $_POST["Descrizione"];
$expireData = $_POST["Data"];
$TeamID = $_POST["TeamID"];

//Verifica dati
require_once "./../../../private/ActivityDataValidation.php";
if (!is_numeric($TeamID) || !is_valid_activity_title($title) || !is_valid_activity_description($description) || !is_valid_activity_expire_date($expireData)) {
    echo 0;
    exit();
}
$TeamID = intval($TeamID);


require_once "./../../../private/Database.php";
//Controllo che l'utente è nel team
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :u AND TeamID = :t");
$stmt->execute([
    "u"=>$_SESSION["user_id"],
    "t"=>$TeamID
]);

if(!($stmt->fetchAll(PDO::FETCH_ASSOC))){
    echo "-1"; //Utente non è nel team
    return;
}

//Creazione del attivita
$stmt = $pdo->prepare("INSERT INTO Attivita (Titolo,Descrizione,Stato,Scadenza,FK_TeamID) VALUES (:Titolo,:Descrizione,'Da fare',:Scadenza,:TeamID)");
$stmt->execute([
    "Titolo"=>$title,
    "Descrizione"=>$description,
    "Scadenza"=>($expireData=="") ? null : $expireData,
    "TeamID"=>$TeamID
]);
echo "1"; //Utente non è nel team

($_POST["Descrizione"] == "") ? "None" : $_POST["Descrizione"];
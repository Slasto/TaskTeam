<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_POST["TeamID"]) || !isset($_POST["TeamName"]) || !isset($_POST["TeamDescription"]) || !isset($_SESSION['logged_in'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

//Validazione dati
require_once "./../../../private/TeamDataValidation.php";
$TeamID = $_POST["TeamID"];
$TeamName = $_POST["TeamName"];
$TeamDescription = $_POST["TeamDescription"];
if (!is_numeric($TeamID) || !is_valid_team_name($TeamName) || !is_valid_team_description($TeamDescription)) {
    echo "0";
    exit();
}
$TeamID = intval($TeamID);


require_once "./../../../private/Database.php";
//è l'utente il proprietario del team(!=Privato)?
$stmt = $pdo->prepare("SELECT * FROM Team WHERE ID = :TeamID AND  FK_UsernameProprietario = :Username AND Nome!=\"Privato\"");
$stmt->execute([
    "TeamID" => $TeamID,
    "Username" => $_SESSION["username"]

]);
if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
    echo "-1";
    exit();
};

//Modifica effettiva dei dati
if ($TeamDescription == "")
    $TeamDescription = "None";

$stmt = $pdo->prepare("UPDATE Team SET Nome = :TeamName , Descrizione = :TeamDescription WHERE FK_UsernameProprietario = :Proprietario AND ID = :TeamId");
$stmt->execute([
    "TeamName" => $TeamName,
    "TeamDescription" => $TeamDescription,
    "Proprietario" => $_SESSION["username"],
    "TeamId" => $TeamID
]);

echo "1";
unset($pdo);
unset($stmt);

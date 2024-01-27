<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if ( !isset($_POST["TeamCode"]) || !isset($_SESSION["username"]) && !isset($_SESSION['logged_in']) && !isset($_SESSION['user_id'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

require_once "./../../../private/TeamDataValidation.php";
$code = $_POST["TeamCode"];
if(!is_valid_team_code($code)){
    echo "0";
}

//Controllo del esistenza del Team+Ottenimento del id
require_once "./../../../private/Database.php";
$stmt = $pdo->prepare("SELECT ID FROM Team Where CodiceInvito = :code");
$stmt->execute([
    "code"=>$code
]);

$TeamID = ($stmt->fetchAll(PDO::FETCH_ASSOC))[0];

if (!$TeamID){
    echo "0";
    exit();
}
$TeamID = $TeamID["ID"];


// Controllo de l'user_id si trova gia nel team
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam Where TeamID = :team AND UserID = :user");
$stmt->execute([
    "team"=>$TeamID,
    "user"=>$_SESSION['user_id']
]);
if(($stmt->fetchAll(PDO::FETCH_ASSOC))){
    echo "2";
    exit();
}


// Aggiunta del utente
$stmt = $pdo->prepare("INSERT INTO UserInTeam (UserID, TeamID) VALUES (:user, :team)");
$stmt->execute([
    "user"=>$_SESSION['user_id'],
    "team"=>$TeamID
]);
unset($pdo);
unset($stmt);

echo "1";

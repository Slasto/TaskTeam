<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_SESSION['logged_in']) || !isset($_POST["AttivitaID"]) || !isset($_POST["TeamID"]) || !isset($_POST["SwitchTo"])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

$TeamID = $_POST["TeamID"];
$ActivityID = $_POST["AttivitaID"];
$SwitchTo = $_POST["SwitchTo"];
if (!is_numeric($TeamID) || !is_numeric($ActivityID) || !is_numeric($SwitchTo)) {
    echo "0";
    exit();
}
$TeamID = intval($TeamID);
$ActivityID = intval($ActivityID);
$SwitchTo = intval($SwitchTo);

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

//Se in stato fatto, la modifica non può avvenire
$stmt = $pdo->prepare("SELECT Assegnato FROM Attivita WHERE ID = :a AND FK_TeamID = :t AND Stato!='Fatto'");
$stmt->execute([
    "a" => $ActivityID,
    "t" => $TeamID
]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$data) {
    echo "0";
    exit();
}

switch ($_POST["Admin"]) {
    case null:
        //Richiesta di membro
        $data = $data[0];
        if ($data["Assegnato"] !== $_SESSION["username"] || $data["Assegnato"] !== "") {
            echo "0";
            exit();
        }
        $stmt = $pdo->prepare("UPDATE Attivita SET Assegnato = :A WHERE ID = :id AND FK_TeamID = :t AND Stato!='Fatto'");
        $stmt->execute([
            "A" => (($SwitchTo == -1) ? null : $_SEASON["username"]),
            "id" => $ActivityID,
            "t" => $TeamID
        ]);
        break;

    default:
        //Richiesta del presunto leader membro
        $UserToMod = $_POST["Admin"];

        //Controlla che l'utente è il proprietario
        $stmt = $pdo->prepare("SELECT ID FROM Team WHERE ID = :Team AND FK_UsernameProprietario = :fk");
        $stmt->execute([
            "Team" => $TeamID,
            "fk" => $_SEASON["username"]
        ]);
        if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
            echo "-1";
            exit();
        }

        //L'utente è nel team?
        $stmt = $pdo->prepare("SELECT ID FROM User WHERE Username = :u");
        $stmt->execute([
            "u" => $_POST["Admin"],
        ]);
        $userID = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$userID) {
            echo "0";
            exit();
        }
        $userID = $userID[0]["ID"];

        $stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :u AND TeamID = :t");
        $stmt->execute([
            "u" => $userID,
            "t" => $TeamID
        ]);

        if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
            echo "-1"; //Utente non è nel team
            return;
        }

        //faccio la modifica
        $stmt = $pdo->prepare("UPDATE Attivita SET Assegnato = :A WHERE ID = :id AND FK_TeamID = :t AND Stato!='Fatto'");
        $stmt->execute([
            "A" => (($SwitchTo == -1) ? null : $_POST["Admin"]),
            "id" => $ActivityID,
            "t" => $TeamID
        ]);

        break;
}

unset($stmt);
unset($pdo);

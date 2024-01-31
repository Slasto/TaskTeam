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
require_once "./../../../private/Database.php";
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :u AND TeamID = :t");
$stmt->execute([
    "u" => $_SESSION["user_id"],
    "t" => $TeamID
]);

if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
    echo "0"; //Utente non è nel team
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

switch (isset($_POST["Admin"])) {
    case false:
        //Richiesta di membro
        $data = $data[0];

        switch ($SwitchTo) {
            case '1':
                //se sto tentando il set ma è gia stato assegnato a qualcun altro
                if ($data["Assegnato"] !== $_SESSION["username"] && $data["Assegnato"] !== null) {
                    echo "-1"; //set a qualcun altro;
                    exit();
                }
                break;

            case '-1':
                //se sto tentando il reset ma NON è stato assegnato a me
                if ($SwitchTo == -1 && $data["Assegnato"] !== $_SESSION["username"]) {
                    echo "-2";
                    exit();
                }
                break;
        }

        $stmt = $pdo->prepare("UPDATE Attivita SET Assegnato = :A WHERE ID = :id AND FK_TeamID = :t AND Stato!='Fatto'");
        $stmt->execute([
            "A" => (($SwitchTo == -1) ? null : $_SESSION["username"]),
            "id" => $ActivityID,
            "t" => $TeamID
        ]);
        break;

    case true:
        //Controlla che l'utente è il proprietario
        $stmt = $pdo->prepare("SELECT ID FROM Team WHERE ID = :Team AND FK_UsernameProprietario = :fk");
        $stmt->execute([
            "Team" => $TeamID,
            "fk" => $_SESSION["username"]
        ]);
        if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
            echo "-1";
            exit();
        }

        $userID = $_POST["Admin"];
        if ($userID !== "") {
            if (!is_numeric($userID)) {
                echo "-1";
                exit();
            }
            $userID = intval($userID);

            $stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :u AND TeamID = :t");
            $stmt->execute([
                "u" => $userID,
                "t" => $TeamID
            ]);

            if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
                echo "-1"; //Utente non è nel team
                exit();
            }

            //L'utente è nel team?
            $stmt = $pdo->prepare("SELECT Username FROM User WHERE ID = :u");
            $stmt->execute([
                "u" => $userID,
            ]);
            $username = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$username) {
                echo "-2";
                exit();
            }
            $username = $username[0]["Username"];
        }


        //faccio la modifica
        $stmt = $pdo->prepare("UPDATE Attivita SET Assegnato = :A WHERE ID = :id AND FK_TeamID = :t AND Stato!='Fatto'");
        $stmt->execute([
            "A" => (($userID === "") ? null : $username),
            "id" => $ActivityID,
            "t" => $TeamID
        ]);
        break;
}

echo "1";
unset($stmt);
unset($pdo);

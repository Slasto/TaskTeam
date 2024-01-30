<?php
session_start();

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_SESSION['logged_in']) || !isset($_POST["Titolo"]) || !isset($_POST["Descrizione"]) || !isset($_POST["Stato"]) || !isset($_POST["Scadenza"]) || !isset($_POST["AttivitaID"]) || !isset($_POST["TeamID"])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

$TeamID = $_POST["TeamID"];
$AttivitaID = $_POST["AttivitaID"];
$Titolo = $_POST["Titolo"];
$Descrizione = ($_POST["Descrizione"] == "") ? "None" : $_POST["Descrizione"];;
$Stato = $_POST["Stato"];
$Expire = $_POST["Scadenza"];

//Verifica dati
require_once "./../../../private/ActivityDataValidation.php";
if (!is_numeric($TeamID) || !is_numeric($AttivitaID) || !is_valid_activity_title($Titolo) || !is_valid_activity_description($Descrizione) || !is_valid_activity_expire_date($Expire) || is_valid_activity_status($Stato)) {
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

//Controllo se l'attivita è gia stata completata
$stmt = $pdo->prepare("SELECT Stato FROM Attivita WHERE ID = :a AND FK_TeamID = :t");
$stmt->execute([
    "a" => $AttivitaID,
    "t" => $TeamID
]);
$OldStato = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$OldStato) {
    echo "-1"; //non esiste l'attivita non è nel team
    return;
}

$OldStato = $OldStato[0]["Stato"];
unset($stmt);
if ($OldStato !== "Fatto" && $Stato === "Fatto") {
    // Lo stato è passato ad essere "fatto"...
    $stmt = $pdo->prepare("UPDATE Attivita SET Titolo = :title, Descrizione=:descr, Stato=:stato, Scadenza=:scad, FattoIl = :dataFatto WHERE ID = :Aid AND FK_TeamID = :Tid");
    $dateFatto = (new DateTime('now', new DateTimeZone('Europe/Rome')))->format('Y-m-d');
    $stmt->bindParam("dataFatto", $dateFatto);
    unset($dateFatto);
    $stmt->bindParam("stato", $Stato);
} elseif ($OldStato ===  $Stato) {
    //Se lo stato non è cambiato sicuramente FattoIl non la devo impostare
    $stmt = $pdo->prepare("UPDATE Attivita SET Titolo = :title, Descrizione=:descr, Scadenza=:scad WHERE ID = :Aid AND FK_TeamID = :Tid");
} else {
    if ($OldStato === "Fatto" && $Stato !== "Fatto")
        $pdo->exec("UPDATE Attivita SET FattoIl = null WHERE ID =" . $AttivitaID . " AND FK_TeamID =" . $TeamID);

    $stmt = $pdo->prepare("UPDATE Attivita SET Titolo = :title, Descrizione=:descr, Scadenza=:scad, Stato=:stato  WHERE ID = :Aid AND FK_TeamID = :Tid");
    $stmt->bindParam("stato", $Stato);
}
unset($OldStato);

$stmt->bindParam(":title", $Titolo);

$Descrizione = ($Descrizione == "") ? "None" : $Descrizione;
$stmt->bindParam(":descr", $Descrizione);

$Expire = ($Expire == "") ? null : $Expire;
$stmt->bindParam(":scad", $Expire);

$stmt->bindParam(":Aid", $AttivitaID);

$stmt->bindParam(":Tid", $TeamID);

$stmt->execute();
unset($pdo);
unset($stmt);

echo "1";

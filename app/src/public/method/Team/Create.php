<?php
function generateRandomString()
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^*_|', ceil(12 / strlen($x)))), 1, 12);
}

session_start();
header('Content-Type: text/html');
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    echo "0";
    return;
}

if (!isset($_POST["TeamName"]) || !isset($_SESSION['logged_in'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "0";
    exit();
}

require_once "./../../../private/TeamDataValidation.php";
$Name = $_POST["TeamName"];
if (!is_valid_team_name($Name)) {
    echo "0";
    exit();
}

require_once "./../../../private/Database.php";
$finalQuery = $pdo->prepare("INSERT INTO Team (Nome, Descrizione, CodiceInvito, FK_UsernameProprietario) VALUES (:Nome, :Descrizione, :CodiceInvito, :FK_UsernameProprietario)");
switch (isset($_POST["Description"])) {
    case true: //esiste Description
        $Description = $_POST["Description"];
        break;
    case false: //non Description
        $Description = "None";
        break;
}

$bool = true;
$testInvitationCode = $pdo->prepare("SELECT ID FROM Team Where CodiceInvito=:TestCode");
do {
    $CodiceInvito = generateRandomString();
    $testInvitationCode->execute(["TestCode" => $CodiceInvito]);
    if (!($testInvitationCode->fetchAll(PDO::FETCH_ASSOC))) {
        //un elemento NON è gia presente con tale codice
        $bool = false;
    }
} while ($bool);
unset($testInvitationCode);

$finalQuery->execute(
    [
        "Nome" => $Name,
        "Descrizione" => $Description,
        "CodiceInvito" => $CodiceInvito,
        "FK_UsernameProprietario" => $_SESSION["username"]
    ]
);

echo "1";

unset($pdo);
unset($finalQuery);

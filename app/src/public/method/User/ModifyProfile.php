<?php
session_start();
require_once "./../../../private/UserDataValidation.php";
function bad_request()
{
    $risposta = array(
        'stato' => 0,
        'messaggio' => "Bad Request"
    );
    header("HTTP/1.0 400 Bad Request");
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    return;
}

header('Content-Type: application/json');
if (!isset($_GET["change"]) && !(strcmp($_GET["change"], "Password") == 0 || strcmp($_GET["change"], "Email") == 0) && !isset($_SESSION["username"]) && !isset($_SESSION['logged_in']) && !isset($_SESSION['user_id'])) {
    bad_request();
}

require_once "../../../private/Database.php";
switch ($_GET["change"]) {
    case 'Email':
        if (!isset($_POST["email"]))
            bad_request();
        $stmt = $pdo->prepare("UPDATE User SET  Email = :email  WHERE Username = :username AND ID = :id");
        $stmt->execute([
            "email" => $_POST["email"],
            "username" => $_SESSION["username"],
            "id" => $_SESSION['user_id']
        ]);
        $risposta = array(
            'stato' => 1,
            'messaggio' => "Email cambiata correttamente"
        );
        break;

    case 'Password':
        if (!isset($_POST["password"]))
            bad_request();

        $stmt = $pdo->prepare("SELECT Salt FROM User WHERE Username = :username AND ID = :id");
        $stmt->execute([
            "username" => $_SESSION["username"],
            "id" => $_SESSION["user_id"]
        ]);
        $Salt = ($stmt->fetchAll(PDO::FETCH_ASSOC))[0]["Salt"];

        $stmt = $pdo->prepare("UPDATE User SET HashPw = :HashPw WHERE Username = :username AND ID = :id");

        $stmt->execute([
            "HashPw" => password_hash($_SESSION["username"] . ":" . $Salt, PASSWORD_DEFAULT),
            "username" => $_SESSION["username"],
            "id" => $_SESSION['user_id']
        ]);
        $risposta = array(
            'stato' => 1,
            'messaggio' => "Password cambiata correttamente"
        );
        break;
    default:
        exit(-1);
        break;
};

unset($pdo);
unset($stmt);
echo json_encode($risposta, JSON_PRETTY_PRINT);

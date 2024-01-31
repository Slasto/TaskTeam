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
if (!isset($_GET["change"]) && !(strcmp($_GET["change"], "Password") == 0 || strcmp($_GET["change"], "Email") == 0) && !isset($_SESSION['logged_in'])) {
    bad_request();
}

require_once "../../../private/Database.php";

switch ($_GET["change"]) {
    case 'Email':
        if (!isset($_POST["email"]))
            bad_request();
        if(!is_valid_email($_POST["email"])){
            $risposta = array(
                'stato' => 0,
                'messaggio' => "Email non valida"
            );
            break;
        }
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

        if(!is_valid_password($_POST["password"])){
            $risposta = array(
                'stato' => 0,
                'messaggio' => "password non valida"
            );
            break;
        }

        $stmt = $pdo->prepare("UPDATE User SET HashPw = :HashPw WHERE Username = :username AND ID = :id");
        $stmt->execute([
            "HashPw" => password_hash($_POST["password"], PASSWORD_DEFAULT),
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

echo json_encode($risposta, JSON_PRETTY_PRINT);
unset($pdo);
unset($stmt);
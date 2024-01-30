<?php

require_once "./../../../private/UserDataValidation.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("HTTP/1.0 405 Method Not Allowed");
    return;
}

header('Content-Type: application/json');
// POST
if (!isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["username"])) {
    $risposta = array(
        'stato' => 0,
        'messaggio' => "Tutti i campi devono essere riempiti"
    );
    header("HTTP/1.0 400 Bad Request");
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

$user  = $_POST['username'];
$email = $_POST['email'];
$pwd   = $_POST['password'];

// controllo sul Email
if (!is_valid_email($email)) {
    $risposta = array(
        'stato' => 0,
        'messaggio' => "Indirizzo email non valido"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

/* controllo sul username:
 *  - unico e 
 *  - stessa regex del frontend
 */
if (!is_valid_username($user)) {
    $risposta = array(
        'stato' => 0,
        'messaggio' => "L'Username non può: contenere caratteri speciali e accentati, ed essere più lungo di 32 caratteri"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

require_once "../../../private/Database.php";
$stmt = $pdo->prepare('SELECT id FROM User WHERE Username = :username');
$stmt->execute([':username' => $user]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($data) != 0) {
    $risposta = array(
        'stato' => 0,
        'messaggio' => "L'Username inserito risulta già registrato"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

// controllo sulla password
if (!is_valid_password($pwd)) {
    $risposta = array(
        'stato' => 0,
        'messaggio' => "La lunghezza della password deve essere tra 12 e 255 caratteri\nNon deve contenere caratteri accentati\nI caratteri speciali consentiti !@#$%^&*?."
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

// Se tutti i check sono validati, posso procedere ad registrare l'utente
$stmt = $pdo->prepare('INSERT INTO `User` (`ID`,`Username`, `Email`, `HashPW`, `LastR`) VALUES (null, :username, :email, :HashPW, null);');

//TODO: post implementazione del login(hash(hash(password+salt)+R)) sicuro, creare la stringa randomizza di 12 caratteri ascii
//$salt = md5(rand());
$stmt->execute([
    ':username' => $user,
    ':email' => $email,
    ':HashPW' => password_hash($pwd, PASSWORD_DEFAULT),
]);
unset($pdo);
unset($stmt);

$risposta = array(
    'stato' => 1,
    'messaggio' => ""
);
echo json_encode($risposta, JSON_PRETTY_PRINT);

<?php
function is_valid_username($username){
    $pattern = '/^[a-zA-Z ]{1,32}$/';
    if (preg_match($pattern, $username))
        return true;
    return false;
}

function is_valid_email($email) {
    $pattern = '/^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}){1,50}$/';
    if (preg_match($pattern, $email))
        return true;
    return false;
}

function is_valid_password($password) {
    $pattern = '/^[\w\s!@#$%^&*?.]{12,255}$/';
    if (preg_match($pattern, $password))
        return true;
    return false;
}

if ($_SERVER["REQUEST_METHOD"]!="POST"){
    header("HTTP/1.0 405 Method Not Allowed");
    return;
}

// POST
if (!isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["username"])){
    $risposta = array(
        'stato' => 0,
        'messaggio' => "Tutti i campi devono essere riempiti"
    );
    header("HTTP/1.0 400 Bad Request");
    header('Content-Type: application/json');
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

$user  = $_POST['username'];
$email = $_POST['email'];
$pwd   = $_POST['password'];

header('Content-Type: application/json');

// controllo sul eamil
if (!is_valid_email($email)){
    $risposta = array(
        'stato' => 0,
        'messaggio' => "Indirizzo email non valido"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

// controllo sul username che è unico e stessa regex del frontedn
if (!is_valid_username($user)){
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
if (count($data)!=0){
    $risposta = array(
        'stato' => 0,
        'messaggio' => "L'Username inserito risulta già registrato"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

// controllo sulla password
if (!is_valid_password($pwd)){
    $risposta = array(
        'stato' => 0,
        'messaggio' => "La lunghezza della password deve essere tra 12 e 255 caratteri e senza caratteri accentati"
    );
    echo json_encode($risposta, JSON_PRETTY_PRINT);
    exit;
}

// allora crea l'user con i dati forniti
$stmt = $pdo->prepare('INSERT INTO `User` (`ID`,`Username`, `Email`, `HashPW`, `Salt`, `LastR`) VALUES (null, :username, :email, :HashPW, :salt, null);');

//TODO: post implementazione del login(hash(hash(pswd+salt)+R)) sicuro, creare la stringa randomica di 12 caratteri ascii
$stmt->execute([
    ':username' => $user,
    ':email' => $email,
    ':HashPW' => password_hash($pwd, PASSWORD_DEFAULT),
    ':salt' => "ToDo"
]);
unset($pdo);
unset($stmt);

$risposta = array(
   'stato' => 1,
   'messaggio' => ""
);
echo json_encode($risposta, JSON_PRETTY_PRINT);






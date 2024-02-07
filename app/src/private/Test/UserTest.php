<?php
require('./vendor/autoload.php');

use PHPUnit\Framework\TestCase;

/*
    I seguenti test sono pensati per essere eseguiti uno dopo l'altro e non separatamente
*/

class UserTest extends TestCase
{
    private $pdo;
    private $email = 'test@example.com';
    private $password = 'password1235489';
    private $username = 'kHbJNvFZCLwXqfyPIpG'; // se questo viene modifica ricorda di modificare anche username in tearDownAfterClass
    private $PrivateTeam = "";

    protected function setUp(): void
    {
        $this->pdo = new PDO("mysql:host=172.24.0.2;dbname=SWBD-database", 'root', '1234');
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }


    public function testRegister(): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE Username = :user");
        $stmt->execute(["user" => ($this->username)]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertNotNull($data, "ERR! Un utente con Username=" . $this->username . "' già esiste.");

        $data = array(
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password
        );

        //creazione utente
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/method/User/Create');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $this->assertEquals(1, $response['stato'], "ERR! Non è stato creato nessun utente." . $response['messaggio']);
        error_log("Test Create.php:" . print_r($response, true) . PHP_EOL . "-------------");
    }

    public function testTriggerPrivate(): void
    {
        $stmt = $this->pdo->prepare("SELECT ID FROM Team WHERE FK_UsernameProprietario = :username AND Nome = 'Privato' AND CodiceInvito IS NULL");
        $stmt->execute(['username' => $this->username]);
        $queryResult = $stmt->fetch();
        $this->assertNotNull($queryResult, "ERR! Il trigger per la creazione del area privata del user è fallito");
        error_log("Test Team Private team:" . print_r($queryResult, true) . PHP_EOL . "-------------");
    }

    public function testAttivita(): void
    {
        $stmt = $this->pdo->prepare("SELECT ID FROM Team WHERE FK_UsernameProprietario = :username AND Nome = 'Privato' AND CodiceInvito IS NULL");
        $stmt->execute(['username' => $this->username]);
        $TeamID = $stmt->fetch()["ID"];
        error_log($TeamID);


        error_log("Test ID Team" . $this->PrivateTeam);
        $stmt = $this->pdo->prepare("INSERT INTO Attivita (Titolo, Descrizione,	Stato, Scadenza, FK_TeamID) VALUES ('Test','Test','Da Fare','2030-10-10',:TeamID)");
        $stmt->execute(["TeamID" => $TeamID]);

        $selectStmt = $this->pdo->prepare("SELECT * FROM Attivita WHERE FK_TeamID = :id");
        $selectStmt->execute(['id' => $TeamID]);
        $queryResult = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertNotNull($queryResult, "ERR! Attivita non creata");
        error_log("Test Team Private team:" . print_r($queryResult, true) . PHP_EOL . "-------------");
    }

    public static function tearDownAfterClass(): void
    {
        $pdo = new PDO("mysql:host=172.24.0.2;dbname=SWBD-database", 'root', '1234');
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $username = 'kHbJNvFZCLwXqfyPIpG';
        $stmt = $pdo->prepare("DELETE FROM User WHERE Username = :username");
        $stmt->execute(['username' => $username]);
    }
}

<?php
$pdo = new PDO("mysql:host=172.24.0.2;dbname=SWBD-database", 'root', '1234');
$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

$unbufferedResult = $pdo->query("SELECT * FROM `User` ");
foreach ($unbufferedResult as $row) {
    echo $row['NomeUtente'].PHP_EOL;
}
?>
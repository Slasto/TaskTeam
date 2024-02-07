<?php
    $pdo = new PDO("mysql:host=db;dbname=SWBD-database", 'root', '1234');
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);


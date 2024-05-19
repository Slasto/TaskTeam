<?php
    $pdo = new PDO("mysql:host=" . getenv('PMA_HOST') . ";dbname=" . getenv('MYSQL_DATABASE'), getenv('PMA_USER'), getenv('MYSQL_ROOT_PASSWORD'));
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

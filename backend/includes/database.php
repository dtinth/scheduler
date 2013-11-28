<?php

require dirname(__FILE__) . '/database.local.php';

try {
    $db = new PDO("mysql:host=localhost;dbname=zp2925_schedule", DB_USER, DB_PASS);
} catch(PDOException $e) {
    die($e->getMessage());
}


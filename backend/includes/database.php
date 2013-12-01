<?php

require_once dirname(__FILE__) . '/database.local.php';

try {
  $db = new PDO("mysql:host=localhost;dbname=zp2925_schedule", DB_USER, DB_PASS);
} catch(PDOException $e) {
  die($e->getMessage());
}

class DbUtil {
  
  static function execute($statement, $what = 'execute SQL') {
    global $db;
    if (!$statement->execute()) {
      throw new Exception("Cannot $what! " . $statement->errorInfo()[2]);
    }
    return $db->lastInsertId();
  }
  
  static function createStatement($sql, $bindings=array()) {
    global $db;
    $statement = $db->prepare($sql);
    foreach ($bindings as $key => $value) {
      $statement->bindValue($key, $value);
    }
    return $statement;
  }
  
  static function query($sql, $bindings=array()) {
    $statement = self::createStatement($sql, $bindings);
    self::execute($statement);
    return $statement;
  }
  
  static function select($sql, $bindings=array()) {
    global $db;
    $statement = self::query($sql, $bindings);
    return $statement->fetchAll();
  }
  
}


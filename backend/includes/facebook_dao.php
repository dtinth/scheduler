<?php

require_once dirname(__FILE__) . '/database.php';
require_once dirname(__FILE__) . '/facebook.php';

class FacebookDao {

  public static function find($id) {
    global $db;
    $row = DbUtil::select("SELECT * FROM users WHERE id = :id", array(':id' => $id));
    if (empty($row)) {
      return null;
    }
    return array(
      'name' => $row[0]['name'],
      'uid' => $row[0]['uid']
    );
  }
  
  public static function getUserId() {
    
    global $db;
    global $facebook;
    
    // Get the facebook user ID.
    $uid = $facebook->getUser();
    
    // Return null if user is not signed in to Facebook.
    if ($uid == 0) return null;
    
    // Get the user profile.
    $profile = $facebook->api('/me', 'GET');
    $name = $profile['name'];
    
    // If there is no record in the database, create new user otherwise, update name.
    $statement = $db->prepare("
      SELECT id FROM users WHERE uid = :uid");
    $statement->bindParam(':uid', $uid);
    DbUtil::execute($statement);
    
    $row = $statement->fetch();
    
    if ($row === false) {
      $statement = $db->prepare("
        INSERT INTO users (uid, name)
        VALUES (:uid, :name)");
      $statement->bindParam(':uid', $uid);
      $statement->bindParam(':name', $name);
      $userId = DbUtil::execute($statement, "insert new user");
    } else {
      $userId = $row['id'];
      $statement = $db->prepare("
        UPDATE users SET name = :name WHERE id = :id");
      $statement->bindParam(':id', $userId);
      $statement->bindParam(':name', $name);
      DbUtil::execute($statement, "update user's name");
    }
    
    return $userId;
    
  }

}
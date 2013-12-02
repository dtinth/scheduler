<?php

require_once 'includes/schedule_dao.php';
require_once 'includes/facebook_dao.php';
require_once 'includes/json_api.php';

json_api(function($params) {

  $courses = $params->courses;
  $name    = $params->name;

  if (!$courses) {
    die("NO! WHAT DID YOU SEND ME???? NOOOOO!!!!!!! NAAAHHHHHLLLLL!!!!");
  }
  
  $userId = FacebookDao::getUserId();
  $id = null;
  
  if (isset($params->id) && $userId != null) {
    $key = $params->id;
    $schedule = ScheduleDao::findByKey($key);
    if ($schedule && $schedule['user_id'] == $userId) {
      $id = $schedule['id'];
      DbUtil::query("
        DELETE FROM courses
        WHERE schedule_id = :id", array(':id' => $id));
      DbUtil::query("
        UPDATE schedules
        SET name = :name
        WHERE id = :id", array(':id' => $id, ':name' => $name));
    }
  }
  
  if ($id == null) {
    $id = ScheduleDao::insertSchedule($userId, $name);
  }
  ScheduleDao::insertScheduleData($id, $courses);

  return array('key' => ScheduleDao::key($id));

});










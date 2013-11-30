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
  
  $id = ScheduleDao::insertSchedule($userId, $name);
  ScheduleDao::insertScheduleData($id, $courses);

  return array('key' => ScheduleDao::key($id));

});










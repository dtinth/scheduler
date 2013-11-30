<?php

require_once 'includes/schedule_dao.php';
require_once 'includes/facebook_dao.php';
require_once 'includes/json_api.php';

json_api(function() {

  $key = $_GET['id'];
  $schedule = ScheduleDao::findByKey($key);
  
  $user = null;
  
  if ($schedule['user_id']) {
    $user = FacebookDao::find($schedule['user_id']);
  }
  
  return array(
    'courses' => ScheduleDao::getCourses($schedule['id']),
    'name' => $schedule['name'],
    'user' => $user,
    'created' => $schedule['created_at'],
    'updated' => $schedule['updated_at'],
  );

});






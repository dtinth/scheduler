<?php

require_once '../includes/database.php';
require_once '../includes/json_api.php';

function map_course($course) {
  return array(
    'id' => $course['id'],
    'name' => $course['name']
  );
}

json_api(function() {
  $array = DbUtil::select('SELECT * FROM ku_courses ORDER BY id');
  return array_map('map_course', $array);
});


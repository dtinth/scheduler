<?php

require_once '../includes/database.php';
require_once '../includes/json_api.php';
require_once '../includes/scraper.php';

function filter_section($section) {
  return !empty($section->periods);
}

json_api(function() {
  $year = '56';
  $semester = intval($_GET['semester']);
  $id = $_GET['id'];
  
  $timetable = DbUtil::select("SELECT timetable FROM ku_timetables
    WHERE year = :year AND semester = :semester AND ku_course_id = :id", array(
      ':year' => $year,
      ':semester' => $semester,
      ':id' => $id
  ));
    
  if (empty($timetable)) {
    $url = 'https://inter-regis.ku.ac.th/_webcourse_data.php?key_field=' . $id . '|' . $year . '|' . $semester;
    $html = file_get_contents($url);
    $schedule_scraper = new ScheduleScraper($html);
    
    $result = $schedule_scraper->scrape();
    if (empty($result)) {
      throw new Exception("Cannot fetch the schedule for this course.");
    }
    
    // only take sections that have at least one period
    $result->sections = array_filter($result->sections, 'filter_section');
    if (empty($result->sections)) {
      throw new Exception("There are no open sections in this course.");
    }
    
    $json = ScheduleScraper::toJSON($result);
    DbUtil::query("INSERT INTO ku_timetables (ku_course_id, year, semester, timetable)
      VALUES (:id, :year, :semester, :json)", array(
        ':year' => $year,
        ':semester' => $semester,
        ':id' => $id,
        ':json' => $json
    ));
    return json_decode($json);
  } else {
    return json_decode($timetable[0]['timetable']);
  }
});


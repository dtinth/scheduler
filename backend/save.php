<?php

require 'includes/database.php';

$json = $_POST['data'];
$courses = json_decode($json);

// print_r($courses); // when you want to debug

if (!$courses) {
  die("NO! WHAT DID YOU SEND ME???? NOOOOO!!!!!!! NAAAHHHHHLLLLL!!!!");
}

// schedule --> course --> save section --> period, instructors

$statement = $db->prepare("
  INSERT INTO schedules
    (user_id, created_at, updated_at, name)
  VALUES
    (:user_id, NOW(), NOW(), :name)");
$statement->bindParam(':user_id', $userId);
$statement->bindParam(':name',    $scheduleName);

$userId = null;
$scheduleName = 'Untitled Schedule';

if (!$statement->execute()) {
  die("Cannot insert schedule! " . $statement->errorInfo()[2]);
}

$scheduleId = $db->lastInsertId();

foreach ($courses as $course) {
  
  $statement = $db->prepare("
    INSERT INTO courses
      (course_id, credits_lecture, credits_lab, schedule_id, name)
    VALUES
      (:course_id, :credits_lecture, :credits_lab, :schedule_id, :name)");
      
  $statement->bindParam(':course_id',       $courseCode);
  $statement->bindParam(':schedule_id',     $scheduleId);
  $statement->bindParam(':name',            $courseName);
  $statement->bindParam(':credits_lecture', $creditsLecture);
  $statement->bindParam(':credits_lab',     $creditsLab);
  
  $courseCode = $course->courseId;
  $courseName = $course->courseName;

  $creditsLecture = 3; // TODO
  $creditsLab     = 0; // TODO
  
  if (!$statement->execute()) {
    die("Cannot insert course! " . $statement->errorInfo()[2]);
  }
  
  $courseId = $db->lastInsertId();
  
  foreach ($course->sections as $section) {
  
    $statement = $db->prepare("
      INSERT INTO groups
        (type, section_no, course_id, selected)
      VALUES
        (:type, :section_no, :course_id, :selected)");
        
    $statement->bindParam(':type',            $groupType);
    $statement->bindParam(':section_no',      $sectionNo);
    $statement->bindParam(':course_id',       $courseId);
    $statement->bindParam(':selected',        $selected);
    
    $groupType = 'Lecture'; // TODO
    $sectionNo = $section->sectionNo;
    $selected  = !empty($section->selected) ? 1 : 0;
    
    if (!$statement->execute()) {
      die("Cannot insert section! " . $statement->errorInfo()[2]);
    }
    
    // TODO insert periods
    // TODO insert instructors
    
  }

}

echo "Saved!";


















<?php

require_once dirname(__FILE__) . '/database.php';

class ScheduleDao {
  
  /**
   * Inserts a blank schedule into the database.
   *
   * @return the schedule ID
   */
  static function insertSchedule($userId, $scheduleName) {
  
    global $db;
    
    $statement = $db->prepare("
      INSERT INTO schedules
        (user_id, created_at, updated_at, name, secret)
      VALUES
        (:user_id, NOW(), NOW(), :name, :secret)");
    $statement->bindParam(':user_id', $userId);
    $statement->bindParam(':name',    $scheduleName);
    $statement->bindParam(':secret',  $secret);
    $secret = substr(md5(microtime()), 0, 6);
    
    $scheduleId = DbUtil::execute($statement, "insert schedule");
    return $scheduleId;
  
  }

  /**
   * Insert courses, groups, periods, and instructors into
   * the schedule given by its ID.
   */
  static function insertScheduleData($scheduleId, $courses) {
    
    global $db;
  
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
      
      $courseId = DbUtil::execute($statement, "insert course");
      
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
        
        $groupType = $section->type == 0 ? 'Lecture' : 'Lab';
        $sectionNo = $section->sectionNo;
        $selected  = !empty($section->selected) ? 1 : 0;
        
        $groupId = DbUtil::execute($statement, "insert group");
        
        foreach ($section->periods as $period) {
          
          $statement = $db->prepare("
            INSERT INTO periods
              (day, start_time, finish_time, location, group_id)
            VALUES
              (:day, :start_time, :finish_time, :location, :group_id)");
          
          $statement->bindParam(':day',           $day);
          $statement->bindParam(':start_time',    $startTime);
          $statement->bindParam(':finish_time',   $finishTime);
          $statement->bindParam(':location',      $location);
          $statement->bindParam(':group_id',      $groupId);
          
          $day = intval($period->day);
          $startTime  = self::str_to_minutes($period->start);
          $finishTime = self::str_to_minutes($period->finish);
          $location = $period->place;
          
          DbUtil::execute($statement, "insert period");
          
        }
        
        $instructors = array_map('trim', explode(',', $section->instructor));
        
        foreach ($instructors as $instructor) {
          
          $statement = $db->prepare("
            INSERT INTO instructors
              (name, group_id)
            VALUES
              (:name, :group_id)");
          
          $statement->bindParam(':name', $instructor);
          $statement->bindParam(':group_id', $groupId);
          
          DbUtil::execute($statement, "insert instructor");
          
        }
        
      }
    
    }
  
  }
  
  public static function key($scheduleId) {
    global $db;
    $statement = $db->prepare("
      SELECT secret FROM schedules WHERE id = :id");
    $statement->bindParam(':id', $scheduleId);
    DbUtil::execute($statement);
    return $scheduleId . $statement->fetch()['secret'];
  }
  
  public static function findByKey($key) {
    global $db;
    $id = substr($key, 0, -6);
    $secret = substr($key, -6);
    $statement = $db->prepare("
      SELECT * FROM schedules WHERE id = :id AND secret = :secret");
    $statement->bindParam(':id', $id);
    $statement->bindParam(':secret', $secret);
    DbUtil::execute($statement);
    return $statement->fetch();
  }
  
  public static function getCourses($id) {
    
    $courses = DbUtil::select("
      SELECT * FROM courses WHERE schedule_id = :id", array(':id' => $id));
    $groups = self::groupBy('course_id', DbUtil::select("
      SELECT * FROM groups WHERE course_id IN (
        SELECT id FROM courses WHERE schedule_id = :id)", array(':id' => $id)));
    $periods = self::groupBy('group_id', DbUtil::select("
      SELECT * FROM periods WHERE group_id IN (
        SELECT id FROM groups WHERE course_id IN (
          SELECT id FROM courses WHERE schedule_id = :id))", array(':id' => $id)));
    $instructors = self::groupBy('group_id', DbUtil::select("
      SELECT * FROM instructors WHERE group_id IN (
        SELECT id FROM groups WHERE course_id IN (
          SELECT id FROM courses WHERE schedule_id = :id))", array(':id' => $id)));

    $output = array();
    
    foreach ($courses as $courseRow) {
      $course = array(
        'courseId' => $courseRow['course_id'],
        'courseName' => $courseRow['name'],
        'lecCredit' => $courseRow['credits_lecture'],
        'labCredit' => $courseRow['credits_lab'],
        'sections' => array()
      );
      foreach ($groups[$courseRow['id']] as $groupRow) {
        $section = array(
          'sectionNo' => $groupRow['section_no'],
          'type' => $groupRow['type'] == 'Lecture' ? '0' : '1',
          'instructor' => '',
          'periods' => array(),
          'selected' => $groupRow['selected'] == '0' ? false : true
        );
        foreach ($periods[$groupRow['id']] as $periodRow) {
          $period = array(
            'day' => $periodRow['day'],
            'start' => self::minutes_to_str($periodRow['start_time']),
            'finish' => self::minutes_to_str($periodRow['finish_time']),
            'place' => $periodRow['location']
          );
          $section['periods'][] = $period;
        }
        $sectionInstructors = array();
        foreach ($instructors[$groupRow['id']] as $instructorRow) {
          $sectionInstructors[] = $instructorRow['name'];
        }
        $section['instructor'] = implode(', ', $sectionInstructors);
        $course['sections'][] = $section;
      }
      $output[] = $course;
    }
    
    return $output;
    
  }
  
  // =========== UTILITIES FUNCTIONS ==============
  
  /**
   * Converts $str from format of 1200 or 12:00 into
   * minutes since midnight (example: 12:00 becomes 720).
   */
  private static function str_to_minutes($str) {
    $str .= '';
    $str = str_replace(':', '', $str);
    return substr($str, 0, 2) * 60 + substr($str, 2, 2) * 1;
  }
  private static function minutes_to_str($mins) {
    return sprintf("%02d:%02d", (int)($mins / 60), $mins % 60);
  }
  
  private static function groupBy($field, $rows) {
    $output = array();
    foreach ($rows as $row) {
      $key = $row[$field];
      if (!isset($output[$key])) $output[$key] = array();
      $output[$key][] = $row;
    }
    return $output;
  }

}
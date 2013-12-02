<?php

use Symfony\Component\DomCrawler\Crawler;
require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php';

class Period {

  public $day; // 0-6 = sun-sat
  public $startTime; // minutes since midnight (9:30 = 570)
  public $finishTime;
  public $location;

}

class Group {
  
  public $sectionNo = 0;
  public $courseType = 'Lecture';
  public $instructors = array();
  public $studentsAccepted = 0;
  public $studentsEnrolled = 0;
  public $periods = array();

}

class Course {

  public $name;
  public $code;
  public $lecCredit;
  public $labCredit;

}

class ScheduleScraper {

  public function __construct($html) {
    $this->crawler = new Crawler('<meta charset="utf-8">' . iconv('tis-620', 'utf-8', $html));
  }

  public function scrape() {

    $groups = array();
    $currentGroup = null;
    $self = $this;

    $this->crawler->filter('tr')->each(
      function($tr) use (&$groups, &$currentGroup, $self) {
        $tds = $tr->filter('td');
        if ($tds->count() >= 8) {

          // create new group
          $currentGroup = new Group();
          $groups[] = $currentGroup;

          // tds = [ no., course type, group, date-time, location, instructor,
          //         no of accepted students, no of students enrolled, ... ]
          
          // enter information for group

          $currentGroup->sectionNo = ScheduleScraper::supertrim($tds->eq(2)->text());
          $currentGroup->type = ScheduleScraper::supertrim($tds->eq(1)->text());

		  // Check period
	   	  if(strlen($tds->eq(3)->text()) !== 20 && strlen($tds->eq(4)->text(4)) != 2)
            $currentGroup->periods[] = $self->parsePeriod($tds->eq(3)->text(), ScheduleScraper::supertrim($tds->eq(4)->text()));

		  $currentGroup->studentsAccepted = intval($tds->eq(6)->text());
		  $currentGroup->studentsEnrolled = intval($tds->eq(7)->text());
		  $currentGroup->instructors = explode('<br>', ScheduleScraper::supertrim($tds->eq(5)->html()));


        } else if ($currentGroup !== null) {
          // tds = [ date-time, location ]
		
          $currentGroup->periods[] = $self->parsePeriod($tds->eq(0)->text(), ScheduleScraper::supertrim($tds->eq(1)->text()));
        }
      }
    );
    
    $head = preg_split('~\s{4,}~', trim(str_replace("\xC2\xA0", ' ', $this->crawler->filter('p.head_blue')->text())));
    
    $name = trim(substr($head[0], strpos($head[0], ' ') + 1));
    $code = substr($head[0], 0, 8);
    preg_match('~\d+~', $head[1], $m1);
    preg_match('~\d+~', $head[2], $m2);
    
    $course->sections = $groups;
    $course->lecCredit = intval($m1[0]);
    $course->labCredit = intval($m2[0]);
    $course->name = $name;
    $course->code = $code;

    return $course;

  }

  public function parsePeriod($dateTime, $location) {

    $days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    $dateTime = trim($dateTime);
    $location = trim($location);
    list($date, $time) = explode(' ', $dateTime);
    list($start, $finish) = explode('-', $time);

    $period = new Period();
    $period->day = array_search($date, $days); 
    $period->startTime = $this->parseTime($start);
    $period->finishTime = $this->parseTime($finish);
    $period->location = $location;

    return $period;

  }

  public function parseTime($string) {
    list($hours, $minutes) = explode('.', $string);
    return $hours * 60 + $minutes;
  }

  public static function toJSON($course) {
    $output = array(
      'sections' => array(),
      'lecCredit' => $course->lecCredit,
      'labCredit' => $course->labCredit,
      'courseName' => $course->name,
      'courseId' => $course->code,
    );
    foreach ($course->sections as $sectionObj) {
      $section = array(
        'sectionNo' => $sectionObj->sectionNo,
        'type' => $sectionObj->type == 'Lecture' ? '0' : '1',
        'instructor' => implode(', ', $sectionObj->instructors),
        'studentsAccepted' => $sectionObj->studentsAccepted,
        'studentsEnrolled' => $sectionObj->studentsEnrolled,
        'periods' => array()
      );
      foreach ($sectionObj->periods as $periodObj) {
        $period = array(
          'day' => $periodObj->day,
          'start' => self::minutes_to_str($periodObj->startTime),
          'finish' => self::minutes_to_str($periodObj->finishTime),
          'place' => $periodObj->location
        );
        $section['periods'][] = $period;
      }
      $output['sections'][] = $section;
    }
    return json_encode($output);
  }
  
  private static function minutes_to_str($mins) {
    return sprintf("%02d:%02d", (int)($mins / 60), $mins % 60);
  }
  
  static function supertrim($x) {
    return trim(str_replace("\xC2\xA0", "", $x));
  }
  
}
















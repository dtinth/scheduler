<?php

use Symfony\Component\DomCrawler\Crawler;

require 'vendor/autoload.php';

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

class ScheduleScraper {

  public function __construct($html) {
    $this->crawler = new Crawler('<meta charset="utf-8">' . iconv('tis-620', 'utf-8', $html));
  }

  public function scrape() {

    $groups = array();
    $currentGroup = null;

    $this->crawler->filter('tr')->each(
      function($tr) use (&$groups, &$currentGroup) {
        $tds = $tr->filter('td');
        if ($tds->count() >= 8) {

          // create new group
          $currentGroup = new Group();
          $groups[] = $currentGroup;

          // tds = [ no., course type, group, date-time, location, instructor,
          //         no of accepted students, no of students enrolled, ... ]
          
          // enter information for group
          $currentGroup->sectionNo = $tds->eq(2)->text();

          $currentGroup->periods[] = $this->parsePeriod($tds->eq(3)->text(), $tds->eq(4)->text());

        } else if ($currentGroup !== null) {

          // tds = [ date-time, location ]
          
        }
      }
    );

    return $groups;

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

}
















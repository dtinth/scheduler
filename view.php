<?php

require_once 'backend/includes/schedule_dao.php';
require_once 'backend/includes/facebook_dao.php';
require_once 'backend/includes/json_api.php';

$key = $_GET['id'];
$schedule = ScheduleDao::findByKey($key);

if (!$schedule) {
  throw new Exception("Schedule not found!");
}

$user = null;
if ($schedule['user_id']) {
  $user = FacebookDao::find($schedule['user_id']);
}

?><!doctype html>
<html lang="en" ng-app="scheduler.view">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($schedule['name']) ?><?php if (!empty($user)) { ?> by <?= htmlspecialchars($user['name']) ?><?php } ?> : Scheduler</title>
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/scheduler.css">
  <script src="//use.edgefonts.net/montez.js"></script>
</head>
<body ng-controller="ViewPageController">
  
  <div id="header">
    <h1>Scheduler</h1>
  </div>
  
  <div id="main" class="main-only" ng-controller="ScheduleViewController">
    
    <div id="meta">
      <!--
      <h1>{{scheduleInfo.name}}
      <small ng-if="scheduleInfo.user">by <a href="https://www.facebook.com/profile.php?id={{scheduleInfo.user.uid}}"> {{scheduleInfo.user.name}}</a></small></h1>
      -->
      <h1><?= htmlspecialchars($schedule['name']) ?>
      <?php if (!empty($user)) { ?>
        <small>by <a href="https://www.facebook.com/profile.php?id=<?= htmlspecialchars($user['uid']) ?>"><?= htmlspecialchars($user['name']) ?></a></small>
      <?php } ?></h1>
    </div>
    
    <div class="text-muted section-types">
      <span class="glyphicon glyphicon-book"></span> Lecture &middot; 
      <span class="glyphicon glyphicon-send"></span> Lab
    </div>
    
    <div id="schedule">
      
      <ng-include src="'template/schedule.html'"></ng-include>
      
      <div class="clearfix">
        <?= file_get_contents('template/credits.html'); ?>
        <div class="buttons" ng-show="facebook.loggedIn && facebook.me.id == scheduleInfo.user.uid">
          <a class="btn btn-lg btn-default" href="my.php">
            My Saved Schedules
          </a>
          <button class="btn btn-lg btn-default" ng-click="editThisSchedule()">
            Edit Schedule
          </button>
        </div>
      </div>
      
    </div>
    
    <div class="info-table">
      <ng-include src="'template/info-table.html'"></ng-include>
    </div>
    
  </div>
    
  <script src="vendor/jquery/jquery-2.0.3.min.js"></script>
  <script src="vendor/angular/angular.min.js"></script>
  <script src="vendor/angular-easyfb/angular-easyfb.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  
  <script src="js/view.js"></script>
  <script src="js/facebook.js"></script>
  <script src="vendor/ui-utils/ui-utils.min.js"></script>
  
</body>
</html>
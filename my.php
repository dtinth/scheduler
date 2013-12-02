<?php

require_once 'backend/includes/schedule_dao.php';
require_once 'backend/includes/facebook_dao.php';
require_once 'backend/includes/json_api.php';

$userId = FacebookDao::getUserId();

if ($userId != null) {
  $schedules = DbUtil::select('
    SELECT * FROM schedules
    WHERE user_id = :user_id
    ORDER BY updated_at DESC', array(':user_id' => $userId));
}

?><!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Schedules : Scheduler</title>
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/scheduler.css">
  <script src="//use.edgefonts.net/montez.js"></script>
</head>
<body>
  
  <div id="header">
    <h1>Scheduler</h1>
  </div>
  
  <div id="main" class="main-only">
    <div class="box">
      <h1>My Schedules</h1>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Schedule ID</th>
            <th>Name</th>
            <th>Created</th>
            <th>Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($schedules as $schedule) { ?>
            <tr>
              <td>
                <a href="view.php?id=<?= $schedule['id'] . $schedule['secret'] ?>"><?= $schedule['id'] . $schedule['secret'] ?></a>
              </td>
              <td><?= htmlspecialchars($schedule['name']); ?></td>
              <td><?= htmlspecialchars($schedule['created_at']); ?></td>
              <td><?= htmlspecialchars($schedule['updated_at']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
    
  <script src="vendor/jquery/jquery-2.0.3.min.js"></script>
  
</body>
</html>
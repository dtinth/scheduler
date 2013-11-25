<?php

require 'scraper.php';

$html = file_get_contents('test.html');
$schedule_scraper = new ScheduleScraper($html);
$result = $schedule_scraper->scrape();

?>
<meta charset="utf-8">
<table border="1">
  <?php foreach ($result as $group) { ?>
    <tr>
      <td>Section <?= $group->sectionNo ?></td>
      <td><?= $group->courseType ?></td>
      <td>Instructors: <?= implode(',,', $group->instructors) ?></td>
      <td>
        <?= $group->studentsEnrolled ?> / <?= $group->studentsAccepted ?>
      </td>
    </tr>
    <tr>
      <td colspan="4">
        <ul>
          <?php foreach ($group->periods as $period) { ?>
            <li><?php print_r($period); ?></li>
          <?php } ?>
        </ul>
      </td>
    </tr>
  <?php } ?>
</table>


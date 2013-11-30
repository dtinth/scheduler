<?php

function json_api($fn) {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = json_decode(file_get_contents("php://input"));
  } else {
    $json = null;
  }
  $result = $fn($json);
  header('Content-Type: application/json');
  echo json_encode($result);
}


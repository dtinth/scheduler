<?php

function json_api($fn) {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = json_decode(file_get_contents("php://input"));
  } else {
    $json = null;
  }
  try {
    $result = $fn($json);
  } catch (Exception $e) {
    header('HTTP/1.1 500 Wtf');
    $result = array('error' => $e->getMessage());
  }
  header('Content-Type: application/json');
  echo json_encode($result);
}


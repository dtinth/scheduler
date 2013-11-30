<?php

require_once dirname(__FILE__) . '/facebook.local.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php';

$facebook = new Facebook(array(
  'appId' => '239504039549087',
  'secret' => FACEBOOK_APP_SECRET,
  'fileUpload' => false,
  'allowSignedRequest' => false,
));

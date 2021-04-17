<?php

/*
 *  Build on pojay.dev @42A
 */
require_once "__config.php";
$url = "mysql:host=" . HRMS_DB_HOST . ";dbname=" . HRMS_DB_NAME;
$dbo = new PDO($url, HRMS_DB_USER, HRMS_DB_PASS);

date_default_timezone_set('Asia/Jakarta');

function autoLoad($class) {
  //LOAD DB-DAL
  if (file_exists(MODEL_DIR . 'class.' . strtolower($class) . '.inc.php')) {
    require MODEL_DIR . 'class.' . strtolower($class) . '.inc.php';
  }
  //LOAD UTIL CLASS
  if (file_exists(MODEL_DIR . 'util/class.' . strtolower($class) . '.inc.php')) {
    require MODEL_DIR . 'util/class.' . strtolower($class) . '.inc.php';
  }
  //LOAD EMP MODEL CLASS
  if (file_exists(MODEL_DIR . 'emp/class.' . strtolower($class) . '.inc.php')) {
    require MODEL_DIR . 'emp/class.' . strtolower($class) . '.inc.php';
  }
  //LOAD RECRUITMENTS MODEL CLASS
  if (file_exists(MODEL_DIR . 'rec/class.' . strtolower($class) . '.inc.php')) {
    require MODEL_DIR . 'rec/class.' . strtolower($class) . '.inc.php';
  }
  //LOAD SETTING MODEL CLASS
  if (file_exists(MODEL_DIR . 'setting/class.' . strtolower($class) . '.inc.php')) {
    require MODEL_DIR . 'setting/class.' . strtolower($class) . '.inc.php';
  }
}

spl_autoload_register('autoLoad');
?>
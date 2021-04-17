<?php

class DAL {

  protected $db;

  function __construct($dbo = NULL) {
    if (is_object($dbo)) {
      $this->db = $dbo;
//      var_dump($this->db);
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
      $url = "mysql:host=" . HRMS_DB_HOST . ";dbname=" . HRMS_DB_NAME;
      try {
        $this->db = new PDO($url, HRMS_DB_USER, HRMS_DB_PASS);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (Exception $e) {
        die($e->getMessage());
      }
    }
  }

}

?>

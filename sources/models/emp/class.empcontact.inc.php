<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_contact.inc.php
 *
 * @author mazte
 */
class EmpContact extends DAL {

  public $id;
  public $parentId;
  public $srNama;
  public $srHub;
  public $srPhone;
  public $srAddress;
  public $srProv;
  public $srCity;
  public $brNama;
  public $brHub;
  public $brPhone;
  public $brAddress;
  public $brProv;
  public $brCity;
  public $creBy;
  public $creDate;
  public $updBy;
  public $updDate;

  public function __construct($dbo = NULL) {
    if ($dbo == NULL) {
      parent::__construct($dbo);
    }
  }

  function persist() {
    $sql = "INSERT INTO emp_contact (
      
          parent_id,
sr_nama,
sr_hub,
sr_phone,
sr_address,
sr_prov,
sr_city,
br_nama,
br_hub,
br_phone,
br_address,
br_prov,
br_city,

          
          create_by,
          create_date
          ) VALUES (
          :pParentId, 
:pSrNama, 
:pSrHub, 
:pSrPhone, 
:pSrAddress, 
:pSrProv, 
:pSrCity, 
:pBrNama, 
:pBrHub, 
:pBrPhone, 
:pBrAddress, 
:pBrProv, 
:pBrCity, 

          
          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSrNama', $this->srNama, PDO::PARAM_STR);
      $stmt->bindParam(':pSrHub', $this->srHub, PDO::PARAM_STR);
      $stmt->bindParam(':pSrPhone', $this->srPhone, PDO::PARAM_STR);
      $stmt->bindParam(':pSrAddress', $this->srAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pSrProv', $this->srProv, PDO::PARAM_STR);
      $stmt->bindParam(':pSrCity', $this->srCity, PDO::PARAM_STR);
      $stmt->bindParam(':pBrNama', $this->brNama, PDO::PARAM_STR);
      $stmt->bindParam(':pBrHub', $this->brHub, PDO::PARAM_STR);
      $stmt->bindParam(':pBrPhone', $this->brPhone, PDO::PARAM_STR);
      $stmt->bindParam(':pBrAddress', $this->brAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pBrProv', $this->brProv, PDO::PARAM_STR);
      $stmt->bindParam(':pBrCity', $this->brCity, PDO::PARAM_STR);



      $stmt->bindParam(':pCreBy', $this->creBy, PDO::PARAM_STR);
      $stmt->bindParam(':pCreDate', $this->creDate, PDO::PARAM_STR);

      $stmt->execute();
      $this->id = $this->db->lastInsertId();
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function update() {
    $sql = " UPDATE emp_contact SET


parent_id=:pParentId,
    sr_nama=:pSrNama,
    sr_hub=:pSrHub,
    sr_phone=:pSrPhone,
    sr_address=:pSrAddress,
    sr_prov=:pSrProv,
    sr_city=:pSrCity,
    br_nama=:pBrNama,
    br_hub=:pBrHub,
    br_phone=:pBrPhone,
    br_address=:pBrAddress,
    br_prov=:pBrProv,
    br_city=:pBrCity,


          update_by=:pUpdBy,
          update_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSrNama', $this->srNama, PDO::PARAM_STR);
      $stmt->bindParam(':pSrHub', $this->srHub, PDO::PARAM_STR);
      $stmt->bindParam(':pSrPhone', $this->srPhone, PDO::PARAM_STR);
      $stmt->bindParam(':pSrAddress', $this->srAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pSrProv', $this->srProv, PDO::PARAM_STR);
      $stmt->bindParam(':pSrCity', $this->srCity, PDO::PARAM_STR);
      $stmt->bindParam(':pBrNama', $this->brNama, PDO::PARAM_STR);
      $stmt->bindParam(':pBrHub', $this->brHub, PDO::PARAM_STR);
      $stmt->bindParam(':pBrPhone', $this->brPhone, PDO::PARAM_STR);
      $stmt->bindParam(':pBrAddress', $this->brAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pBrProv', $this->brProv, PDO::PARAM_STR);
      $stmt->bindParam(':pBrCity', $this->brCity, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_contact
				 WHERE id=:pId
				 ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      return 1;
    } catch (Exception $ex) {
      var_dump($ex);
      return 0;
    }
  }

  function getById() {
    $sql = " SELECT 
      
      id,
parent_id,
sr_nama,
sr_hub,
sr_phone,
sr_address,
sr_prov,
sr_city,
br_nama,
br_hub,
br_phone,
br_address,
br_prov,
br_city,


      create_by,
      create_date,
      update_by,
      update_date
      FROM emp_contact 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->srNama = $row["sr_nama"];
        $this->srHub = $row["sr_hub"];
        $this->srPhone = $row["sr_phone"];
        $this->srAddress = $row["sr_address"];
        $this->srProv = $row["sr_prov"];
        $this->srCity = $row["sr_city"];
        $this->brNama = $row["br_nama"];
        $this->brHub = $row["br_hub"];
        $this->brPhone = $row["br_phone"];
        $this->brAddress = $row["br_address"];
        $this->brProv = $row["br_prov"];
        $this->brCity = $row["br_city"];

        $this->creBy = $row["create_by"];
        $this->creDate = $row["create_date"];
        $this->updBy = $row["update_by"];
        $this->updDate = $row["update_date"];
      }
      $stmt->closeCursor();
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getByParentId() {
    $sql = " SELECT 
      
      id,
parent_id,
sr_nama,
sr_hub,
sr_phone,
sr_address,
sr_prov,
sr_city,
br_nama,
br_hub,
br_phone,
br_address,
br_prov,
br_city,

      create_by,
      create_date,
      update_by,
      update_date
      FROM emp_contact 
      WHERE parent_id=:pParentId
      limit 1";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->srNama = $row["sr_nama"];
        $this->srHub = $row["sr_hub"];
        $this->srPhone = $row["sr_phone"];
        $this->srAddress = $row["sr_address"];
        $this->srProv = $row["sr_prov"];
        $this->srCity = $row["sr_city"];
        $this->brNama = $row["br_nama"];
        $this->brHub = $row["br_hub"];
        $this->brPhone = $row["br_phone"];
        $this->brAddress = $row["br_address"];
        $this->brProv = $row["br_prov"];
        $this->brCity = $row["br_city"];


        $this->creBy = $row["create_by"];
        $this->creDate = $row["create_date"];
        $this->updBy = $row["update_by"];
        $this->updDate = $row["update_date"];
      }
      $stmt->closeCursor();
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAll() {
    $sql = " SELECT 

      create_by,
      create_date,
      update_by,
      update_date
      FROM emp_contact ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpContact("C");

        $ret0->creBy = $row["create_by"];
        $ret0->creDate = $row["create_date"];
        $ret0->updBy = $row["update_by"];
        $ret0->updDate = $row["update_date"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function loadTable() {
    $sql = " SELECT
      
      create_by creBy,
      create_date creDate,
      update_by updBy,
      update_date updDate
      FROM emp_contact 
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return json_encode(array("sEcho" => 1, "aaData" => $result));
  }

  private function populateWithPost() {
    foreach ($_POST as $var => $value) {
      if (property_exists($this, $var)) {
        if (!is_array($value)) {
          $this->$var = htmlentities($value);
          if ((strpos($var, "Date") > -1 || strpos(strtolower($var), "tanggal") > -1) && ($value == "" || $value == "0000-00-00")) {
            $this->$var = null;
          } else if ($value == "" || $value == "0000-00-00 00:00:00" || $value == "0000-00-00") {
            $this->$var = null;
          }
        } else {
          $this->$var = implode(";", $value);
        }
      }
    }
    return $this;
  }

  function processForm() {
    $this->id = $_SESSION["entity_id"];
    $this->getById();
    $this->populateWithPost();
    $this->parentId = $_SESSION["curr_emp_id"];
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
//    var_dump($this);
//    die();
    return $this;
  }

}

?>
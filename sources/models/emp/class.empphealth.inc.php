<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_health.inc.php
 *
 * @author mazte
 */
class EmpPhealth extends DAL {

  public $id;
  public $parentId;  
  public $hltDate;
  public $hltPlace;
  public $hltDoctor;
  public $hltNote;
  public $hltRemark;  
  public $hltFilename;  
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
    $sql = "INSERT INTO emp_health (
      
parent_id,
hlt_date,
hlt_place,
hlt_doctor,
hlt_note,
hlt_remark,
hlt_filename,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pHltDate, 
:pHltPlace, 
:pHltDoctor, 
:pHltNote, 
:pHltRemark, 
:pHltFilename, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->hltDate = setTanggal($this->hltDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);      
      $stmt->bindParam(':pHltDate', $this->hltDate, PDO::PARAM_STR);
      $stmt->bindParam(':pHltPlace', $this->hltPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pHltDoctor', $this->hltDoctor, PDO::PARAM_STR);
      $stmt->bindParam(':pHltNote', $this->hltNote, PDO::PARAM_STR);
      $stmt->bindParam(':pHltRemark', $this->hltRemark, PDO::PARAM_STR);      
      $stmt->bindParam(':pHltFilename', $this->hltFilename, PDO::PARAM_STR);      

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
    $sql = " UPDATE emp_health SET

parent_id=:pParentId,
hlt_date=:pHltDate,
hlt_place=:pHltPlace,
hlt_doctor=:pHltDoctor,
hlt_note=:pHltNote,
hlt_remark=:pHltRemark,
hlt_filename=:pHltFilename,


          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $this->hltDate = setTanggal($this->hltDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);      
      $stmt->bindParam(':pHltDate', $this->hltDate, PDO::PARAM_STR);
      $stmt->bindParam(':pHltPlace', $this->hltPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pHltDoctor', $this->hltDoctor, PDO::PARAM_STR);
      $stmt->bindParam(':pHltNote', $this->hltNote, PDO::PARAM_STR);
      $stmt->bindParam(':pHltRemark', $this->hltRemark, PDO::PARAM_STR);      
      $stmt->bindParam(':pHltFilename', $this->hltFilename, PDO::PARAM_STR);      


      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_health
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
hlt_date,
hlt_place,
hlt_doctor,
hlt_note,
hlt_remark,
hlt_filename,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_health 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];        
        $this->hltDate = $row["hlt_date"];
        $this->hltPlace = $row["hlt_place"];
        $this->hltDoctor = $row["hlt_doctor"];
        $this->hltNote = $row["hlt_note"];
        $this->hltRemark = $row["hlt_remark"];        
        $this->hltFilename = $row["hlt_filename"];

        $this->creBy = $row["cre_by"];
        $this->creDate = $row["cre_date"];
        $this->updBy = $row["upd_by"];
        $this->updDate = $row["upd_date"];
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
hlt_date,
hlt_place,
hlt_doctor,
hlt_note,
hlt_remark,
hlt_filename,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_health 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPhealth("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];        
        $ret0->hltDate = $row["hlt_date"];
        $ret0->hltPlace = $row["hlt_place"];
        $ret0->hltDoctor = $row["hlt_doctor"];
        $ret0->hltNote = $row["hlt_note"];
        $ret0->hltRemark = $row["hlt_remark"];        
        $ret0->hltFilename = $row["hlt_filename"];        

        $ret0->creBy = $row["cre_by"];
        $ret0->creDate = $row["cre_date"];
        $ret0->updBy = $row["upd_by"];
        $ret0->updDate = $row["upd_date"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAll() {
    $sql = " SELECT 

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_health ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPhealth("C");

        $ret0->creBy = $row["cre_by"];
        $ret0->creDate = $row["cre_date"];
        $ret0->updBy = $row["upd_by"];
        $ret0->updDate = $row["upd_date"];
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

t1.id id,
t1.parent_id parentId,
DATE_FORMAT(t1.hlt_date, '%d/%m/%Y') hltDate,
t1.hlt_place hltPlace,
t1.hlt_doctor hltDoctor,
t1.hlt_note hltNote,
t1.hlt_remark hltRemark,
t1.hlt_filename hltFilename,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_health t1      
      WHERE parent_id=:pParentId
      
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
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
//          else if ((strpos(strtolower($var), "date") > -1 || strpos(strtolower($var), "tanggal") > -1) && ($value != "")) {
//            echo "VAR: " . $this->$var . " ; val: " . $value . "<br/>";
//            $temp = DateTime::createFromFormat("d/m/Y", $value);
//            echo "TMP: " . $temp->format("Y-m-d") . "<br/>";
//            $this->$var = $temp->format("Y-m-d");
//            echo "VAR: " . $this->$var . " ; val: " . $value . "<br/>";
//          }
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
    $this->hltNo = strtoupper($this->hltNo);
    $this->parentId = $_SESSION["curr_emp_id"];
    //HANDLE FILE UPLOAD
    $fileKtpTmp = $_FILES["hltFilename"]["tmp_name"];
    $fileKtpName = $_FILES["hltFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "hlt" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "hlt_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->hltFilename = $fktpName;
      }
    }
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
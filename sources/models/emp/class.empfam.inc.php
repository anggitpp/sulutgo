<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_family.inc.php
 *
 * @author mazte
 */
class EmpFam extends DAL {

  public $id;
  public $parentId;
  public $name;
  public $rel;
  public $birthPlace;
  public $birthDate;
  public $gender;
  public $relFilename;
  public $remark;
  public $status;
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
    $sql = "INSERT INTO emp_family (
      
parent_id,
name,
rel,
birth_place,
birth_date,
gender,
rel_filename,
remark,
status,
          
          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pName, 
:pRel, 
:pBirthPlace, 
:pBirthDate,
:pGender, 
:pRelFilename, 
:pRemark, 
:pStatus, 
          
          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->birthDate = setTanggal($this->birthDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pName', $this->name, PDO::PARAM_STR);
      $stmt->bindParam(':pRel', $this->rel, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthPlace', $this->birthPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthDate', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindParam(':pGender', $this->gender, PDO::PARAM_STR);
      $stmt->bindParam(':pRelFilename', $this->relFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);


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
    $sql = " UPDATE emp_family SET

parent_id=:pParentId,
name=:pName,
rel=:pRel,
birth_place=:pBirthPlace,
birth_date=:pBirthDate,
gender=:pGender,
rel_filename=:pRelFilename,
remark=:pRemark,
status=:pStatus,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $this->birthDate = setTanggal($this->birthDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pName', $this->name, PDO::PARAM_STR);
      $stmt->bindParam(':pRel', $this->rel, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthPlace', $this->birthPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthDate', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindParam(':pGender', $this->gender, PDO::PARAM_STR);
      $stmt->bindParam(':pRelFilename', $this->relFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_family
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
name,
rel,
birth_place,
birth_date,
gender,
rel_filename,
remark,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_family 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->name = $row["name"];
        $this->rel = $row["rel"];
        $this->birthPlace = $row["birth_place"];
        $this->birthDate = $row["birth_date"];
        $this->gender = $row["gender"];
        $this->relFilename = $row["rel_filename"];
        $this->remark = $row["remark"];
        $this->status = $row["status"];
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
name,
rel,
birth_place,
birth_date,
gender,
rel_filename,
remark,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_family 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpFam("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->name = $row["name"];
        $ret0->rel = $row["rel"];
        $ret0->birthPlace = $row["birth_place"];
        $ret0->birthDate = $row["birth_date"];
        $ret0->gender = $row["gender"];
        $ret0->relFilename = $row["rel_filename"];
        $ret0->remark = $row["remark"];
        $ret0->status = $row["status"];

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
      FROM emp_family ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpFam("C");

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
t1.name name,
t1.rel rel,
t1.birth_place birthPlace,

DATE_FORMAT(t1.birth_date, '%d/%m/%Y') birthDate,
t1.rel_filename relFilename,
t1.remark remark,
t1.status status,
t2.namaData relName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_family t1
      JOIN mst_data t2 on t1.rel=t2.kodeData
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
    //HANDLE FILE UPLOAD KTP
    $fileKtpTmp = $_FILES["relFilename"]["tmp_name"];
    $fileKtpName = $_FILES["relFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "rel" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "rel_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->relFilename = $fktpName;
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
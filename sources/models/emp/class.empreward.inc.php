<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_reward.inc.php
 *
 * @author mazte
 */
class EmpReward extends DAL {

  public $id;
  public $parentId;
  public $rwdNo;
  public $rwdSubject;
  public $rwdDate;
  public $rwdAgency;
  public $rwdYear;
  public $rwdCat;
  public $rwdType;
  public $rwdFilename;
  public $remark;
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
    $sql = "INSERT INTO emp_reward (
      
parent_id,
rwd_no,
rwd_subject,
rwd_date,
rwd_agency,
rwd_year,
rwd_cat,
rwd_type,
rwd_filename,
remark,


          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pRwdNo, 
:pRwdSubject, 
:pRwdDate, 
:pRwdAgency, 
:pRwdYear, 
:pRwdCat, 
:pRwdType, 
:pRwdFilename, 
:pRemark, 


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
      $stmt->bindParam(':pRwdNo', $this->rwdNo, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdSubject', $this->rwdSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdDate', $this->rwdDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdAgency', $this->rwdAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdYear', $this->rwdYear, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdCat', $this->rwdCat, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdType', $this->rwdType, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdFilename', $this->rwdFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);


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
    $sql = " UPDATE emp_reward SET

parent_id=:pParentId,
rwd_no=:pRwdNo,
rwd_subject=:pRwdSubject,
rwd_date=:pRwdDate,
rwd_agency=:pRwdAgency,
rwd_year=:pRwdYear,
rwd_cat=:pRwdCat,
rwd_type=:pRwdType,
rwd_filename=:pRwdFilename,
remark=:pRemark,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdNo', $this->rwdNo, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdSubject', $this->rwdSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdDate', $this->rwdDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdAgency', $this->rwdAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdYear', $this->rwdYear, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdCat', $this->rwdCat, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdType', $this->rwdType, PDO::PARAM_STR);
      $stmt->bindParam(':pRwdFilename', $this->rwdFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);


      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_reward
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
rwd_no,
rwd_subject,
rwd_date,
rwd_agency,
rwd_year,
rwd_cat,
rwd_type,
rwd_filename,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_reward 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->rwdNo = $row["rwd_no"];
        $this->rwdSubject = $row["rwd_subject"];
        $this->rwdDate = $row["rwd_date"];
        $this->rwdAgency = $row["rwd_agency"];
        $this->rwdYear = $row["rwd_year"];
        $this->rwdCat = $row["rwd_cat"];
        $this->rwdType = $row["rwd_type"];
        $this->rwdFilename = $row["rwd_filename"];
        $this->remark = $row["remark"];

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
rwd_no,
rwd_subject,
rwd_date,
rwd_agency,
rwd_year,
rwd_cat,
rwd_type,
rwd_filename,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_reward 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->rwdNo = $row["rwd_no"];
        $ret0->rwdSubject = $row["rwd_subject"];
        $ret0->rwdDate = $row["rwd_date"];
        $ret0->rwdAgency = $row["rwd_agency"];
        $ret0->rwdYear = $row["rwd_year"];
        $ret0->rwdCat = $row["rwd_cat"];
        $ret0->rwdType = $row["rwd_type"];
        $ret0->rwdFilename = $row["rwd_filename"];
        $ret0->remark = $row["remark"];

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
      FROM emp_reward ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpReward("C");

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
      t1.rwd_no rwdNo,
      t1.rwd_subject rwdSubject,
      t1.rwd_date rwdDate,
      t1.rwd_agency rwdAgency,
      t1.rwd_year rwdYear,
      t1.rwd_cat rwdCat,
      t1.rwd_type rwdType,
      t1.rwd_filename rwdFilename,
      t1.remark remark,

      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_reward t1
      JOIN mst_data t2 on t1.rwd_cat=t2.kodeData
      JOIN mst_data t3 on t1.rwd_type=t3.kodeData
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
    $this->rwdNo = strtoupper($this->rwdNo);
    $this->parentId = $_SESSION["curr_emp_id"];
    //HANDLE FILE UPLOAD
    $fileKtpTmp = $_FILES["rwdFilename"]["tmp_name"];
    $fileKtpName = $_FILES["rwdFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "rwd" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "reward_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->rwdFilename = $fktpName;
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
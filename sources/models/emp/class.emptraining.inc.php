<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_training.inc.php
 *
 * @author mazte
 */
class EmpTraining extends DAL {

  public $id;
  public $parentId;
  public $trnNo;
  public $trnSubject;
  public $trnDate;
  public $trnAgency;
  public $trnYear;
  public $trnCat;
  public $trnType;
  public $trnDateStart;
  public $trnDateEnd;
  public $trnLoc;
  public $trnFilename;
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
    $sql = "INSERT INTO emp_training (
      
parent_id,
trn_no,
trn_subject,
trn_date,
trn_agency,
trn_year,
trn_cat,
trn_type,
trn_date_start,
trn_date_end,
trn_loc,
trn_filename,
remark,
status,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pTrnNo, 
:pTrnSubject, 
:pTrnDate, 
:pTrnAgency, 
:pTrnYear, 
:pTrnCat, 
:pTrnType, 
:pTrnDateStart, 
:pTrnDateEnd, 
:pTrnLoc, 
:pTrnFilename, 
:pRemark, 
:pStatus, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->trnDateStart = setTanggal($this->trnDateStart);
      $this->trnDateEnd = setTanggal($this->trnDateEnd);
      $this->creDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnNo', $this->trnNo, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnSubject', $this->trnSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDate', $this->trnDate, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnAgency', $this->trnAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnYear', $this->trnYear, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnCat', $this->trnCat, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnType', $this->trnType, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDateStart', $this->trnDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDateEnd', $this->trnDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnLoc', $this->trnLoc, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnFilename', $this->trnFilename, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_training SET

parent_id=:pParentId,
trn_no=:pTrnNo,
trn_subject=:pTrnSubject,
trn_date=:pTrnDate,
trn_agency=:pTrnAgency,
trn_year=:pTrnYear,
trn_cat=:pTrnCat,
trn_type=:pTrnType,
trn_date_start=:pTrnDateStart,
trn_date_end=:pTrnDateEnd,
trn_loc=:pTrnLoc,
trn_filename=:pTrnFilename,
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
      $this->trnDateStart = setTanggal($this->trnDateStart);
      $this->trnDateEnd = setTanggal($this->trnDateEnd);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnNo', $this->trnNo, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnSubject', $this->trnSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDate', $this->trnDate, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnAgency', $this->trnAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnYear', $this->trnYear, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnCat', $this->trnCat, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnType', $this->trnType, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDateStart', $this->trnDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnDateEnd', $this->trnDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnLoc', $this->trnLoc, PDO::PARAM_STR);
      $stmt->bindParam(':pTrnFilename', $this->trnFilename, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_training
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
trn_no,
trn_subject,
trn_date,
trn_agency,
trn_year,
trn_cat,
trn_type,
trn_date_start,
trn_date_end,
trn_loc,
trn_filename,
remark,
status,



      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_training 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->trnNo = $row["trn_no"];
        $this->trnSubject = $row["trn_subject"];
        $this->trnDate = $row["trn_date"];
        $this->trnAgency = $row["trn_agency"];
        $this->trnYear = $row["trn_year"];
        $this->trnCat = $row["trn_cat"];
        $this->trnType = $row["trn_type"];
        $this->trnDateStart = $row["trn_date_start"];
        $this->trnDateEnd = $row["trn_date_end"];
        $this->trnLoc = $row["trn_loc"];
        $this->trnFilename = $row["trn_filename"];
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
trn_no,
trn_subject,
trn_date,
trn_agency,
trn_year,
trn_cat,
trn_type,
trn_date_start,
trn_date_end,
trn_loc,
trn_filename,
remark,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_training 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpTraining("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->trnNo = $row["trn_no"];
        $ret0->trnSubject = $row["trn_subject"];
        $ret0->trnDate = $row["trn_date"];
        $ret0->trnAgency = $row["trn_agency"];
        $ret0->trnYear = $row["trn_year"];
        $ret0->trnCat = $row["trn_cat"];
        $ret0->trnType = $row["trn_type"];
        $ret0->trnDateStart = $row["trn_date_start"];
        $ret0->trnDateEnd = $row["trn_date_end"];
        $ret0->trnLoc = $row["trn_loc"];
        $ret0->trnFilename = $row["trn_filename"];
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
      FROM emp_training ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpTraining("C");

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
      t1.trn_no trnNo,
      t1.trn_subject trnSubject,
      t1.trn_date trnDate,
      t1.trn_agency trnAgency,
      t1.trn_year trnYear,
      t1.trn_cat trnCat,
      t1.trn_type trnType,

      DATE_FORMAT(t1.trn_date_start, '%d/%m/%Y') trnDateStart,
      DATE_FORMAT(t1.trn_date_end, '%d/%m/%Y') trnDateEnd,
      t1.trn_loc trnLoc,
      t1.trn_filename trnFilename,
      t1.remark remark,
      t1.status status,

      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_training t1
      JOIN mst_data t2 on t1.trn_cat=t2.kodeData
      JOIN mst_data t3 on t1.trn_type=t3.kodeData
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
    $this->trnNo = strtoupper($this->trnNo);
    $this->parentId = $_SESSION["curr_emp_id"];
    //HANDLE FILE UPLOAD
    $fileKtpTmp = $_FILES["trnFilename"]["tmp_name"];
    $fileKtpName = $_FILES["trnFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "trn" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "trn_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->trnFilename = $fktpName;
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
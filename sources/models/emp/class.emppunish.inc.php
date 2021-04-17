<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_punish.inc.php
 *
 * @author mazte
 */
class EmpPunish extends DAL {

  public $id;
  public $parentId;
  public $pnhNo;
  public $pnhSubject;
  public $pnhDate;
  public $pnhAgency;
  public $pnhYear;
  public $pnhType;
  public $pnhDateStart;
  public $pnhDateEnd;
  public $pnhImpl;
  public $pnhFilename;
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
    $sql = "INSERT INTO emp_punish (
      
parent_id,
pnh_no,
pnh_subject,
pnh_date,
pnh_agency,
pnh_year,
pnh_type,
pnh_date_start,
pnh_date_end,
pnh_impl,
pnh_filename,
remark,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPnhNo, 
:pPnhSubject, 
:pPnhDate, 
:pPnhAgency, 
:pPnhYear, 
:pPnhType, 
:pPnhDateStart, 
:pPnhDateEnd, 
:pPnhImpl, 
:pPnhFilename, 
:pRemark, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->pnhDateStart = setTanggal($this->pnhDateStart);
      $this->pnhDateEnd = setTanggal($this->pnhDateEnd);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhNo', $this->pnhNo, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhSubject', $this->pnhSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDate', $this->pnhDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhAgency', $this->pnhAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhYear', $this->pnhYear, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhType', $this->pnhType, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDateStart', $this->pnhDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDateEnd', $this->pnhDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhImpl', $this->pnhImpl, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhFilename', $this->pnhFilename, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_punish SET

parent_id=:pParentId,
pnh_no=:pPnhNo,
pnh_subject=:pPnhSubject,
pnh_date=:pPnhDate,
pnh_agency=:pPnhAgency,
pnh_year=:pPnhYear,
pnh_type=:pPnhType,
pnh_date_start=:pPnhDateStart,
pnh_date_end=:pPnhDateEnd,
pnh_impl=:pPnhImpl,
pnh_filename=:pPnhFilename,
remark=:pRemark,


          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->pnhDateStart = setTanggal($this->pnhDateStart);
      $this->pnhDateEnd = setTanggal($this->pnhDateEnd);
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhNo', $this->pnhNo, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhSubject', $this->pnhSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDate', $this->pnhDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhAgency', $this->pnhAgency, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhYear', $this->pnhYear, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhType', $this->pnhType, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDateStart', $this->pnhDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhDateEnd', $this->pnhDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhImpl', $this->pnhImpl, PDO::PARAM_STR);
      $stmt->bindParam(':pPnhFilename', $this->pnhFilename, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_punish
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
pnh_no,
pnh_subject,
pnh_date,
pnh_agency,
pnh_year,
pnh_type,
pnh_date_start,
pnh_date_end,
pnh_impl,
pnh_filename,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_punish 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->pnhNo = $row["pnh_no"];
        $this->pnhSubject = $row["pnh_subject"];
        $this->pnhDate = $row["pnh_date"];
        $this->pnhAgency = $row["pnh_agency"];
        $this->pnhYear = $row["pnh_year"];
        $this->pnhType = $row["pnh_type"];
        $this->pnhDateStart = $row["pnh_date_start"];
        $this->pnhDateEnd = $row["pnh_date_end"];
        $this->pnhImpl = $row["pnh_impl"];
        $this->pnhFilename = $row["pnh_filename"];
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
pnh_no,
pnh_subject,
pnh_date,
pnh_agency,
pnh_year,
pnh_type,
pnh_date_start,
pnh_date_end,
pnh_impl,
pnh_filename,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_punish 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPunish("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->pnhNo = $row["pnh_no"];
        $ret0->pnhSubject = $row["pnh_subject"];
        $ret0->pnhDate = $row["pnh_date"];
        $ret0->pnhAgency = $row["pnh_agency"];
        $ret0->pnhYear = $row["pnh_year"];
        $ret0->pnhType = $row["pnh_type"];
        $ret0->pnhDateStart = $row["pnh_date_start"];
        $ret0->pnhDateEnd = $row["pnh_date_end"];
        $ret0->pnhImpl = $row["pnh_impl"];
        $ret0->pnhFilename = $row["pnh_filename"];
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
      FROM emp_punish ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPunish("C");

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
t1.pnh_no pnhNo,
t1.pnh_subject pnhSubject,
t1.pnh_date pnhDate,
t1.pnh_agency pnhAgency,
t1.pnh_year pnhYear,
t1.pnh_type pnhType,
t1.pnh_date_start pnhDateStart,
t1.pnh_date_end pnhDateEnd,
t1.pnh_impl pnhImpl,
t1.pnh_filename pnhFilename,
t1.remark remark,


      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_punish t1
      JOIN mst_data t3 on t1.pnh_type=t3.kodeData
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
    $this->pnhNo = strtoupper($this->pnhNo);
    $this->parentId = $_SESSION["curr_emp_id"];
    //HANDLE FILE UPLOAD
    $fileKtpTmp = $_FILES["pnhFilename"]["tmp_name"];
    $fileKtpName = $_FILES["pnhFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "pnh" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "pnh_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->pnhFilename = $fktpName;
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
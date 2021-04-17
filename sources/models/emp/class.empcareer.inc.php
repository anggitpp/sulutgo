<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_career.inc.php
 *
 * @author mazte
 */
class EmpCareer extends DAL {

  public $id;
  public $parentId;
  public $skNo;
  public $skSubject;
  public $skDate;
  public $skCat;
  public $skType;
  public $skDateStart;
  public $skDateEnd;
  public $skFilename;
  public $remark;
  public $status;
  public $chPos;
  public $posName;
  public $dirId;
  public $divId;
  public $deptId;
  public $unitId;
  public $posRemark;
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
    $sql = "INSERT INTO emp_career (
      
parent_id,
sk_no,
sk_subject,
sk_date,
sk_cat,
sk_type,
sk_date_start,
sk_date_end,
sk_filename,
remark,
status,
ch_pos,
pos_name,
dir_id,
div_id,
dept_id,
unit_id,
pos_remark,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pSkNo, 
:pSkSubject, 
:pSkDate, 
:pSkCat, 
:pSkType, 
:pSkDateStart, 
:pSkDateEnd, 
:pSkFilename, 
:pRemark, 
:pStatus, 
:pChPos, 
:pPosName, 
:pDirId, 
:pDivId, 
:pDeptId, 
:pUnitId, 
:pPosRemark, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->skDate = setTanggal($this->skDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkSubject', $this->skSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSkCat', $this->skCat, PDO::PARAM_STR);
      $stmt->bindParam(':pSkType', $this->skType, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDateStart', $this->skDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDateEnd', $this->skDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pSkFilename', $this->skFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pChPos', $this->chPos, PDO::PARAM_STR);
      $stmt->bindParam(':pPosName', $this->posName, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
      $stmt->bindParam(':pPosRemark', $this->posRemark, PDO::PARAM_STR);

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
    $sql = " UPDATE emp_career SET

parent_id=:pParentId,
sk_no=:pSkNo,
sk_subject=:pSkSubject,
sk_date=:pSkDate,
sk_cat=:pSkCat,
sk_type=:pSkType,
sk_date_start=:pSkDateStart,
sk_date_end=:pSkDateEnd,
sk_filename=:pSkFilename,
remark=:pRemark,
status=:pStatus,
ch_pos=:pChPos,
pos_name=:pPosName,
dir_id=:pDirId,
div_id=:pDivId,
dept_id=:pDeptId,
unit_id=:pUnitId,
pos_remark=:pPosRemark,


          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $this->skDate = setTanggal($this->skDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkSubject', $this->skSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSkCat', $this->skCat, PDO::PARAM_STR);
      $stmt->bindParam(':pSkType', $this->skType, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDateStart', $this->skDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDateEnd', $this->skDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pSkFilename', $this->skFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pChPos', $this->chPos, PDO::PARAM_STR);
      $stmt->bindParam(':pPosName', $this->posName, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
      $stmt->bindParam(':pPosRemark', $this->posRemark, PDO::PARAM_STR);


      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_career
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
sk_no,
sk_subject,
sk_date,
sk_cat,
sk_type,
sk_date_start,
sk_date_end,
sk_filename,
remark,
status,
ch_pos,
pos_name,
dir_id,
div_id,
dept_id,
unit_id,
pos_remark,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_career 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->skNo = $row["sk_no"];
        $this->skSubject = $row["sk_subject"];
        $this->skDate = $row["sk_date"];
        $this->skCat = $row["sk_cat"];
        $this->skType = $row["sk_type"];
        $this->skDateStart = $row["sk_date_start"];
        $this->skDateEnd = $row["sk_date_end"];
        $this->skFilename = $row["sk_filename"];
        $this->remark = $row["remark"];
        $this->status = $row["status"];
        $this->chPos = $row["ch_pos"];
        $this->posName = $row["pos_name"];
        $this->dirId = $row["dir_id"];
        $this->divId = $row["div_id"];
        $this->deptId = $row["dept_id"];
        $this->unitId = $row["unit_id"];
        $this->posRemark = $row["pos_remark"];


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
sk_no,
sk_subject,
sk_date,
sk_cat,
sk_type,
sk_date_start,
sk_date_end,
sk_filename,
remark,
status,
ch_pos,
pos_name,
dir_id,
div_id,
dept_id,
unit_id,
pos_remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_career 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpCareer("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->skNo = $row["sk_no"];
        $ret0->skSubject = $row["sk_subject"];
        $ret0->skDate = $row["sk_date"];
        $ret0->skCat = $row["sk_cat"];
        $ret0->skType = $row["sk_type"];
        $ret0->skDateStart = $row["sk_date_start"];
        $ret0->skDateEnd = $row["sk_date_end"];
        $ret0->skFilename = $row["sk_filename"];
        $ret0->remark = $row["remark"];
        $ret0->status = $row["status"];
        $ret0->chPos = $row["ch_pos"];
        $ret0->posName = $row["pos_name"];
        $ret0->dirId = $row["dir_id"];
        $ret0->divId = $row["div_id"];
        $ret0->deptId = $row["dept_id"];
        $ret0->unitId = $row["unit_id"];
        $ret0->posRemark = $row["pos_remark"];


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
      FROM emp_career ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpCareer("C");

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
      t1.sk_no skNo,
      t1.sk_subject skSubject,
      DATE_FORMAT(t1.sk_date, '%d/%m/%Y') skDate,
      t1.sk_cat skCat,
      t1.sk_type skType,
      t1.sk_date_start skDateStart,
      t1.sk_date_end skDateEnd,
      t1.sk_filename skFilename,
      t1.remark remark,
      t1.status status,
      t1.ch_pos chPos,
      t1.pos_name posName,
      t1.dir_id dirId,
      t1.div_id divId,
      t1.dept_id deptId,
      t1.unit_id unitId,
      t1.pos_remark posRemark,
      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_career t1
      LEFT JOIN mst_data t2 on t1.sk_cat=t2.kodeData
      LEFT JOIN mst_data t3 on t1.sk_type=t3.kodeData
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
    $this->skNo = strtoupper($this->skNo);
    $this->posName = strtoupper($this->posName);
    $this->parentId = $_SESSION["curr_emp_id"];
    //HANDLE FILE UPLOAD KTP
    $fileKtpTmp = $_FILES["skFilename"]["tmp_name"];
    $fileKtpName = $_FILES["skFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "career" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "career_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->skFilename = $fktpName;
      }
    }
    if ($this->id == "") {
      $this->persist();
      if ($this->chPos == "1") {
        $cutil = new Common();
        $emph = new EmpPhist();
        $emph->parentId = $this->parentId;
        $emph->posName = $this->posName;
        $emph->skNo = $this->skNo;
        $emph->skDate = $this->skDate;
        $emph->startDate = $this->skDateStart;
        $emph->endDate = $this->skDateEnd;
        $emph->dirId = $this->dirId;
        $emph->divId = $this->divId;
        $emph->deptId = $this->deptId;
        $emph->unitId = $this->unitId;
        $emph->remark = $this->posRemark;
        $emph->status = 1;
        $cutil->execute("UPDATE emp_phist SET status=0 WHERE parent_id='$this->parentId'");
        $emph->persist();
      }
    } else {
      $this->update();
    }
//    die();
    return $this;
  }

}

?>
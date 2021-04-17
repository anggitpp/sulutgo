<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emppcontract.inc
 *
 * @author mazte
 */
class EmpPcontract extends DAL {

  public $id;
  public $parentId;
  public $subject;
  public $skNo;
  public $skDate;
  public $startDate;
  public $endDate;
  public $remark;
  public $fileSk;
  public $loc1;
  public $loc2;
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
    $sql = "INSERT INTO emp_pcontract (
		
parent_id,
sk_no,
sk_date,
subject,
start_date,
end_date,
remark,
file_sk,
loc1,
loc2,
status,

          cre_by,
          cre_date
          ) VALUES (
:pParentId, 
:pSkNo, 
:pSkDate, 
:pSubject, 
:pStartDate, 
:pEndDate, 
:pRemark, 
:pFileSk, 
:pLoc1, 
:pLoc2, 
:pStatus, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
         $this->skDate = setTanggal($this->skDate);
      $this->startDate = setTanggal($this->startDate);
      $this->endDate = setTanggal($this->endDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileSk', $this->fileSk, PDO::PARAM_STR);
      $stmt->bindParam(':pLoc1', $this->loc1, PDO::PARAM_STR);
      $stmt->bindParam(':pLoc2', $this->loc2, PDO::PARAM_STR);
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

  function updateLast(){
    global $db,$cUsername;
    $cek = getField("select id from emp_pcontract where parent_id = '".$this->parentId."'");
    if($cek){
      $sql = "update emp_pcontract set status = '0' where parent_id = '".$this->parentId."'";
      db($sql);
    }
  }

  function update() {
    $sql = " UPDATE emp_pcontract SET
parent_id=:pParentId,
sk_no=:pSkNo,
sk_date=:pSkDate,
subject=:pSubject,
start_date=:pStartDate,
end_date=:pEndDate,
remark=:pRemark,
file_sk=:pFileSk,
loc1=:pLoc1,
loc2=:pLoc2,
status=:pStatus,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->skDate = setTanggal($this->skDate);
      $this->startDate = setTanggal($this->startDate);
      $this->endDate = setTanggal($this->endDate);
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileSk', $this->fileSk, PDO::PARAM_STR);
      $stmt->bindParam(':pLoc1', $this->loc1, PDO::PARAM_STR);
      $stmt->bindParam(':pLoc2', $this->loc2, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_pcontract
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
sk_date,
subject,
start_date,
end_date,
remark,
file_sk,
loc1,
loc2,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_pcontract 
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
        $this->skDate = $row["sk_date"];
        $this->startDate = $row["start_date"];
        $this->subject = $row["subject"];
        $this->endDate = $row["end_date"];
        $this->remark = $row["remark"];
        $this->fileSk = $row["file_sk"];
        $this->loc1 = $row["loc1"];
        $this->loc2 = $row["loc2"];
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

  function getAll() {
    $sql = " SELECT 
id,
parent_id,
sk_no,
sk_date,
start_date,
subject,
end_date,
remark,
file_sk,
loc1,
loc2,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_pcontract ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPcontract("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->skNo = $row["sk_no"];
        $ret0->skDate = $row["sk_date"];
        $ret0->subject = $row["subject"];
        $ret0->startDate = $row["start_date"];
        $ret0->endDate = $row["end_date"];
        $ret0->remark = $row["remark"];
        $ret0->fileSk = $row["file_sk"];
        $ret0->loc1 = $row["loc1"];
        $ret0->loc2 = $row["loc2"];
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

  function loadTable() {
    $sql = " SELECT

t1.id id,
t1.parent_id parentId,
t1.sk_no skNo,

t1.subject subject,
DATE_FORMAT(t1.start_date, '%d/%m/%Y') startDate,
DATE_FORMAT(t1.end_date, '%d/%m/%Y') endDate,
DATE_FORMAT(t1.sk_date, '%d/%m/%Y') skDate,
t1.remark remark,
t1.file_sk fileSk,
t1.loc1 loc1,
t1.loc2 loc2,
t1.status status,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_pcontract t1 
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

  function loadTableByParent() {
    $sql = " SELECT

t1.id id,
t1.parent_id parentId,
t1.sk_no skNo,
t1.subject subject,
DATE_FORMAT(t1.start_date, '%d/%m/%Y') startDate,
DATE_FORMAT(t1.end_date, '%d/%m/%Y') endDate,
DATE_FORMAT(t1.sk_date, '%d/%m/%Y') skDate,
t1.remark remark,
t1.file_sk fileSk,
t1.loc1 loc1,
t1.loc2 loc2,
t1.status status,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_pcontract t1 
      WHERE t1.parent_id=:pParentId
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
    //HANDLE FILE UPLOAD
    $fileContractTmp = $_FILES["fileSk"]["tmp_name"];
    $fileContractName = $_FILES["fileSk"]["name"];
    if ($fileContractTmp != "" && $fileContractTmp != "none") {
      $contractPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "contract" . DS;
      if (!file_exists($contractPath)) {
        mkdir($contractPath, "0777", true);
      }
      $fcontractName = "contract_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileContractName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileContractTmp, $contractPath . $fcontractName)) {
        $this->fileSk = $fcontractName;
      }
    }
    if ($this->id == "") {
       $this->updateLast();
      $this->persist();
      

    } else {
      $this->update();
    }
    return $this;
  }

}

?>
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recselectionsk.inc
 *
 * @author mazte
 */
class RecSelectionSk extends DAL {

  public $id;
  public $parentId;
  public $planId;
  public $vacId;
  public $applId;
  public $skNo;
  public $skDate;
  public $status;
  public $remark;
  public $skFilename;
  public $startDate;
  public $endDate;
  public $empSkId;
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
    $sql = "INSERT INTO rec_selection_sk (
      
		parent_id,
        plan_id,
        vac_id,
        appl_id,
        sk_no,
        sk_date,
        status,
        remark,
        sk_filename,
        start_date,
        end_date,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPlanId, 
:pVacId, 
:pApplId, 
:pSkNo, 
:pSkDate, 
:pStatus, 
:pRemark, 
:pSkFilename, 
:pStartDate, 
:pEndDate, 

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
      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $this->vacId, PDO::PARAM_STR);
      $stmt->bindParam(':pApplId', $this->applId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pSkFilename', $this->skFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);

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
    $sql = " UPDATE rec_selection_sk SET
parent_id=:pParentId,
plan_id=:pPlanId,
vac_id=:pVacId,
appl_id=:pApplId,
sk_no=:pSkNo,
sk_date=:pSkDate,
status=:pStatus,
remark=:pRemark,
sk_filename=:pSkFilename,
start_date=:pStartDate,
end_date=:pEndDate,
emp_sk_id=:pEmpSkId,

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
      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $this->vacId, PDO::PARAM_STR);
      $stmt->bindParam(':pApplId', $this->applId, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pSkFilename', $this->skFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEmpSkId', $this->empSkId, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM rec_selection_sk
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
plan_id,
vac_id,
appl_id,
sk_no,
sk_date,
status,
remark,
sk_filename,
start_date,
end_date,
emp_sk_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_sk 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->planId = $row["plan_id"];
        $this->vacId = $row["vac_id"];
        $this->applId = $row["appl_id"];
        $this->skNo = $row["sk_no"];
        $this->skDate = $row["sk_date"];
        $this->status = $row["status"];
        $this->remark = $row["remark"];
        $this->skFilename = $row["sk_filename"];
        $this->startDate = $row["start_date"];
        $this->endDate = $row["end_date"];
        $this->empSkId = $row["emp_sk_id"];

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
plan_id,
vac_id,
appl_id,
sk_no,
sk_date,
status,
remark,
sk_filename,
start_date,
end_date,
emp_sk_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_sk 
      WHERE parent_id=:pParentId
      AND appl_id=:pApplId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pApplId', $this->applId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->planId = $row["plan_id"];
        $this->vacId = $row["vac_id"];
        $this->applId = $row["appl_id"];
        $this->skNo = $row["sk_no"];
        $this->skDate = $row["sk_date"];
        $this->status = $row["status"];
        $this->remark = $row["remark"];
        $this->skFilename = $row["sk_filename"];
        $this->startDate = $row["start_date"];
        $this->endDate = $row["end_date"];
        $this->empSkId = $row["emp_sk_id"];

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
plan_id,
vac_id,
appl_id,
sk_no,
sk_date,
status,
remark,
sk_filename,
start_date,
end_date,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_sk ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecSelectionSk("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
        $ret0->applId = $row["appl_id"];
        $ret0->skNo = $row["sk_no"];
        $ret0->skDate = $row["sk_date"];
        $ret0->status = $row["status"];
        $ret0->remark = $row["remark"];
        $ret0->skFilename = $row["sk_filename"];
        $ret0->startDate = $row["start_date"];
        $ret0->endDate = $row["end_date"];

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

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM rec_selection_sk t1 
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
    $cutil = new Common();
    $this->id = $_SESSION["entity_id"];
    $this->parentId = $_SESSION["eparent_id"];
    $this->applId = $_SESSION["eappl_id"];
    $this->planId = $_SESSION["eplan_id"];
    $this->vacId = $_SESSION["evac_id"];
    $this->getById();
    $this->populateWithPost();


    $fileSkTmp = $_FILES["skFilename"]["tmp_name"];
    $fileSkName = $_FILES["skFilename"]["name"];
    if ($fileSkTmp != "" && $fileSkTmp != "none") {
      $skPath = HOME_DIR . DS . "files" . DS . "recruit" . DS . "sk" . DS;
      if (!file_exists($skPath)) {
        mkdir($skPath, "0777", true);
      }
      $fskName = "applsk_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileSkName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileSkTmp, $skPath . $fskName)) {
        $this->skFilename = $fskName;
      }
    }

    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    //COPY TO EMP
    $ra = new RecApplicant();
    $ra->id = $this->applId;
    $ra = $ra->getById();
    $emp = new Emp();
    if (!empty($ra->empId)) {
      $emp->id = $ra->empId;
      $emp = $emp->getById();
    }
//    var_dump($ra);
    foreach ($ra as $key => $value) {
      if (property_exists($emp, $key)) {
        if ($key != "id")
          $emp->$key = $value;
      }
    }
    $emp->regNo = "";
    $emp->cat = $this->status;
    $emp->status=535;
    $emp->joinDate = $this->startDate;
//    var_dump($emp);
    if (empty($emp->id)) {
      $emp = $emp->persist();
    } else {
      $emp->update();
    }
    $ra->empId = $emp->id;
    $ra->acceptPlanId = $this->planId;
    $ra->acceptVacId = $this->vacId;
    $ra->update();
    //COPY TO EMP_CAREER
    $empc = new EmpCareer();
    $empc->id = $this->empSkId;
    $empc = $empc->getById();
    $empc->parentId = $emp->id;
    $empc->skDateStart = $this->startDate;
    $empc->skFilename = $this->skFilename;
    $empc->skDateEnd = $this->endDate;
    $empc->skDate = $this->skDate;
    $empc->skNo = $this->skNo;
    if (empty($empc->id)) {
      $empc = $empc->persist();
    } else {
      $empc->update();
    }
    $this->empSkId = $empc->id;
    $this->update();
    $sql = "UPDATE rec_applicant_ver set result_status=1 WHERE plan_id='$this->planId' AND vac_id='$this->vacId' AND applicant_id='$ra->id' ";
    $cutil->execute($sql);
//    die();
    return $this;
  }

}

?>
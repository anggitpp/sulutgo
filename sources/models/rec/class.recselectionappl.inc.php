<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recselectionappl.inc
 *
 * @author mazte
 */
class RecSelectionAppl extends DAL {

  public $id;
  public $parentId;
  public $planId;
  public $vacId;
  public $applId;
  public $phaseId;
  public $phaseSort;
  public $phaseStatus;
  public $selDate;
  public $selTime;
  public $selResult;
  public $selRemark;
  public $selCancel;
  public $selStatus;
  public $selFilename;
  public $apprSta;
  public $apprBy;
  public $apprDate;
  public $apprRemark;
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
    $sql = "INSERT INTO rec_selection_appl (

    parent_id,
    plan_id,
    vac_id,
    appl_id,
    phase_id,
    phase_sort,
    phase_status,
    sel_date,
    sel_time,
    sel_result,
    sel_remark,
     sel_cancel,
    sel_status,
    sel_filename,
    appr_sta,
    appr_by,
    appr_date,
    appr_remark,

    cre_by,
    cre_date
    ) VALUES (

    :pParentId, 
    :pPlanId, 
    :pVacId, 
    :pApplId, 
    :pPhaseId, 
    :pPhaseSort, 
    :pPhaseStatus, 
    :pSelDate, 
    :pSelTime, 
    :pSelResult, 
    :pSelRemark, 
    :pSelCancel,  
    :pSelStatus, 
    :pSelFilename, 
    :pApprSta, 
    :pApprBy, 
    :pApprDate, 
    :pApprRemark, 

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
      $stmt->bindParam(':pPhaseId', $this->phaseId, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseSort', $this->phaseSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseStatus', $this->phaseStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pSelDate', $this->selDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelTime', $this->selTime, PDO::PARAM_STR);
      $stmt->bindParam(':pSelResult', $this->selResult, PDO::PARAM_STR);
      $stmt->bindParam(':pSelRemark', $this->selRemark, PDO::PARAM_STR);
      $stmt->bindParam(':pSelStatus', $this->selStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pSelCancel', $this->selCancel, PDO::PARAM_STR);
      $stmt->bindParam(':pSelFilename', $this->selFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pApprSta', $this->apprSta, PDO::PARAM_STR);
      $stmt->bindParam(':pApprBy', $this->apprBy, PDO::PARAM_STR);
      $stmt->bindParam(':pApprDate', $this->apprDate, PDO::PARAM_STR);
      $stmt->bindParam(':pApprRemark', $this->apprRemark, PDO::PARAM_STR);
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
    $sql = " UPDATE rec_selection_appl SET

    parent_id=:pParentId,
    plan_id=:pPlanId,
    vac_id=:pVacId,
    appl_id=:pApplId,
    phase_id=:pPhaseId,
    phase_sort=:pPhaseSort,
    phase_status=:pPhaseStatus,
    sel_date=:pSelDate,
    sel_time=:pSelTime,
    sel_result=:pSelResult,
    sel_remark=:pSelRemark,
    sel_cancel=:pSelCancel,
    sel_status=:pSelStatus,
    sel_filename=:pSelFilename,
    appr_sta=:pApprSta,
    appr_by=:pApprBy,
    appr_date=:pApprDate,
    appr_remark=:pApprRemark,

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
      $stmt->bindParam(':pPhaseId', $this->phaseId, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseSort', $this->phaseSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseStatus', $this->phaseStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pSelDate', $this->selDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelTime', $this->selTime, PDO::PARAM_STR);
      $stmt->bindParam(':pSelResult', $this->selResult, PDO::PARAM_STR);
      $stmt->bindParam(':pSelRemark', $this->selRemark, PDO::PARAM_STR);
      $stmt->bindParam(':pSelCancel', $this->selCancel, PDO::PARAM_STR);
      $stmt->bindParam(':pSelStatus', $this->selStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pSelFilename', $this->selFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pApprSta', $this->apprSta, PDO::PARAM_STR);
      $stmt->bindParam(':pApprBy', $this->apprBy, PDO::PARAM_STR);
      $stmt->bindParam(':pApprDate', $this->apprDate, PDO::PARAM_STR);
      $stmt->bindParam(':pApprRemark', $this->apprRemark, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM rec_selection_appl
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
    phase_id,
    phase_sort,
    phase_status,
    sel_date,
    sel_time,
    sel_result,
    sel_remark,
    sel_cancel,
    sel_status,
    sel_filename,
    appr_sta,
    appr_by,
    appr_date,
    appr_remark,

    cre_by,
    cre_date,
    upd_by,
    upd_date
    FROM rec_selection_appl 
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
        $this->phaseId = $row["phase_id"];
        $this->phaseSort = $row["phase_sort"];
        $this->phaseStatus = $row["phase_status"];
        $this->selDate = $row["sel_date"];
        $this->selTime = $row["sel_time"];
        $this->selResult = $row["sel_result"];
        $this->selRemark = $row["sel_remark"];
        $this->selStatus = $row["sel_status"];
        $this->selCancel = $row["sel_cancel"];
        $this->selFilename = $row["sel_filename"];
        $this->apprSta = $row["appr_sta"];
        $this->apprBy = $row["appr_by"];
        $this->apprDate = $row["appr_date"];
        $this->apprRemark = $row["appr_remark"];

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

  function getByIdUnique($parentId, $planId, $vacId, $applId, $phaseId) {
    $sql = " SELECT 

    id,
    parent_id,
    plan_id,
    vac_id,
    appl_id,
    phase_id,
    phase_sort,
    phase_status,
    sel_date,
    sel_time,
    sel_result,
    sel_remark,
    sel_cancel,
    sel_status,
    sel_filename,
    appr_sta,
    appr_by,
    appr_date,
    appr_remark,

    cre_by,
    cre_date,
    upd_by,
    upd_date
    FROM rec_selection_appl 
    WHERE parent_id=:pParentId
    AND plan_id=:pPlanId
    AND vac_id=:pVacId
    AND appl_id=:pApplId
    AND phase_id=:pPhaseId
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pPlanId', $planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $vacId, PDO::PARAM_STR);
      $stmt->bindParam(':pApplId', $applId, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseId', $phaseId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->planId = $row["plan_id"];
        $this->vacId = $row["vac_id"];
        $this->applId = $row["appl_id"];
        $this->phaseId = $row["phase_id"];
        $this->phaseSort = $row["phase_sort"];
        $this->phaseStatus = $row["phase_status"];
        $this->selDate = $row["sel_date"];
        $this->selTime = $row["sel_time"];
        $this->selResult = $row["sel_result"];
        $this->selRemark = $row["sel_remark"];
        $this->selCancel = $row["sel_cancel"];
        $this->selStatus = $row["sel_status"];
        $this->selFilename = $row["sel_filename"];
        $this->apprSta = $row["appr_sta"];
        $this->apprBy = $row["appr_by"];
        $this->apprDate = $row["appr_date"];
        $this->apprRemark = $row["appr_remark"];

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
    phase_id,
    phase_sort,
    phase_status,
    sel_date,
    sel_time,
    sel_result,
    sel_remark,
    sel_cancel,
    sel_status,
    sel_filename,
    appr_sta,
    appr_by,
    appr_date,
    appr_remark,

    cre_by,
    cre_date,
    upd_by,
    upd_date
    FROM rec_selection_appl ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecSelectionAppl("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
        $ret0->applId = $row["appl_id"];
        $ret0->phaseId = $row["phase_id"];
        $ret0->phaseSort = $row["phase_sort"];
        $ret0->phaseStatus = $row["phase_status"];
        $ret0->selDate = $row["sel_date"];
        $ret0->selTime = $row["sel_time"];
        $ret0->selResult = $row["sel_result"];
        $ret0->selRemark = $row["sel_remark"];
        $ret0->selCancel = $row["sel_cancel"];
        $ret0->selStatus = $row["sel_status"];
        $ret0->selFilename = $row["sel_filename"];
        $ret0->apprSta = $row["appr_sta"];
        $ret0->apprBy = $row["appr_by"];
        $ret0->apprDate = $row["appr_date"];
        $ret0->apprRemark = $row["appr_remark"];

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

//
//  function getRowsForEdit($applId, $planId, $vacId, $parentId) {
//    $sql = " SELECT 
//
//
//id,
//parent_id,
//plan_id,
//vac_id,
//appl_id,
//phase_id,
//phase_sort,
//phase_status,
//sel_date,
//sel_result,
//sel_remark,
//sel_status,
//appr_sta,
//appr_by,
//appr_date,
//appr_remark,
//
//      cre_by,
//      cre_date,
//      upd_by,
//      upd_date
//      FROM rec_selection_appl 
//      WHERE 
//      parent_id=:pParentId
//      AND plan_id=:pPlanId
//      AND vac_id=:pVacId
//      AND appl_id=:pApplId
//      ORDER BY phase_sort
//";
//    try {
//      $stmt = $this->db->prepare($sql);
//      $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
//      $stmt->bindParam(':pPlanId', $parentId, PDO::PARAM_STR);
//      $stmt->bindParam(':pVacId', $parentId, PDO::PARAM_STR);
//      $stmt->bindParam(':pApplId', $parentId, PDO::PARAM_STR);
//      $stmt->execute();
//      $result = $stmt->fetchAll(PDO::FETCH_ASSO C);
//      $ret = array();
//      foreach ($result as $row) {
//        $ret0 = new RecSelectionAppl("C");
//
//        $ret0->id = $row["id"];
//        $ret0->parentId = $row["parent_id"];
//        $ret0->planId = $row["plan_id"];
//        $ret0->vacId = $row["vac_id"];
//        $ret0->applId = $row["appl_id"];
//        $ret0->phaseId = $row["phase_id"];
//        $ret0->phaseSort = $row["phase_sort"];
//        $ret0->phaseStatus = $row["phase_status"];
//        $ret0->selDate = $row["sel_date"];
//        $ret0->selResult = $row["sel_result"];
//        $ret0->selRemark = $row["sel_remark"];
//        $ret0->selStatus = $row["sel_status"];
//        $ret0->apprSta = $row["appr_sta"];
//        $ret0->apprBy = $row["appr_by"];
//        $ret0->apprDate = $row["appr_date"];
//        $ret0->apprRemark = $row["appr_remark"];
//
//        $ret0->creBy = $row["cre_by"];
//        $ret0->creDate = $row["cre_date"];
//        $ret0->updBy = $row["upd_by"];
//        $ret0->updDate = $row["upd_date"];
//        $ret[] = $ret0;
//      }
//      $stmt->closeCursor();
//      return $ret;
//    } catch (Exception $ex) {
//      var_dump($ex);
//    }
//  }

  function getRowsByParent($parentId) {
    $sql = " SELECT 


    id,
    parent_id,
    plan_id,
    vac_id,
    appl_id,
    phase_id,
    phase_sort,
    phase_status,
    sel_date,
    sel_time,
    sel_result,
    sel_remark,
    sel_cancel,
    sel_status,
    sel_filename,
    appr_sta,
    appr_by,
    appr_date,
    appr_remark,

    cre_by,
    cre_date,
    upd_by,
    upd_date
    FROM rec_selection_appl 
    WHERE parent_id=:pParentId
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParent Id', $parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecSelectionAppl("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row ["parent_id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
        $ret0->applId = $row["appl_id"];
        $ret0->phaseId = $row["phase_id"];
        $ret0->phaseSort = $row["phase_sort"];
        $ret0->phaseStatus = $row["phase_status"];
        $ret0->selDate = $row["sel_date"];
        $ret0->selTime = $row["sel_time"];
        $ret0->selResult = $row["sel_result"];
        $ret0->selRemark = $row["sel_remark"];
        $ret0->selCancel = $row["sel_cancel"];
        $ret0->selStatus = $row["sel_status"];
        $ret0->selFilename = $row["sel_filename"];
        $ret0->apprSta = $row["appr_sta"];
        $ret0->apprBy = $row["appr_by"];
        $ret0->apprDate = $row["appr_date"];
        $ret0->apprRemark = $row["appr_remark"];

        $ret0->creBy = $row["cre_by"];
        $ret0->creDate = $row["cre_date"];
        $ret0->updBy = $row["upd_by"];
        $ret0->updDate = $row[
        "upd_date"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getRowsForEdit($applId, $planId, $vacId, $parentId) {
    $sql = " SELECT
    t1.id id,
    t1.parent_id parentId,
    t1.plan_id planId,
    t1.vac_id vacId,
    t1.appl_id applId,
    t1.phase_id phaseId,
    t1.phase_sort phaseSort,
    t1.phase_status phaseStatus,
    t1.sel_date selDate,
    t1.sel_time selTime,
    t1.sel_result selResult,
    t1.sel_remark selRemark,
    t1.sel_filename selFilename,
    t1.sel_cancel selCancel,
    t1.sel_status selStatus,
    t1.appr_sta apprSta,
    t1.appr_by apprBy,
    t1.appr_date apprDate,
    t1.appr_remark apprRemark,

    t2.namaData phaseName,


    t1.cre_by creBy,
    t1.cre_date creDate,
    t1.upd_by updBy,
    t1.upd_date updDate
    FROM rec_selection_appl t1
    JOIN mst_data t2 ON t1.phase_id=t2.kodeData
    WHERE
    t1.parent_id=:pParentId
    AND t1.plan_id=:pPlanId
    AND t1.vac_id=:pVacId
    AND t1.appl_id=:pApplId
    ORDER BY phase_sort
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pPlanId', $planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $vacId, PDO::PARAM_STR);
      $stmt->bindParam(':pApplId', $applId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTable() {
    $sql = " SELECT
    t1.id id,
    t1.parent_id parentId,
    t1.plan_id planId,
    t1.vac_id vacId,
    t1.appl_id applId,
    t1.phase_id phaseId,
    t1.phase_sort phaseSort,
    t1.phase_status phaseStatus,
    t1.sel_date selDate,
    t1.sel_time selTime,
    t1.sel_result selResult,
    t1.sel_remark selRemark,
    t1.sel_cancel selCancel,
    t1.sel_status selStatus,
    t1.sel_filename selFilename,
    t1.appr_sta apprSta,
    t1.appr_by apprBy,
    t1.appr_date apprDate,
    t1.appr_remark apprRemark,

    t1.cre_by creBy,
    t1.cre_date creDate,
    t1.upd_by updBy,
    t1.upd_date updDate
    FROM rec_selection_appl t1 
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
    global $db,$cUsername;
    $cutil = new Common();
    $rs = new RecSelection();
    $rs->id = $_GET["pid"];
    $idSel = $_GET["sid"];
    $rs = $rs->getById();
    $rsaId = $_POST["raId"];
    $c = 0;
    $len = count($rsaId);
    $fileList = $this->reArrayFiles($_FILES["raSelFilename"]);
    // echo "<pre>".json_encode($_POST)."</pre>";
    // echo "<pre>".json_encode($fileList)."</pre>";
    $selFileIds = $_POST["pSelId"];
    $dno = 0;
    if (count($selFileIds) > 0) {
      $usedId = array();
      foreach ($selFileIds as $sfId) {
        $selFile = new RecSelectionFile();
        $selFile->id = $sfId;
        $selFile->parentId = $idSel;
        $selFile->name = $_POST["pSelName"][$dno];
        $selFile->filename = $_POST["pSelFilename"][$dno];
        $selFile->remark = $_POST["pSelRemark"][$dno];
        if ($selFile->id == "") {
          $selFile = $selFile->persist();
        } else {
          $selFile->update();
        }
        $usedId[] = $selFile->id;
        $dno++;
      }
      $usedDtlIds = implode(",", $usedId);
      $cutil->execute("DELETE FROM rec_selection_appl_file WHERE parent_id='$idSel' AND id NOT IN ($usedDtlIds)");
    } else {
      $cutil->execute("DELETE FROM rec_selection_appl_file WHERE parent_id='$idSel'");
    } 

    //UPLOAD FILE
    
        // die();
    foreach ($rsaId as $raId) {	
      $ra = new RecSelectionAppl();
      if ($c < ($len - 1)) {
        $ra->id = $raId;
        $ra->applId = $_POST["raApplId"][$c];
        $ra->parentId = $_POST["raParentId"][$c];
        $ra->phaseId = $_POST["raPhaseId"][$c];
        $ra->phaseSort = $_POST["raPhaseSort"][$c];
        $ra->phaseStatus = $_POST["raPhaseStatus"][$c];
        $ra->planId = $_POST["raPlainId"][$c];
        $ra->selDate = ($_POST["raSelDate"][$c] == "0000-00-00" ? null : $_POST["raSelDate"][$c]);
        $ra->selTime = ($_POST["raSelTime"][$c] == "00:00" ? null : $_POST["raSelTime"][$c]);
        $ra->selRemark = $_POST["raSelRemark"][$c];
        $ra->selResult = $_POST["raSelResult"][$c];
        $ra->selCancel = $_POST["raSelCancel"][$c];
        $ra->selStatus = $_POST["raSelStatus"][$c];
        $ra->vacId = $_POST["raVacId"][$c];
        $file = $fileList[$c];
        $filePostTmp = $file["tmp_name"];
        $filePostName = $file["name"];

       

        if ($filePostTmp != "" && $filePostTmp != "none") {
           $postPath = HOME_DIR . DS . "files" . DS . "recruit" . DS . "selection" . DS;
          if (!file_exists($postPath)) {
            mkdir($postPath, "0777", true);
          }
          $fpostName = "sel_file_" . date("Ymd_His") . "_" . $rs->id . "_" . $ra->id . "." . pathinfo($filePostName, PATHINFO_EXTENSION);
          if (move_uploaded_file($filePostTmp, $postPath . $fpostName)) {
            $ra->selFilename = $fpostName;
          }
        }

      } else {
        $e = 0;
        $ra->id = $raId;
        $ra->applId = $_POST["raApplId"][$c];
        $ra->parentId = $_POST["raParentId"][$c];
        $ra->phaseId = $_POST["raPhaseId"][$c];
        $ra->phaseSort = $_POST["raPhaseSort"][$c];
        $ra->phaseStatus = $_POST["raPhaseStatus"][$c];
        $ra->planId = $_POST["raPlainId"][$c];
        $ra->vacId = $_POST["raVacId"][$c];
        $ra->apprBy = $cUsername;
        $ra->apprSta = $_POST["raApprSta"][$e];
        $ra->apprDate = ($_POST["raApprDate"][$c] == "0000-00-00" ? null : $_POST["raApprDate"][$c]);
        $ra->apprRemark = $_POST["raApprRemark"][$c];
        if (!empty($ra->apprDate)) {
          $sql = "
          UPDATE rec_applicant_ver 
          SET sel_status=1,
          last_status='$ra->apprSta'
          WHERE plan_id='$ra->planId' AND vac_id='$ra->vacId' AND applicant_id='$ra->applId';
          ";
//          echo $sql;
          $cutil->execute($sql);
        }
      }
      $ra->update();
      $c++;

    }
  }

  function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
      foreach ($file_keys as $key) {
        $file_ary[$i][$key] = $file_post[$key][$i];
      }
    }

    return $file_ary;
  }

}

?>
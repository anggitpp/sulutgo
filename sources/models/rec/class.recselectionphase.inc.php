<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recselectionphase.inc
 *
 * @author mazte
 */
class RecSelectionPhase extends DAL {

  public $id;
  public $parentId;
  public $planId;
  public $vacId;
  public $phaseId;
  public $phaseSort;
  public $phaseStatus;
  public $phaseDate;
  public $phaseTime;
  public $phaseLoc;
  public $phaseRemark;
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
    $sql = "INSERT INTO rec_selection_phase (
  
parent_id,
plan_id,
vac_id,
phase_id,
phase_sort,
phase_status,
phase_date,
phase_time,
phase_loc,
phase_remark,

		
          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPlanId, 
:pVacId, 
:pPhaseId, 
:pPhaseSort, 
:pPhaseStatus, 
:pPhaseDate, 
:pPhaseTime, 
:pPhaseLoc, 
:pPhaseRemark, 

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
      $stmt->bindParam(':pPhaseId', $this->phaseId, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseSort', $this->phaseSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseStatus', $this->phaseStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseDate', $this->phaseDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseTime', $this->phaseTime, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseLoc', $this->phaseLoc, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseRemark', $this->phaseRemark, PDO::PARAM_STR);

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
    $sql = " UPDATE rec_selection_phase SET
parent_id=:pParentId,
plan_id=:pPlanId,
vac_id=:pVacId,
phase_id=:pPhaseId,
phase_sort=:pPhaseSort,
phase_status=:pPhaseStatus,
phase_date=:pPhaseDate,
phase_time=:pPhaseTime,
phase_loc=:pPhaseLoc,
phase_remark=:pPhaseRemark,

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
      $stmt->bindParam(':pPhaseId', $this->phaseId, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseSort', $this->phaseSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseStatus', $this->phaseStatus, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseDate', $this->phaseDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseTime', $this->phaseTime, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseLoc', $this->phaseLoc, PDO::PARAM_STR);
      $stmt->bindParam(':pPhaseRemark', $this->phaseRemark, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM rec_selection_phase
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
phase_id,
phase_sort,
phase_status,
phase_date,
phase_time,
phase_loc,
phase_remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_phase 
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
        $this->phaseId = $row["phase_id"];
        $this->phaseSort = $row["phase_sort"];
        $this->phaseStatus = $row["phase_status"];
        $this->phaseDate = $row["phase_date"];
        $this->phaseTime = $row["phase_time"];
        $this->phaseLoc = $row["phase_loc"];
        $this->phaseRemark = $row["phase_remark"];

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
phase_id,
phase_sort,
phase_status,
phase_date,
phase_time,
phase_loc,
phase_remark,      

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_phase ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecSelectionPhase("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
        $ret0->phaseId = $row["phase_id"];
        $ret0->phaseSort = $row["phase_sort"];
        $ret0->phaseStatus = $row["phase_status"];
        $ret0->phaseDate = $row["phase_date"];
        $ret0->phaseTime = $row["phase_time"];
        $ret0->phaseLoc = $row["phase_loc"];
        $ret0->phaseRemark = $row["phase_remark"];
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

  function loadTableNew() {
    $sql = "SELECT
      t1.id id,
      t1.parent_id parentId,
      t1.plan_id planId,
      t1.vac_id vacId,
      t1.phase_status phaseStatus,
      t1.phase_date phaseDate,
      t1.phase_time phaseTime,
      t1.phase_loc phaseLoc,
      t1.phase_remark phaseRemark,
      t2.kodeData phaseId,
      t2.namaData phaseName,
      t2.urutanData phaseSort,
      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM mst_data t2 
      LEFT JOIN rec_selection_phase t1 ON t1.phase_id=t2.kodeData AND t1.id=-999
      WHERE t2.kodeCategory='R09' 
      ORDER BY t2.urutanData      
			
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableByParentId($parentId) {
    $sql = " SELECT
      t1.id id,
      t1.parent_id parentId,
      t1.plan_id planId,
      t1.vac_id vacId,
      t1.phase_id phaseId,
      t1.phase_sort phaseSort,
      t1.phase_status phaseStatus,
      t1.phase_date phaseDate,
      t1.phase_time phaseTime,
      t1.phase_loc phaseLoc,
      t1.phase_remark phaseRemark,
      t2.namaData phaseName,
      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM rec_selection_phase t1 
      JOIN mst_data t2 ON t1.phase_id=t2.kodeData
      WHERE t1.parent_id='$parentId'
        ORDER BY t1.phase_sort 
      ";
    try {
      $stmt = $this->db->prepare($sql);
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
      t1.phase_id phaseId,
      t1.phase_sort phaseSort,
      t1.phase_status phaseStatus,
      t1.phase_date phaseDate,
      t1.phase_time phaseTime,
      t1.phase_loc phaseLoc,
      t1.phase_remark phaseRemark,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM rec_selection_phase t1 
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
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    return $this;
  }

}

?>
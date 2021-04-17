<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recselectionpos.inc.php
 *
 * @author mazte
 */
class RecSelectionPlacement extends DAL {

  public $id;
  public $parentId;
  public $planId;
  public $vacId;
  public $applId;
  public $position;
  public $rank;
  public $topId;
  public $dirId;
  public $divId;
  public $deptId;
  public $unitId;
  public $remark;
  public $empPlaceId;
  public $provId;
  public $cityId;
  public $leaderId;
  public $administrationId;
  public $replacementId;
  public $replacement2Id;
  public $lembur;
  public $payrollId;
  public $processId;
  public $groupId;
  public $penilaianId;
  public $shiftId;
  public $companyId;
  public $kategori;
  public $perdin;
  public $obat;
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
    $sql = "INSERT INTO rec_selection_placement (

parent_id,
plan_id,
vac_id,
appl_id,
position,
rank,
top_id,
dir_id,
div_id,
dept_id,
unit_id,
leader_id,
administration_id,
replacement_id,
replacement2_id,
lembur,
payroll_id,
proses_id,
group_id,
penilaian_id,
shift_id,
company_id,
kategori,
perdin,
obat,
remark,
emp_place_id,
prov_id,
city_id,


          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPlanId, 
:pVacId, 
:pApplId, 
:pPosition, 
:pRank, 
:pTopId, 
:pDirId, 
:pDivId, 
:pDeptId, 
:pUnitId, 
:pLeaderId,
:pAdministrationId,
:pReplacementId,
:pReplacement2Id,
:pLembur,
:pPayrollId,
:pProsesId,
:pGroupId,
:pPenilaianId,
:pShiftId,
:pCompanyId,
:pKategori,
:pPerdin,
:pObat,
:pRemark, 
:pEmpPlaceId,
:pProvId, 
:pCityId, 

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
      $stmt->bindParam(':pPosition', $this->position, PDO::PARAM_STR);
      $stmt->bindParam(':pRank', $this->rank, PDO::PARAM_STR);
      $stmt->bindParam(':pTopId', $this->topId, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaderId', $this->leaderId, PDO::PARAM_STR);
      $stmt->bindParam(':pAdministrationId', $this->administrationId, PDO::PARAM_STR);
      $stmt->bindParam(':pReplacementId', $this->replacementId, PDO::PARAM_STR);
      $stmt->bindParam(':pReplacement2Id', $this->replacement2Id, PDO::PARAM_STR);
      $stmt->bindParam(':pLembur', $this->replacement2Id, PDO::PARAM_STR);
      $stmt->bindParam(':pPayrollId', $this->payrollId, PDO::PARAM_STR);
      $stmt->bindParam(':pProsesId', $this->prosesId, PDO::PARAM_STR);
      $stmt->bindParam(':pGroupId', $this->groupId, PDO::PARAM_STR);
      $stmt->bindParam(':pPenilaianId', $this->penilaianId, PDO::PARAM_STR);
      $stmt->bindParam(':pShiftId', $this->shiftId, PDO::PARAM_STR);
      $stmt->bindParam(':pCompanyId', $this->companyId, PDO::PARAM_STR);
      $stmt->bindParam(':pKategori', $this->kategori, PDO::PARAM_STR);
      $stmt->bindParam(':pPerdin', $this->perdin, PDO::PARAM_STR);
      $stmt->bindParam(':pObat', $this->obat, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pEmpPlaceId', $this->empPlaceId, PDO::PARAM_STR);
      $stmt->bindParam(':pProvId', $this->provId, PDO::PARAM_STR);
      $stmt->bindParam(':pCityId', $this->cityId, PDO::PARAM_STR);

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
    $sql = " UPDATE rec_selection_placement SET
parent_id=:pParentId,
plan_id=:pPlanId,
vac_id=:pVacId,
appl_id=:pApplId,
position=:pPosition,
rank=:pRank,
top_id=:pTopId,
dir_id=:pDirId,
div_id=:pDivId,
dept_id=:pDeptId,
unit_id=:pUnitId,
leader_id=:pLeaderId,
administration_id=:pAdministrationId,
replacement_id=:pReplacementId,
replacement2_id=:pReplacement2Id,
lembur=:pLembur,
payroll_id=:pPayrollId,
proses_id=:pProsesId,
group_id=:pGroupId,
penilaian_id=:pPenilaianId,
shift_id=:pShiftId,
company_id=:pCompanyId,
kategori=:pKategori,
perdin=:pPerdin,
obat=:pObat,
remark=:pRemark,
emp_place_id=:pEmpPlaceId,
prov_id=:pProvId,
city_id=:pCityId,

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
      $stmt->bindParam(':pPosition', $this->position, PDO::PARAM_STR);
      $stmt->bindParam(':pRank', $this->rank, PDO::PARAM_STR);
      $stmt->bindParam(':pTopId', $this->topId, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaderId', $this->leaderId, PDO::PARAM_STR);
      $stmt->bindParam(':pAdministrationId', $this->administrationId, PDO::PARAM_STR);
      $stmt->bindParam(':pReplacementId', $this->replacementId, PDO::PARAM_STR);
    $stmt->bindParam(':pReplacement2Id', $this->replacement2Id, PDO::PARAM_STR);
    $stmt->bindParam(':pLembur', $this->lembur, PDO::PARAM_STR);
    $stmt->bindParam(':pPayrollId', $this->payrollId, PDO::PARAM_STR);
    $stmt->bindParam(':pProsesId', $this->prosesId, PDO::PARAM_STR);
    $stmt->bindParam(':pGroupId', $this->groupId, PDO::PARAM_STR);
    $stmt->bindParam(':pPenilaianId', $this->penilaianId, PDO::PARAM_STR);
    $stmt->bindParam(':pShiftId', $this->shiftId, PDO::PARAM_STR);
    $stmt->bindParam(':pCompanyId', $this->companyId, PDO::PARAM_STR);
    $stmt->bindParam(':pKategori', $this->kategori, PDO::PARAM_STR);
    $stmt->bindParam(':pPerdin', $this->perdin, PDO::PARAM_STR);
    $stmt->bindParam(':pObat', $this->obat, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pEmpPlaceId', $this->empPlaceId, PDO::PARAM_STR);
      $stmt->bindParam(':pProvId', $this->provId, PDO::PARAM_STR);
      $stmt->bindParam(':pCityId', $this->cityId, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM rec_selection_placement
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
position,
rank,
top_id,
dir_id,
div_id,
dept_id,
unit_id,
leader_id,
administration_id,
replacement_id,
replacement2_id,
lembur,
payroll_id,
proses_id,
group_id,
penilaian_id,
shift_id,
company_id,
kategori,
perdin,
obat,
remark,
emp_place_id,
prov_id,
city_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_placement 
      WHERE id=:pId
      ";
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
        $this->position = $row["position"];
        $this->rank = $row["rank"];
        $this->topId = $row["top_id"];
        $this->dirId = $row["dir_id"];
        $this->divId = $row["div_id"];
        $this->deptId = $row["dept_id"];
        $this->unitId = $row["unit_id"];
        $this->leaderId = $row["leader_id"];
        $this->administrationId = $row["administration_id"];
        $this->replacementId = $row["replacement_id"];
        $this->replacement2Id = $row["replacement2_id"];
        $this->lembur = $row["lembur"];
        $this->payrollId = $row["payroll_id"];
        $this->prosesId = $row["proses_id"];
        $this->groupId = $row["group_id"];
        $this->penilaianId = $row["penilaian_id"];
        $this->shiftId = $row["shift_id"];  
        $this->companyId = $row["company_id"];
        $this->kategori = $row["kategori"];
        $this->perdin = $row["perdin"];
        $this->obat = $row["obat"];
        $this->remark = $row["remark"];
        $this->empPlaceId = $row["emp_place_id"];
        $this->provId = $row["prov_id"];
        $this->cityId = $row["city_id"];




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
position,
rank,
top_id,
dir_id,
div_id,
dept_id,
unit_id,
leader_id,
administration_id,
replacement_id,
replacement2_id,
lembur,
payroll_id,
proses_id,
group_id,
penilaian_id,
shift_id,
company_id,
kategori,
perdin,
obat,
remark,
emp_place_id,
prov_id,
city_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_placement 
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
        $this->position = $row["position"];
        $this->rank = $row["rank"];
        $this->topId = $row["top_id"];
        $this->dirId = $row["dir_id"];
        $this->divId = $row["div_id"];
        $this->deptId = $row["dept_id"];
        $this->unitId = $row["unit_id"];
        $this->leaderId = $row["leader_id"];
        $this->administrationId = $row["administration_id"];
        $this->replacementId = $row["replacement_id"];
    $this->replacement2Id = $row["replacement2_id"];
    $this->lembur = $row["lembur"];
    $this->payrollId = $row["payroll_id"];
    $this->prosesId = $row["proses_id"];
    $this->groupId = $row["group_id"];
    $this->penilaianId = $row["penilaian_id"];
    $this->shiftId = $row["shift_id"];  
    $this->companyId = $row["company_id"];
    $this->kategori = $row["kategori"];
    $this->perdin = $row["perdin"];
    $this->obat = $row["obat"];
        $this->remark = $row["remark"];
        $this->empPlaceId = $row["emp_place_id"];
        $this->provId = $row["prov_id"];
        $this->cityId = $row["city_id"];




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
position,
rank,
top_id,
dir_id,
div_id,
dept_id,
unit_id,
remark,
emp_place_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_selection_placement ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecSelectionPlacement("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
        $ret0->applId = $row["appl_id"];
        $ret0->position = $row["position"];
        $ret0->rank = $row["rank"];
        $ret0->topId = $row["top_id"];
        $ret0->dirId = $row["dir_id"];
        $ret0->divId = $row["div_id"];
        $ret0->deptId = $row["dept_id"];
        $ret0->unitId = $row["unit_id"];
        $ret0->remark = $row["remark"];
        $ret0->empPlaceId = $row["emp_place_id"];

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
      FROM rec_selection_placement t1 
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
	global $arrParameter;
    $cutil = new Common();
    $this->id = $_SESSION["entity_id"];
    $this->parentId = $_SESSION["eparent_id"];
    $this->applId = $_SESSION["eappl_id"];
    $this->planId = $_SESSION["eplan_id"];
    $this->vacId = $_SESSION["evac_id"];
    $this->getById();
    $this->populateWithPost();
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    $empId = $cutil->getDescription("SELECT emp_id FROM rec_applicant WHERE id='$this->applId'", "emp_id");
    if (!empty($empId)) {
      $emph = new EmpPhist();
      $emph->id = $this->empPlaceId;
      $emph = $emph->getById();
      $emph->parentId = $empId;
      $emph->posName = $this->position;
      $emph->rank = $this->rank;
      $emph->topId = $this->topId;
      $emph->dirId = $this->dirId;
      $emph->divId = $this->divId;
      $emph->deptId = $this->deptId;
      $emph->unitId = $this->unitId;
      $emph->remark = $this->remark;
      $emph->leaderId = $this->leaderId;
      $emph->administrationId = $this->administrationId;
      $emph->replacementId = $this->replacementId;
      $emph->replacement2Id = $this->replacement2Id;
      $emph->lembur = $this->lembur;
      $emph->payrollId = $this->payrollId;
      $emph->prosesId = $this->prosesId;
      $emph->groupId = $this->groupId;
      $emph->penilaianId = $this->penilaianId;
      $emph->shiftId = $this->shiftId;  
      $emph->companyId = $this->companyId;
      $emph->kategori = $this->kategori;
      $emph->perdin = $this->perdin;
      $emph->obat = $this->obat;
      $emph->provId = $this->provId;
      $emph->cityId = $this->cityId;
      if (empty($emph->id)) {
        $emph = $emph->persist();
      } else {
        $emph->update();
      }
      $this->empPlaceId = $emph->id;
      $this->update();
      $sql = "UPDATE rec_applicant_ver set placement_status=1 WHERE plan_id='$this->planId' AND vac_id='$this->vacId' AND applicant_id='$this->applId' ";
      $cutil->execute($sql);
    }else{
		$cat = getField("select status from rec_selection_sk where plan_id='$this->planId' AND vac_id='$this->vacId' and appl_id='$this->applId'");
		if(empty($cat)) $cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		
		$sql="insert ignore into emp (cat, name, alias, birth_place, birth_date, ktp_no, ktp_filename, ktp_valid, gender, ktp_address, ktp_prov, ktp_city, dom_address, dom_prov, dom_city, phone_no, cell_no, email, marital, religion, pic_filename, npwp_no, npwp_date, bpjs_no, bpjs_date, blood_type, blood_resus, uni_cloth, uni_pant, uni_shoe, status, join_date, cre_by, cre_date) select '".$cat."', name, alias, birth_place, birth_date, ktp_no, ktp_filename, ktp_valid, gender, ktp_address, ktp_prov, ktp_city, dom_address, dom_prov, dom_city, phone_no, cell_no, email, marital, religion, pic_filename, npwp_no, npwp_date, bpjs_no, bpjs_date, blood_type, blood_resus, uni_cloth, uni_pant, uni_shoe, '".getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1")."', '".date("Y-m-d")."', cre_by, cre_date from rec_applicant where id='$this->applId'";
		db($sql);
						
		$emph = new EmpPhist();
		$emph->id = getField("select id from emp_phist order by id desc limit 1");
		$emph = $emph->getById();
		$emph->parentId = getField("select id from emp order by id desc limit 1");
    $emph->posName = $this->position;
		$emph->rank = $this->rank;
    $emph->topId = $this->topId;
		$emph->dirId = $this->dirId;
		$emph->divId = $this->divId;
		$emph->deptId = $this->deptId;
		$emph->unitId = $this->unitId;
		$emph->remark = $this->remark;		
    $emph->leaderId = $this->leaderId;
    $emph->administrationId = $this->administrationId;
    $emph->replacementId = $this->replacementId;
    $emph->replacement2Id = $this->replacement2Id;
    $emph->lembur = $this->lembur;
    $emph->payrollId = $this->payrollId;
    $emph->prosesId = $this->prosesId;
    $emph->groupId = $this->groupId;
    $emph->penilaianId = $this->penilaianId;
    $emph->shiftId = $this->shiftId;  
    $emph->companyId = $this->companyId;
    $emph->kategori = $this->kategori;
    $emph->perdin = $this->perdin;
    $emph->obat = $this->obat;
    $emph->provId = $this->provId;
    $emph->cityId = $this->cityId;
		
		db("delete from emp_phist where parent_id='".$emph->parentId."'");
		
		$emph = $emph->persist();		
      // var_dump($this);
	}

   // die();
    return $this;
  }

}

?>
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_phist.inc.php
 *
 * @author mazte
 */
class EmpPhist extends DAL {

  public $id;
  public $parentId;
  public $posName;
  public $skNo;
  public $skSubject;
  public $skDate;
  public $filename;
  public $dirId;
  public $divId;
  public $deptId;
  public $unitId;
  public $provId;
  public $cityId;
  public $location;
  public $rank;
  public $grade;
  public $startDate;
  public $endDate;
  public $remark;
  public $status;
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
  public $topId;
  public $managerId;
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
    $sql = "INSERT INTO emp_phist (
      
parent_id,
pos_name,
sk_no,
sk_subject,
sk_date,
filename,
dir_id,
div_id,
dept_id,
unit_id,
prov_id,
city_id,
location,
rank,
grade,
start_date,
end_date,
remark,
status,
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
top_id,
manager_id,
          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPosName, 
:pSkNo, 
:pSkSubject, 
:pSkDate, 
:pFilename, 
:pDirId, 
:pDivId, 
:pDeptId, 
:pUnitId, 
:pProvId, 
:pCityId, 
:pLocation,
:pRank,
:pGrade,
:pStartDate, 
:pEndDate, 
:pRemark, 
:pStatus,
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
:pTopId,
:pManagerId,
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
      $stmt->bindParam(':pPosName', $this->posName, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkSubject', $this->skSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
	  $stmt->bindParam(':pProvId', $this->provId, PDO::PARAM_STR);
	  $stmt->bindParam(':pCityId', $this->cityId, PDO::PARAM_STR);
      $stmt->bindParam(':pLocation', $this->location, PDO::PARAM_STR);
      $stmt->bindParam(':pRank', $this->rank, PDO::PARAM_STR);
      $stmt->bindParam(':pGrade', $this->grade, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
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
	  $stmt->bindParam(':pTopId', $this->topId, PDO::PARAM_STR);
	  $stmt->bindParam(':pManagerId', $this->managerId, PDO::PARAM_STR);

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
    $sql = " UPDATE emp_phist SET

pos_name=:pPosName,
sk_no=:pSkNo,
sk_subject=:pSkSubject,
sk_date=:pSkDate,
filename=:pFilename,
dir_id=:pDirId,
div_id=:pDivId,
dept_id=:pDeptId,
unit_id=:pUnitId,
prov_id=:pProvId,
city_id=:pCityId,
location=:pLocation,
grade=:pGrade,
rank=:pRank,
start_date=:pStartDate,
end_date=:pEndDate,
remark=:pRemark,
status=:pStatus,
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
top_id=:pTopId,
manager_id=:pManagerId,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);      
      $stmt->bindParam(':pPosName', $this->posName, PDO::PARAM_STR);
      $stmt->bindParam(':pSkNo', $this->skNo, PDO::PARAM_STR);
      $stmt->bindParam(':pSkSubject', $this->skSubject, PDO::PARAM_STR);
      $stmt->bindParam(':pSkDate', $this->skDate, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);
      $stmt->bindParam(':pDirId', $this->dirId, PDO::PARAM_STR);
      $stmt->bindParam(':pDivId', $this->divId, PDO::PARAM_STR);
      $stmt->bindParam(':pDeptId', $this->deptId, PDO::PARAM_STR);
      $stmt->bindParam(':pUnitId', $this->unitId, PDO::PARAM_STR);
      $stmt->bindParam(':pProvId', $this->provId, PDO::PARAM_STR);
      $stmt->bindParam(':pCityId', $this->cityId, PDO::PARAM_STR);
      $stmt->bindParam(':pLocation', $this->location, PDO::PARAM_STR);
      $stmt->bindParam(':pRank', $this->rank, PDO::PARAM_STR);
      $stmt->bindParam(':pGrade', $this->grade, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
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
      $stmt->bindParam(':pTopId', $this->topId, PDO::PARAM_STR);
      $stmt->bindParam(':pManagerId', $this->managerId, PDO::PARAM_STR);
	  
      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_phist
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
pos_name,
sk_no,
sk_subject,
sk_date,
filename,
dir_id,
div_id,
dept_id,
unit_id,
prov_id,
city_id,
location,
grade,
rank,
start_date,
end_date,
remark,
status,
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
top_id,
manager_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_phist 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->posName = $row["pos_name"];
        $this->skNo = $row["sk_no"];
        $this->skSubject = $row["sk_subject"];
        $this->skDate = $row["sk_date"];
        $this->filename = $row["filename"];
        $this->dirId = $row["dir_id"];
        $this->divId = $row["div_id"];		
        $this->deptId = $row["dept_id"];
		$this->provId = $row["prov_id"];
		$this->cityId = $row["city_id"];
        $this->location = $row["location"];
        $this->rank = $row["rank"];
        $this->grade = $row["grade"];
        $this->startDate = $row["start_date"];
        $this->endDate = $row["end_date"];
        $this->remark = $row["remark"];
        $this->status = $row["status"];
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
		$this->topId = $row["top_id"];
		$this->managerId = $row["manager_id"];


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

  function getActiveParentId() {
    $sql = " SELECT 
      
id,
parent_id,
pos_name,
sk_no,
sk_subject,
sk_date,
filename,
dir_id,
div_id,
dept_id,
unit_id,
prov_id,
city_id,
location,
grade,
rank,
start_date,
end_date,
remark,
status,
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
top_id,
manager_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_phist 
      WHERE status=1 and parent_id=:pParentId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->posName = $row["pos_name"];
        $this->skNo = $row["sk_no"];
        $this->skSubject = $row["sk_subject"];
        $this->skDate = $row["sk_date"];
        $this->filename = $row["filename"];
        $this->dirId = $row["dir_id"];
        $this->divId = $row["div_id"];
        $this->deptId = $row["dept_id"];
		$this->unitId = $row["unit_id"];
		$this->provId = $row["prov_id"];
		$this->cityId = $row["city_id"];
        $this->location = $row["location"];
        $this->rank = $row["rank"];
        $this->grade = $row["grade"];
        $this->startDate = $row["start_date"];
        $this->endDate = $row["end_date"];
        $this->remark = $row["remark"];
        $this->status = $row["status"];
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
		$this->topId = $row["top_id"];
		$this->managerId = $row["manager_id"];

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
pos_name,
sk_no,
sk_subject,
sk_date,
filename,
dir_id,
div_id,
dept_id,
unit_id,
prov_id,
city_id,
start_date,
end_date,
location,
grade,
rank,
remark,
status,
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
top_id,
manager_id,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_phist 
      WHERE parent_id=:pParentId order by year(start_date) desc
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPhist("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->posName = $row["pos_name"];
        $ret0->skNo = $row["sk_no"];
        $ret0->skSubject = $row["sk_subject"];
        $ret0->skDate = $row["sk_date"];
        $ret0->filename = $row["filename"];
        $ret0->dirId = $row["dir_id"];
        $ret0->divId = $row["div_id"];
        $ret0->deptId = $row["dept_id"];
        $ret0->unitId = $row["unit_id"];
		$ret0->provId = $row["prov_id"];
		$ret0->cityId = $row["city_id"];
        $ret0->location = $row["location"];
        $ret0->rank = $row["rank"];
        $ret0->grade = $row["grade"];
        $ret0->startDate = $row["start_date"];
        $ret0->endDate = $row["end_date"];
        $ret0->remark = $row["remark"];
        $ret0->status = $row["status"];
        $ret0->leaderId = $row["leader_id"];
        $ret0->administrationId = $row["administration_id"];
        $ret0->replacementId = $row["replacement_id"];
		$ret0->replacement2Id = $row["replacement2_id"];
		$ret0->lembur = $row["lembur"];
		$ret0->payrollId = $row["payroll_id"];
		$ret0->prosesId = $row["proses_id"];
		$ret0->groupId = $row["group_id"];
		$ret0->penilaianId = $row["penilaian_id"];
		$ret0->shiftId = $row["shift_id"];	
    $ret0->companyId = $row["company_id"];
    $ret0->kategori = $row["kategori"];
    $ret0->perdin = $row["perdin"];
    $ret0->obat = $row["obat"];
		$ret0->topId = $row["top_id"];
		$ret0->managerId = $row["manager_id"];

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
      FROM emp_phist ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPhist("C");

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

t1.parent_id parentId,
t1.pos_name posName,
t1.sk_no skNo,
t1.sk_subject skSubject,
t1.sk_date skDate,
t1.filename filename,
t1.dir_id dirId,
t1.div_id divId,
t1.dept_id deptId,
t1.unit_id unitId,
t1.prov_id provId,
t1.city_id cityId,
t1.start_date startDate,
t1.end_date endDate,
concat(year(t1.start_date),' - ',year(t1.end_date)) posYear,
t1.remark remark,
t1.status status,
t1.leader_id leaderId,
t1.administration_id administrationId,
t1.replacement_id replacementId,
t1.replacement2_id replacement2Id,
t1.kategori kategori,
t1.perdin perdin,
t1.obat obat,
t1.top_id topId,
t1.manager_id managerId,
      t2.namaData dirName,
      t3.namaData divName,
      t4.namaData deptName,
      t5.namaData unitName,
      t6.namaData rankName,
      t7.namaData gradeName,
      t8.namaData topName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_phist t1
      LEFT JOIN mst_data t2 on t1.dir_id=t2.kodeData
      LEFT JOIN mst_data t3 on t1.div_id=t3.kodeData
      LEFT JOIN mst_data t4 on t1.dept_id=t4.kodeData
      LEFT JOIN mst_data t5 on t1.unit_id=t5.kodeData
      LEFT JOIN mst_data t6 on t1.rank=t6.kodeData
      LEFT JOIN mst_data t7 on t1.grade=t7.kodeData
      LEFT JOIN mst_data t8 on t1.top_id=t8.kodeData
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
    $this->parentId = $_SESSION["curr_emp_id"];
    $cutil = new Common();
//HANDLE FILE UPLOAD KTP
   $fileSkTemp = $_FILES["filename"]["tmp_name"];
   $fileSkName = $_FILES["filename"]["name"];
   if ($fileSkTemp != "" && $fileSkTemp != "none") {
     $skPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "career" . DS;
     if (!file_exists($skPath)) {
       mkdir($skPath, "0777", true);
     }
     $skName = "career_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileSkName, PATHINFO_EXTENSION);
     if (move_uploaded_file($fileSkTemp, $skPath . $skName)) {
       $this->filename = $skName;
     }
   }
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    $cutil->execute("UPDATE emp_phist set status=0 WHERE parent_id='$this->parentId' AND id<>'$this->id'");
//    var_dump($this);
//    die();
    return $this;
  }

}

?>
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recvacancy.inc
 *
 * @author mazte
 */
class RecVacancy extends DAL {

  public $id;
  public $planId;
  public $no;
  public $fileLowongan;
  public $candidate;
  public $postStartDate;
  public $postEndDate;
  public $selStartDate;
  public $selEndDate;
  public $annStartDate;
  public $annEndDate;
  public $verStartDate;
  public $verEndDate;
  public $status;
  public $appr1Sta;
  public $appr1By;
  public $appr1Date;
  public $appr1Remark;
  public $remark;
  public $detailInfo;
  public $creBy;
  public $creDate;
  public $updBy;
  public $updDate;

  public function __construct($dbo = NULL) {
    if ($dbo == NULL) {
      parent::__construct($dbo);
    }
  }

  function insert(){
    global $db,$cUsername;

    $lastid = getField("select id from rec_selection order by id desc limit 1 ")+1;

    $this->planId = $_SESSION["plan_id"]; 

    $vac_id = getField("select id from rec_vacancy where plan_id = '".$this->planId."'");

     $sql = "insert into rec_selection (id,plan_id,vac_id,cre_by,cre_date) VALUES ('$lastid','".$this->planId."','" . $vac_id. "', '$cUsername','" . date('Y-m-d H:i:s') . "')";
    db($sql); #insert parent
  }

  function persist() {
    $sql = "INSERT INTO rec_vacancy (

plan_id,
no,
file_lowongan,
candidate,
post_start_date,
post_end_date,
sel_start_date,
sel_end_date,
ann_start_date,
ann_end_date,
ver_start_date,
ver_end_date,
status,
appr1_sta,
appr1_by,
appr1_date,
appr1_remark,
remark,
detail_info,
		
          cre_by,
          cre_date
          ) VALUES (

:pPlanId, 
:pNo, 
:pFileLowongan, 
:pCandidate, 
:pPostStartDate, 
:pPostEndDate, 
:pSelStartDate, 
:pSelEndDate, 
:pAnnStartDate, 
:pAnnEndDate, 
:pVerStartDate, 
:pVerEndDate, 
:pStatus, 
:pAppr1Sta, 
:pAppr1By, 
:pAppr1Date, 
:pAppr1Remark, 
:pRemark, 
:pDetailInfo, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);

      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pNo', $this->no, PDO::PARAM_STR);
      $stmt->bindParam(':pFileLowongan', $this->fileLowongan, PDO::PARAM_STR);
      $stmt->bindParam(':pCandidate', $this->candidate, PDO::PARAM_STR);
      $stmt->bindParam(':pPostStartDate', $this->postStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPostEndDate', $this->postEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelStartDate', $this->selStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelEndDate', $this->selEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAnnStartDate', $this->annStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAnnEndDate', $this->annEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pVerStartDate', $this->verStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pVerEndDate', $this->verEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Sta', $this->appr1Sta, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1By', $this->appr1By, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Date', $this->appr1Date, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Remark', $this->appr1Remark, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pDetailInfo', $this->detailInfo, PDO::PARAM_STR);


      $stmt->bindParam(':pCreBy', $this->creBy, PDO::PARAM_STR);
      $stmt->bindParam(':pCreDate', $this->creDate, PDO::PARAM_STR);

      $stmt->execute();
      $this->id = $this->db->lastInsertId();
      //var_dump($this->db->lastInsertId());
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function update() {
    $sql = " UPDATE rec_vacancy SET
      
plan_id=:pPlanId,
no=:pNo,
file_lowongan=:pFileLowongan,
candidate=:pCandidate,
post_start_date=:pPostStartDate,
post_end_date=:pPostEndDate,
sel_start_date=:pSelStartDate,
sel_end_date=:pSelEndDate,
ann_start_date=:pAnnStartDate,
ann_end_date=:pAnnEndDate,
ver_start_date=:pVerStartDate,
ver_end_date=:pVerEndDate,
status=:pStatus,
appr1_sta=:pAppr1Sta,
appr1_by=:pAppr1By,
appr1_date=:pAppr1Date,
appr1_remark=:pAppr1Remark,
remark=:pRemark,
detail_info=:pDetailInfo,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);

      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pNo', $this->no, PDO::PARAM_STR);
      $stmt->bindParam(':pFileLowongan', $this->fileLowongan, PDO::PARAM_STR);
      $stmt->bindParam(':pCandidate', $this->candidate, PDO::PARAM_STR);
      $stmt->bindParam(':pPostStartDate', $this->postStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pPostEndDate', $this->postEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelStartDate', $this->selStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pSelEndDate', $this->selEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAnnStartDate', $this->annStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAnnEndDate', $this->annEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pVerStartDate', $this->verStartDate, PDO::PARAM_STR);
      $stmt->bindParam(':pVerEndDate', $this->verEndDate, PDO::PARAM_STR);
      $stmt->bindParam(':pStatus', $this->status, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Sta', $this->appr1Sta, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1By', $this->appr1By, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Date', $this->appr1Date, PDO::PARAM_STR);
      $stmt->bindParam(':pAppr1Remark', $this->appr1Remark, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pDetailInfo', $this->detailInfo, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM rec_vacancy
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
plan_id,
no,
file_lowongan,
candidate,
post_start_date,
post_end_date,
sel_start_date,
sel_end_date,
ann_start_date,
ann_end_date,
ver_start_date,
ver_end_date,
status,
appr1_sta,
appr1_by,
appr1_date,
appr1_remark,
remark,
detail_info,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_vacancy 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->planId = $row["plan_id"];
        $this->no = $row["no"];
        $this->fileLowongan = $row["file_lowongan"];
        $this->candidate = $row["candidate"];
        $this->postStartDate = $row["post_start_date"];
        $this->postEndDate = $row["post_end_date"];
        $this->selStartDate = $row["sel_start_date"];
        $this->selEndDate = $row["sel_end_date"];
        $this->annStartDate = $row["ann_start_date"];
        $this->annEndDate = $row["ann_end_date"];
        $this->verStartDate = $row["ver_start_date"];
        $this->verEndDate = $row["ver_end_date"];
        $this->status = $row["status"];
        $this->appr1Sta = $row["appr1_sta"];
        $this->appr1By = $row["appr1_by"];
        $this->appr1Date = $row["appr1_date"];
        $this->appr1Remark = $row["appr1_remark"];
        $this->remark = $row["remark"];
        $this->detailInfo = $row["detail_info"];

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
plan_id,
no,
file_lowongan,
candidate,
post_start_date,
post_end_date,
sel_start_date,
sel_end_date,
ann_start_date,
ann_end_date,
ver_start_date,
ver_end_date,
status,
appr1_sta,
appr1_by,
appr1_date,
appr1_remark,
remark,
detail_info,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_vacancy 
      WHERE plan_id=:pPlanId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->planId = $row["plan_id"];
        $this->no = $row["no"];
        $this->fileLowongan = $row["file_lowongan"];
        $this->candidate = $row["candidate"];
        $this->postStartDate = $row["post_start_date"];
        $this->postEndDate = $row["post_end_date"];
        $this->selStartDate = $row["sel_start_date"];
        $this->selEndDate = $row["sel_end_date"];
        $this->annStartDate = $row["ann_start_date"];
        $this->annEndDate = $row["ann_end_date"];
        $this->verStartDate = $row["ver_start_date"];
        $this->verEndDate = $row["ver_end_date"];
        $this->status = $row["status"];
        $this->appr1Sta = $row["appr1_sta"];
        $this->appr1By = $row["appr1_by"];
        $this->appr1Date = $row["appr1_date"];
        $this->appr1Remark = $row["appr1_remark"];
        $this->remark = $row["remark"];
        $this->detailInfo = $row["detail_info"];

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
plan_id,
no,
file_lowongan,
candidate,
post_start_date,
post_end_date,
sel_start_date,
sel_end_date,
ann_start_date,
ann_end_date,
ver_start_date,
ver_end_date,
status,
appr1_sta,
appr1_by,
appr1_date,
appr1_remark,
remark,
detail_info,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_vacancy ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecVacancy("C");

        $ret0->id = $row["id"];
        $ret0->planId = $row["plan_id"];
        $ret0->no = $row["no"];
        $ret0->fileLowongan = $row["file_lowongan"];
        $ret0->candidate = $row["candidate"];
        $ret0->postStartDate = $row["post_start_date"];
        $ret0->postEndDate = $row["post_end_date"];
        $ret0->selStartDate = $row["sel_start_date"];
        $ret0->selEndDate = $row["sel_end_date"];
        $ret0->annStartDate = $row["ann_start_date"];
        $ret0->annEndDate = $row["ann_end_date"];
        $ret0->verStartDate = $row["ver_start_date"];
        $ret0->verEndDate = $row["ver_end_date"];
        $ret0->status = $row["status"];
        $ret0->appr1Sta = $row["appr1_sta"];
        $ret0->appr1By = $row["appr1_by"];
        $ret0->appr1Date = $row["appr1_date"];
        $ret0->appr1Remark = $row["appr1_remark"];
        $ret0->remark = $row["remark"];
        $ret0->detailInfo = $row["detail_info"];

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

  function loadTableReport($cyear, $cDivisi, $stDate, $edDate) {
    $sql = " SELECT
      
        t2.id planId,
        DATE_FORMAT(t2.propose_date,'%d-%m-%Y') proposeDate,
        DATE_FORMAT(t2.need_date,'%d-%m-%Y') needDate,
        t2.pos_available posAvailable,
        t2.person_needed personNeeded,

        CASE
        WHEN t1.candidate=0 THEN 'Internal'
        WHEN t1.candidate=1 THEN 'Eksternal'
        ELSE ''
        END candidateValue,
        t1.id id,
        t1.no no,
        CONCAT(t1.sel_start_date,' - ',t1.sel_end_date) selDate,
        CONCAT(t1.ann_start_date,' - ',t1.ann_end_date) annDate,
        t1.status status,
        t1.appr1_sta appr1Sta,
        t1.appr1_by appr1By,
        t1.appr1_date appr1Date,
        t1.appr1_remark appr1Remark,
        DATE_FORMAT(t1.post_start_date,'%d-%m-%Y') postStartDate,
        DATE_FORMAT(t1.post_end_date,'%d-%m-%Y') postEndDate,
        DATE_FORMAT(t1.sel_start_date,'%d-%m-%Y') selStartDate,
        DATE_FORMAT(t1.sel_end_date,'%d-%m-%Y') selEndDate,
        DATE_FORMAT(t1.ann_start_date,'%d-%m-%Y') annStartDate,
        DATE_FORMAT(t1.ann_end_date,'%d-%m-%Y') annEndDate,
        DATE_FORMAT(t1.ver_start_date,'%d-%m-%Y') verStartDate,
        DATE_FORMAT(t1.ver_end_date,'%d-%m-%Y') verEndDate,

        t1.cre_by creBy,
        t1.cre_date creDate,
        t1.upd_by updBy,
        t1.upd_date updDate
        FROM
            rec_vacancy t1
        RIGHT OUTER JOIN rec_plan t2 ON t1.plan_id=t2.id
        WHERE t2.appr_sdm_sta=3
        AND YEAR(t2.propose_date)='$cyear'
      ";

    if ($cDivisi != "") {
      $sql.=" AND t2.div_id=$cDivisi";
    }
    if (!empty($stDate) && !empty($edDate)) {
      $sql.=" AND t2.propose_date BETWEEN '$stDate' AND '$edDate' ";
    }
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

  function exportToExcelReport($cyear, $cDivisi, $stDate, $edDate) {
    $sql = " SELECT
        t1.no `Nomor`,
        t2.pos_available `Jabatan`,
        t2.person_needed `Jumlah`,
        CASE
        WHEN t1.candidate=0 THEN 'Internal'
        WHEN t1.candidate=1 THEN 'Eksternal'
        ELSE ''
        END `Kandidat`,
        DATE_FORMAT(t2.propose_date,'%d-%m-%Y') `Tgl. Pengajuan`,
        DATE_FORMAT(t2.need_date,'%d-%m-%Y') `Tgl. Kebutuhan`,
        CONCAT(DATE_FORMAT(t1.sel_start_date,'%d-%m-%Y'),' - ',DATE_FORMAT(t1.sel_end_date,'%d-%m-%Y')) `Tgl. Seleksi`,
        CONCAT(DATE_FORMAT(t1.ann_start_date,'%d-%m-%Y'),' - ',DATE_FORMAT(t1.ann_end_date,'%d-%m-%Y')) `Tgl. Pengumuman`,
        CASE 
          WHEN t1.status=1 THEN 'Seleksi'
          WHEN t1.status=2 THEN 'Batal'
          WHEN t1.status=3 THEN 'Selesai'
          ELSE 'Rencana'
        END `Status`,
        CASE 
          WHEN t1.appr1_sta=1 THEN 'Ditolak'
          WHEN t1.appr1_sta=2 THEN 'Pending'
          WHEN t1.appr1_sta=3 THEN 'Disetujui'
          ELSE 'Belum ada approval'
        END `Approval`,
        t1.appr1_by `Approve By`,
        DATE_FORMAT(t1.appr1_date,'%d-%m-%Y') `Tgl. Approve`,
        t1.appr1_remark `Keterangan`
        FROM
        rec_vacancy t1
        RIGHT OUTER JOIN rec_plan t2 ON t1.plan_id=t2.id
        WHERE t2.appr_sdm_sta=3
        AND YEAR(t2.propose_date)='$cyear'
      ";

    if ($cDivisi != "") {
      $sql.=" AND t2.div_id=$cDivisi";
    }
    if (!empty($stDate) && !empty($edDate)) {
      $sql.=" AND t2.propose_date BETWEEN '$stDate' AND '$edDate' ";
    }
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

  function loadTable($ctype, $cyear) {
    $sql = " SELECT
      
        t2.id planId,
        DATE_FORMAT(t2.propose_date,'%d-%m-%Y') proposeDate,
        DATE_FORMAT(t2.need_date,'%d-%m-%Y') needDate,
        CASE WHEN t2.cat IN('1','2') THEN t2.subject ELSE t2.pos_available END AS posAvailable,
        t2.person_needed personNeeded,

        CASE
        WHEN t1.candidate=0 THEN 'Internal'
        WHEN t1.candidate=1 THEN 'Eksternal'
        ELSE ''
        END candidateValue,
        t1.id id,
        t1.no no,
        CONCAT(t1.sel_start_date,' - ',t1.sel_end_date) selDate,
        CONCAT(t1.ann_start_date,' - ',t1.ann_end_date) annDate,
        t1.status status,
        t1.appr1_sta appr1Sta,
        t1.appr1_by appr1By,
        t1.appr1_date appr1Date,
        t1.appr1_remark appr1Remark,
        t1.post_start_date postStartDate,
      	 t1.post_end_date postEndDate,
        DATE_FORMAT(t1.sel_start_date,'%d-%m-%Y') selStartDate,
        t1.sel_end_date selEndDate,
        DATE_FORMAT(t1.ann_start_date,'%d-%m-%Y') annStartDate,
        t1.ann_end_date annEndDate,
        t1.ver_start_date verStartDate,
        t1.ver_end_date verEndDate,

        t1.cre_by creBy,
        t1.cre_date creDate,
        t1.upd_by updBy,
        t1.upd_date updDate
        FROM
            rec_vacancy t1
        RIGHT OUTER JOIN rec_plan t2 ON t1.plan_id=t2.id
        WHERE t2.sts=3
        AND YEAR(t2.propose_date)='$cyear' AND t2.cat !='3'
      ";

    if ($ctype != "") {
      $sql.=" AND t2.cat=$ctype";
    }
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

  function generateRecNo() {
    $sql = "
      SELECT COALESCE(MAX(CAST(SUBSTR(a.no,1,4) AS UNSIGNED)),0)+1 rec_no
      FROM rec_vacancy a
      WHERE YEAR(a.cre_date)=YEAR(CURRENT_DATE())
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pCat', $this->cat);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $ret = $row["rec_no"];
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }
function processForm() {
    $this->id = $_SESSION["entity_id"];
    $this->planId = $_SESSION["plan_id"];
    $this->getById();
    $this->populateWithPost();

    //HANDLE FILE UPLOAD VACANCY
    $file0Tmp = $_FILES["fileLowongan"]["tmp_name"];
    $file0Name = $_FILES["fileLowongan"]["name"];
    if ($file0Tmp != "" && $file0Tmp != "none") {
      $file0Path = HOME_DIR . DS . "files" . DS . "recruit" . DS . "vacancy" . DS;
      if (!file_exists($file0Path)) {
        mkdir($file0Path, "0777", true);
      }
      $file0Name = "vacancy_" . date("Ymd_His") . "_" . $this->id . "." . pathinfo($file0Name, PATHINFO_EXTENSION);
      if (move_uploaded_file($file0Tmp, $file0Path . $file0Name)) {
        $this->fileLowongan = $file0Name;
      }
    }
    if ($this->id == "") {
      $recNo = $this->generateRecNo();
      $this->no = str_pad($recNo, 4, "0", STR_PAD_LEFT) . "/RP/" . getRomawi(date("n")) . "/" . date("Y");
      $this->persist();
      $this->insert();
    } else {
      $this->update();
    }
    return $this;
  }

}

?>
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_pwork.inc.php
 *
 * @author mazte
 */
class EmpPwork extends DAL {

  public $id;
  public $parentId;
  public $companyName;
  public $position;
  public $startDate;
  public $endDate;
  public $division;
  public $dept;
  public $city;
  public $jobDesc;
  public $responsibility;
  public $filename;
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
    $sql = "INSERT INTO emp_pwork (
      
parent_id,
company_name,
position,
start_date,
end_date,
division,
dept,
city,
job_desc,
responsibility,
filename,
remark,
status,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pCompanyName, 
:pPosition, 
:pStartDate, 
:pEndDate, 
:pDivision, 
:pDept, 
:pCity, 
:pJobDesc, 
:pResponsibility, 
:pFilename, 
:pRemark, 
:pStatus, 


          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->startDate = setTanggal($this->startDate);
      $this->endDate = setTanggal($this->endDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pCompanyName', $this->companyName, PDO::PARAM_STR);
      $stmt->bindParam(':pPosition', $this->position, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pDivision', $this->division, PDO::PARAM_STR);
      $stmt->bindParam(':pDept', $this->dept, PDO::PARAM_STR);
      $stmt->bindParam(':pCity', $this->city, PDO::PARAM_STR);
      $stmt->bindParam(':pJobDesc', $this->jobDesc, PDO::PARAM_STR);
      $stmt->bindParam(':pResponsibility', $this->responsibility, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_pwork SET

parent_id=:pParentId,
company_name=:pCompanyName,
position=:pPosition,
start_date=:pStartDate,
end_date=:pEndDate,
division=:pDivision,
dept=:pDept,
city=:pCity,
job_desc=:pJobDesc,
responsibility=:pResponsibility,
filename=:pFilename,
remark=:pRemark,
status=:pStatus,


          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
		  
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
       $this->startDate = setTanggal($this->startDate);
      $this->endDate = setTanggal($this->endDate);
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pCompanyName', $this->companyName, PDO::PARAM_STR);
      $stmt->bindParam(':pPosition', $this->position, PDO::PARAM_STR);
      $stmt->bindParam(':pStartDate', $this->startDate, PDO::PARAM_STR);
      $stmt->bindParam(':pEndDate', $this->endDate, PDO::PARAM_STR);
      $stmt->bindParam(':pDivision', $this->division, PDO::PARAM_STR);
      $stmt->bindParam(':pDept', $this->dept, PDO::PARAM_STR);
      $stmt->bindParam(':pCity', $this->city, PDO::PARAM_STR);
      $stmt->bindParam(':pJobDesc', $this->jobDesc, PDO::PARAM_STR);
      $stmt->bindParam(':pResponsibility', $this->responsibility, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_pwork
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
company_name,
position,
start_date,
end_date,
division,
dept,
city,
job_desc,
filename,
responsibility,
remark,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_pwork 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->companyName = $row["company_name"];
        $this->position = $row["position"];
        $this->startDate = $row["start_date"];
        $this->endDate = $row["end_date"];
        $this->division = $row["division"];
        $this->filename = $row["filename"];
        $this->dept = $row["dept"];
        $this->city = $row["city"];
        $this->jobDesc = $row["job_desc"];
        $this->responsibility = $row["responsibility"];
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
company_name,
position,
start_date,
end_date,
division,
dept,
city,
job_desc,
responsibility,
filename,
remark,
status,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_pwork 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPwork("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->companyName = $row["company_name"];
        $ret0->position = $row["position"];
        $ret0->startDate = $row["start_date"];
        $ret0->endDate = $row["end_date"];
        $ret0->division = $row["division"];
        $ret0->filename = $row["filename"];
        $ret0->dept = $row["dept"];
        $ret0->city = $row["city"];
        $ret0->jobDesc = $row["job_desc"];
        $ret0->responsibility = $row["responsibility"];
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
      FROM emp_pwork ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPwork("C");

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
t1.company_name companyName,
t1.position position,
t1.start_date startDate,
t1.end_date endDate,
t1.division division,
t1.dept dept,
t1.filename filename,
t1.city city,
t1.job_desc jobDesc,
concat(year(t1.start_date),' - ',year(t1.end_date))  dtRange,
t1.responsibility responsibility,
t1.remark remark,
t1.status status,


      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_pwork t1
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
    $this->companyName = strtoupper($this->companyName);
    $fileKtpTmp = $_FILES["filename"]["tmp_name"];
    $fileKtpName = $_FILES["filename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "edu" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "pwo_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->filename = $fktpName;
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
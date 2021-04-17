<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_edu.inc.php
 *
 * @author mazte
 */
class EmpEdu extends DAL {

  public $id;
  public $parentId;
  public $eduType;
  public $eduName;
  public $eduCity;
  public $eduYear;
  public $eduFac;
  public $eduDept;
  public $eduEssay;
  public $eduFilename;
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
    $sql = "INSERT INTO emp_edu (
      
parent_id,
edu_type,
edu_name,
edu_city,
edu_year,
edu_fac,
edu_dept,
edu_essay,
edu_filename,
remark,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pEduType, 
:pEduName, 
:pEduCity, 
:pEduYear, 
:pEduFac, 
:pEduDept, 
:pEduEssay, 
:pEduFilename, 
:pRemark, 

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
      $stmt->bindParam(':pEduType', $this->eduType, PDO::PARAM_STR);
      $stmt->bindParam(':pEduName', $this->eduName, PDO::PARAM_STR);
      $stmt->bindParam(':pEduCity', $this->eduCity, PDO::PARAM_STR);
      $stmt->bindParam(':pEduYear', $this->eduYear, PDO::PARAM_STR);
      $stmt->bindParam(':pEduFac', $this->eduFac, PDO::PARAM_STR);
      $stmt->bindParam(':pEduDept', $this->eduDept, PDO::PARAM_STR);
      $stmt->bindParam(':pEduEssay', $this->eduEssay, PDO::PARAM_STR);
      $stmt->bindParam(':pEduFilename', $this->eduFilename, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_edu SET

parent_id=:pParentId,
edu_type=:pEduType,
edu_name=:pEduName,
edu_city=:pEduCity,
edu_year=:pEduYear,
edu_fac=:pEduFac,
edu_dept=:pEduDept,
edu_essay=:pEduEssay,
edu_filename=:pEduFilename,
remark=:pRemark,

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
      $stmt->bindParam(':pEduType', $this->eduType, PDO::PARAM_STR);
      $stmt->bindParam(':pEduName', $this->eduName, PDO::PARAM_STR);
      $stmt->bindParam(':pEduCity', $this->eduCity, PDO::PARAM_STR);
      $stmt->bindParam(':pEduYear', $this->eduYear, PDO::PARAM_STR);
      $stmt->bindParam(':pEduFac', $this->eduFac, PDO::PARAM_STR);
      $stmt->bindParam(':pEduDept', $this->eduDept, PDO::PARAM_STR);
      $stmt->bindParam(':pEduEssay', $this->eduEssay, PDO::PARAM_STR);
      $stmt->bindParam(':pEduFilename', $this->eduFilename, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_edu
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
edu_type,
edu_name,
edu_city,
edu_year,
edu_fac,
edu_dept,
edu_essay,
edu_filename,
remark,



      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_edu 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->eduType = $row["edu_type"];
        $this->eduName = $row["edu_name"];
        $this->eduCity = $row["edu_city"];
        $this->eduYear = $row["edu_year"];
        $this->eduFac = $row["edu_fac"];
        $this->eduDept = $row["edu_dept"];
        $this->eduEssay = $row["edu_essay"];
        $this->eduFilename = $row["edu_filename"];
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
edu_type,
edu_name,
edu_city,
edu_year,
edu_fac,
edu_dept,
edu_essay,
edu_filename,
remark,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_edu 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpEdu("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->eduType = $row["edu_type"];
        $ret0->eduName = $row["edu_name"];
        $ret0->eduCity = $row["edu_city"];
        $ret0->eduYear = $row["edu_year"];
        $ret0->eduFac = $row["edu_fac"];
        $ret0->eduDept = $row["edu_dept"];
        $ret0->eduEssay = $row["edu_essay"];
        $ret0->eduFilename = $row["edu_filename"];
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
      FROM emp_edu ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpEdu("C");

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
t1.edu_type eduType,
t1.edu_name eduName,
t1.edu_city eduCity,
t1.edu_year eduYear,
t1.edu_fac eduFac,
t1.edu_dept eduDept,
t1.edu_essay eduEssay,
t1.edu_filename eduFilename,
t1.remark remark,

      t2.namaData levelName,
      t3.namaData facName,
      t4.namaData deptName,
      t5.namaData cityName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_edu t1
      LEFT JOIN mst_data t2 on t1.edu_type=t2.kodeData
      LEFT JOIN mst_data t3 on t1.edu_fac=t3.kodeData
      LEFT JOIN mst_data t4 on t1.edu_dept=t4.kodeData
      LEFT JOIN mst_data t5 on t1.edu_city=t5.kodeData
      WHERE parent_id=:pParentId
      ORDER BY t1.edu_type
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
    $this->parentId = $_SESSION["curr_emp_id"];
    $this->eduName = strtoupper($this->eduName);
    $fileKtpTmp = $_FILES["eduFilename"]["tmp_name"];
    $fileKtpName = $_FILES["eduFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "edu" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "edu_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->eduFilename = $fktpName;
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
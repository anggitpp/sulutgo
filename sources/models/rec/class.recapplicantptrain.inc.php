<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.rec_applicant_pwork.inc.php
 *
 * @author mazte
 */
class RecApplicantPtrain extends DAL {

  public $id;
  public $parentId;
  public $name;
  public $training;
  public $position;
  public $start;
  public $ends;
  public $location;
  public $filename;
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
    $sql = "INSERT INTO rec_applicant_ptraining (

      parent_id,
      name,
      training,
      position,
      start,
      ends,
      location,
      filename,
      remark,
      

      cre_by,
      cre_date
      ) VALUES (

      :pParentId, 
      :pName,  
      :pTraining,  
      :pPosition,  
      :pStart,  
      :pEnds,  
      :pLocation,  
      :pFilename, 
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
  $stmt->bindParam(':pName', $this->name, PDO::PARAM_STR);
  $stmt->bindParam(':pTraining', $this->training, PDO::PARAM_STR);
  $stmt->bindParam(':pPosition', $this->position, PDO::PARAM_STR);
  $stmt->bindParam(':pStart', $this->start, PDO::PARAM_STR);
  $stmt->bindParam(':pEnds', $this->ends, PDO::PARAM_STR);
  $stmt->bindParam(':pLocation', $this->location, PDO::PARAM_STR);
  $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);
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
  $sql = " UPDATE rec_applicant_ptraining SET

  parent_id parentId,
  name name,
  training training,
  position position,
  start start,
  ends ends,
  location location,
  filename filename,
  remark remark,
  


  upd_by=:pUpdBy,
  upd_date=:pUpdDate
  WHERE id=:pId";
  // echo $sql;
  try {
    global $db,$cUsername;
    date_default_timezone_set('Asia/Jakarta');
    $this->updBy = $cUsername;
    $this->updDate = date('Y-m-d H:i:s');
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam('ParentId', $this->parentId, PDO::PARAM_STR);
    $stmt->bindParam('Name', $this->name, PDO::PARAM_STR);
    $stmt->bindParam('Training', $this->training, PDO::PARAM_STR);
    $stmt->bindParam('Position', $this->position, PDO::PARAM_STR);
    $stmt->bindParam('Start', $this->start, PDO::PARAM_STR);
    $stmt->bindParam('Ends', $this->ends, PDO::PARAM_STR);
    $stmt->bindParam('Location', $this->location, PDO::PARAM_STR);
    $stmt->bindParam('Filename', $this->filename, PDO::PARAM_STR);
    $stmt->bindParam('Remark', $this->remark, PDO::PARAM_STR);
    $stmt->bindParam('UpdBy', $this->updBy, PDO::PARAM_STR);
    $stmt->bindParam('UpdDate', $this->updDate, PDO::PARAM_STR);
    $stmt->bindParam('Id', $this->id, PDO::PARAM_STR);
    $stmt->execute();
  } catch (Exception $ex) {
    var_dump($ex);
  }
}

function destroy() {
  $sql = " DELETE FROM rec_applicant_ptraining
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
  name,
  training,
  position,
  start,
  ends,
  location,
  filename,
  remark,

  cre_by,
  cre_date,
  upd_by,
  upd_date
  FROM rec_applicant_ptraining 
  WHERE id=:pId";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {

      $this->id = $row["id"];
      $this->parentId = $row["parent_id"];
      $this->name = $row["name"];
      $this->training = $row["training"];
      $this->position = $row["position"];
      $this->start = $row["start"];
      $this->ends = $row["ends"];
      $this->location = $row["location"];
      $this->filename = $row["filename"];
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
  name,
  training,
  position,
  start,
  ends,
  location,
  filename,
  remark,
  


  cre_by,
  cre_date,
  upd_by,
  upd_date
  FROM rec_applicant_ptraining 
  WHERE parent_id=:pParentId
  ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ret = array();
    foreach ($result as $row) {
      $ret0 = new RecApplicantPfile("C");
      $ret0->id = $row["id"];
      $ret0->parentId = $row["parent_id"];
      $ret0->name = $row["name"];
      $ret0->training = $row["training"];
      $ret0->position = $row["position"];
      $ret0->start = $row["start"];
      $ret0->ends = $row["ends"];
      $ret0->location = $row["location"];
      $ret0->filename = $row["filename"];
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
  FROM rec_applicant_ptraining ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ret = array();
    foreach ($result as $row) {
      $ret0 = new RecApplicantPfile("C");

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
  t1.name name,
  t1.training training,
  t1.position position,
  t1.start start,
  t1.ends ends,
  t1.location location,
  t1.filename filename,
  t1.remark remark,
  t1.cre_by creBy,
  t1.cre_date creDate,
  t1.upd_by updBy,
  t1.upd_date updDate
  FROM rec_applicant_ptraining t1
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

function getPtrainByParentId($parentId) {
  $sql = " SELECT

  t1.id id,
  t1.parent_id parentId,
  t1.name name,
  t1.training training,
  t1.position position,
  t1.start start,
  t1.ends ends,
  t1.location location,
  t1.filename filename,
  t1.remark remark,
  CONCAT(TIMESTAMPDIFF(YEAR,  t1.start, t1.ends),' thn ', TIMESTAMPDIFF(MONTH, t1.start,  t1.ends) % 12, ' bln') lamaTrain,

  t1.cre_by creBy,
  t1.cre_date creDate,
  t1.upd_by updBy,
  t1.upd_date updDate
  FROM rec_applicant_ptraining t1
  WHERE parent_id=:pParentId

  ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
}

function getEmpPtrainByParentId($parentId) {
  $sql = " SELECT

  t1.id id,
  t1.parent_id parentId,
  t1.name name,
  t1.training training,
  t1.position position,
  t1.start start,
  t1.ends ends,
  t1.name name,
  t1.name name,
  t1.filename filename,
  t1.remark remark,
  CONCAT(TIMESTAMPDIFF(YEAR,  t1.start, t1.ends),' thn ', TIMESTAMPDIFF(MONTH, t1.start,  t1.ends) % 12, ' bln') lamaTrain,


  t1.cre_by creBy,
  t1.cre_date creDate,
  t1.upd_by updBy,
  t1.upd_date updDate
  FROM rec_applicant_ptraining t1
  WHERE parent_id=:pParentId

  ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
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
//    $this->id = $_SESSION["entity_id"];
//    $this->getById();
//    $this->populateWithPost();
//    $this->parentId = $_SESSION["curr_emp_id"];
//    $this->companyName = strtoupper($this->companyName);
  $fileFileTmp = $_FILES["filename"]["tmp_name"];
  $fileFileName = $_FILES["filename"]["name"];
  if ($fileFileTmp != "" && $fileFileTmp != "none") {
    $filePath = HOME_DIR . DS . "files" . DS . "recruit" . DS . "pfile" . DS;
    if (!file_exists($filePath)) {
      mkdir($filePath, "0777", true);
    }
    $ffileName = uniqid("appl_file_", true) . "." . pathinfo($fileFileName, PATHINFO_EXTENSION);
    if (move_uploaded_file($fileFileTmp, $filePath . $ffileName)) {
      return$ffileName;
    }
  }
//    if ($this->id == "") {
//      $this->persist();
//    } else {
//      $this->update();
//    }
//    var_dump($this);
//    die();
  return "";
}

}

?>
 <?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.rec_applicant_pwork.inc.php
 *
 * @author mazte
 */
class RecSelectionFile extends DAL {

  public $id;
  public $parentId;
  public $name;
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
    $sql = "INSERT INTO rec_selection_appl_file (

      parent_id,
      name,
      filename,
      remark,
      

      cre_by,
      cre_date
      ) VALUES (

      :pParentId, 
      :pName,  
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
  $sql = " UPDATE rec_selection_appl_file SET

  parent_id parentId,
  name name,
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
    $stmt->bindParam('name', $this->name, PDO::PARAM_STR);
    $stmt->bindParam('filename', $this->filename, PDO::PARAM_STR);
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
  $sql = " DELETE FROM rec_selection_appl_file
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
  filename,
  remark,

  cre_by,
  cre_date,
  upd_by,
  upd_date
  FROM rec_selection_appl_file 
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
  filename,
  remark,
  


  cre_by,
  cre_date,
  upd_by,
  upd_date
  FROM rec_selection_appl_file 
  WHERE parent_id=:pParentId
  ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ret = array();
    foreach ($result as $row) {
      $ret0 = new RecApplicantPtrain("C");
      $ret0->id = $row["id"];
      $ret0->parentId = $row["parent_id"];
      $ret0->name = $row["name"];
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
  FROM rec_selection_appl_file ";
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ret = array();
    foreach ($result as $row) {
      $ret0 = new RecApplicantPtrain("C");

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
  t1.filename filename,
  t1.remark remark,
  t1.cre_by creBy,
  t1.cre_date creDate,
  t1.upd_by updBy,
  t1.upd_date updDate
  FROM rec_selection_appl_file t1
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

function getSelByParentId($parentId) {
  $sql = " SELECT

  t1.id id,
  t1.parent_id parentId,
  t1.name name,
  t1.filename filename,
  t1.remark remark,
  t1.cre_by creBy,
  t1.cre_date creDate,
  t1.upd_by updBy,
  t1.upd_date updDate
  FROM rec_selection_appl_file t1
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

// function getEmpPtrainByParentId($parentId) {
//   $sql = " SELECT

//   t1.id id,
//   t1.parent_id parentId,
//   t1.name name,
//   t1.filename filename,
//   t1.remark remark,


//   t1.cre_by creBy,
//   t1.cre_date creDate,
//   t1.upd_by updBy,
//   t1.upd_date updDate
//   FROM rec_selection_appl_file t1
//   WHERE parent_id=:pParentId

//   ";
//   try {
//     $stmt = $this->db->prepare($sql);
//     $stmt->bindParam(':pParentId', $parentId, PDO::PARAM_STR);
//     $stmt->execute();
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     $stmt->closeCursor();
//   } catch (PDOException $ex) {
//     var_dump($ex->errorInfo);
//   }
//   return $result;
// }

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
  $fileSelTmp = $_FILES["filename"]["tmp_name"];
  $fileSelName = $_FILES["filename"]["name"];
  if ($fileSelTmp != "" && $fileSelTmp != "none") {
    $selPath = HOME_DIR . DS . "files" . DS . "recruit" . DS . "selection" . DS;
    if (!file_exists($selPath)) {
      mkdir($selPath, "0777", true);
    }
    $fname = uniqid("sel_file_", true) . "." . pathinfo($fileSelName, PATHINFO_EXTENSION);
    if (move_uploaded_file($fileSelTmp, $selPath . $fname)) {
      return$fname;
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
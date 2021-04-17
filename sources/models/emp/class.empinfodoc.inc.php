<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.empinfodoc.inc
 *
 * @author mazte
 */
class EmpInfoDoc extends DAL {

  public $id;
  public $cat;
  public $subject;
  public $remark;
  public $fileDocument;
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
    $sql = "INSERT INTO emp_info_doc (
		
cat,
subject,
remark,
file_document,
status,

          cre_by,
          cre_date
          ) VALUES (
:pCat, 
:pSubject, 
:pRemark, 
:pFileDocument, 
:pStatus, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pCat', $this->cat, PDO::PARAM_STR);
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileDocument', $this->fileDocument, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_info_doc SET
      
cat=:pCat,
subject=:pSubject,
remark=:pRemark,
file_document=:pFileDocument,
status=:pStatus,

          upd_by=:pUpdBy,
          upd_date=:pUpdDate
          WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);

      $stmt->bindParam(':pCat', $this->cat, PDO::PARAM_STR);
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileDocument', $this->fileDocument, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_info_doc
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
cat,
subject,
remark,
file_document,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_info_doc 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->cat = $row["cat"];
        $this->subject = $row["subject"];
        $this->remark = $row["remark"];
        $this->fileDocument = $row["file_document"];
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

  function getAll() {
    $sql = " SELECT 
id,
cat,
subject,
remark,
file_document,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_info_doc ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpInfoDoc("C");
        $ret0->id = $row["id"];
        $ret0->cat = $row["cat"];
        $ret0->subject = $row["subject"];
        $ret0->remark = $row["remark"];
        $ret0->fileDocument = $row["file_document"];
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

  function loadTable() {
    $sql = " SELECT
t1.id id,
t1.cat cat,
t1.subject subject,
t1.remark remark,
t1.file_document fileDocument,
t1.status status,
t2.namaData catName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_info_doc t1 
      LEFT JOIN mst_data t2 ON t2.kodeData=t1.cat
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

    $fileDocTmp = $_FILES["fileDocument"]["tmp_name"];
    $fileDocName = $_FILES["fileDocument"]["name"];
    if ($fileDocTmp != "" && $fileDocTmp != "none") {
      $docPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "doc" . DS;
      if (!file_exists($docPath)) {
        mkdir($docPath, "0777", true);
      }
      $fdocName = "doc_" . date("Ymd_His") . "_" . $this->id . "." . pathinfo($fileDocName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileDocTmp, $docPath . $fdocName)) {
        $this->fileDocument = $fdocName;
      }
    }

    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    return $this;
  }

}

?>
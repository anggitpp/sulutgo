<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_plafon.inc.php
 *
 * @author mazte
 */
class EmpPlaf extends DAL {

  public $id;
  public $parentId;
  public $plafonId;
  public $satuanId;
  public $plafonValue;
  public $satuanPos;
  public $remark;
  public $mulai;
  public $selesai;
  public $toleransi;
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
    $sql = "INSERT INTO emp_plafon (
      
parent_id,
plafon_id,
satuan_id,
plafon_value,
satuan_pos,
remark,
mulai,
selesai,
toleransi,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pPlafonId, 
:pSatuanId, 
:pPlafonValue, 
:pSatuanPos, 
:pRemark, 
:pMulai,
:pSelesai,
:pToleransi,


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
      $stmt->bindParam(':pPlafonId', $this->plafonId, PDO::PARAM_STR);
      $stmt->bindParam(':pSatuanId', $this->satuanId, PDO::PARAM_STR);
      $stmt->bindParam(':pPlafonValue', $this->plafonValue, PDO::PARAM_STR);
      $stmt->bindParam(':pSatuanPos', $this->satuanPos, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
	  $stmt->bindParam(':pMulai', $this->mulai, PDO::PARAM_STR);
	  $stmt->bindParam(':pSelesai', $this->selesai, PDO::PARAM_STR);
	  $stmt->bindParam(':pToleransi', $this->toleransi, PDO::PARAM_STR);

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
    $sql = " UPDATE emp_plafon SET

parent_id=:pParentId,
plafon_id=:pPlafonId,
satuan_id=:pSatuanId,
plafon_value=:pPlafonValue,
satuan_pos=:pSatuanPos,
remark=:pRemark,
mulai=:pMulai,
selesai=:pSelesai,
toleransi=:pToleransi,



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
      $stmt->bindParam(':pPlafonId', $this->plafonId, PDO::PARAM_STR);
      $stmt->bindParam(':pSatuanId', $this->satuanId, PDO::PARAM_STR);
      $stmt->bindParam(':pPlafonValue', $this->plafonValue, PDO::PARAM_STR);
      $stmt->bindParam(':pSatuanPos', $this->satuanPos, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
	  $stmt->bindParam(':pMulai', $this->mulai, PDO::PARAM_STR);
	  $stmt->bindParam(':pSelesai', $this->selesai, PDO::PARAM_STR);
	  $stmt->bindParam(':pToleransi', $this->toleransi, PDO::PARAM_STR);



      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_plafon
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
plafon_id,
satuan_id,
plafon_value,
satuan_pos,
remark,
mulai,
selesai,
toleransi,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_plafon 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->plafonId = $row["plafon_id"];
        $this->satuanId = $row["satuan_id"];
        $this->plafonValue = $row["plafon_value"];
        $this->satuanPos = $row["satuan_pos"];
        $this->remark = $row["remark"];
		$this->mulai = $row["mulai"];
		$this->selesai = $row["selesai"];
		$this->toleransi = $row["toleransi"];

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
plafon_id,
satuan_id,
plafon_value,
satuan_pos,
remark,
mulai,
selesai,
toleransi,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_plafon 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPlaf("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->plafonId = $row["plafon_id"];
        $ret0->satuanId = $row["satuan_id"];
        $ret0->plafonValue = $row["plafon_value"];
        $ret0->satuanPos = $row["satuan_pos"];
        $ret0->remark = $row["remark"];
		$ret0->mulai = $row["mulai"];
		$ret0->selesai = $row["selesai"];
		$ret0->toleransi = $row["toleransi"];

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
      FROM emp_plafon ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpPlaf("C");

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
t1.plafon_id plafonId,
t1.satuan_id satuanId,
t1.plafon_value plafonValue,
t1.satuan_pos satuanPos,
t1.remark remark,
t1.mulai mulai,
t1.selesai selesai,
t1.toleransi toleransi,
CASE
WHEN t1.satuan_pos='d' THEN CONCAT(t3.satName,' ',format(t1.plafon_value,0,'ID_id'))
WHEN t1.satuan_pos='d' THEN CONCAT(format(t1.plafon_value,0,'ID_id'),' ',t3.satName)
ELSE format(t1.plafon_value,0,'ID_id')
END satuanText,


      t2.namaData plafName,
      t3.namaData satName,
      
      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_plafon t1
      LEFT JOIN mst_data t2 on t1.plafon_id=t2.kodeData
      LEFT JOIN mst_data t3 on t1.satuan_id=t3.kodeData
      
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
//    //HANDLE FILE UPLOAD KTP
//    $fileKtpTmp = $_FILES["skFilename"]["tmp_name"];
//    $fileKtpName = $_FILES["skFilename"]["name"];
//    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
//      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "career" . DS;
//      if (!file_exists($ktpPath)) {
//        mkdir($ktpPath, "0777", true);
//      }
//      $fktpName = "career_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
//      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
//        $this->skFilename = $fktpName;
//      }
//    }
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    $cutil->execute("UPDATE emp_plafon set status=0 WHERE parent_id='$this->parentId' AND id<>'$this->id'");
//    var_dump($this);
//    die();
    return $this;
  }

}

?>
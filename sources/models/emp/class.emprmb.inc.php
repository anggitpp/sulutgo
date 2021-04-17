<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_rmb.inc.php
 *
 * @author mazte
 */
class EmpRmb extends DAL {

  public $id;
  public $parentId;
  public $rmbJenis;  
  public $rmbNo;
  public $rmbDate;
  public $rmbCat;
  public $rmbType;
  public $rmbDateStart;
  public $rmbDateEnd;
  public $rmbVal;
  public $aprBy;
  public $aprDate;
  public $aprRemark;
  public $payDate;
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
    $sql = "INSERT INTO emp_rmb (
      
parent_id,
rmb_jenis,
rmb_no,
rmb_date,
rmb_cat,
rmb_type,
rmb_date_start,
rmb_date_end,
rmb_val,
apr_by,
apr_date,
apr_remark,
pay_date,
remark,
status,

          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pRmbJenis, 
:pRmbNo, 
:pRmbDate, 
:pRmbCat, 
:pRmbType, 
:pRmbDateStart, 
:pRmbDateEnd, 
:pRmbVal, 
:pAprBy, 
:pAprDate, 
:pAprRemark, 
:pPayDate, 
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
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
	  $stmt->bindParam(':pRmbJenis', $this->rmbJenis, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbNo', $this->rmbNo, PDO::PARAM_STR);	  
      $stmt->bindParam(':pRmbDate', $this->rmbDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbCat', $this->rmbCat, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbType', $this->rmbType, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbDateStart', $this->rmbDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbDateEnd', $this->rmbDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbVal', $this->rmbVal, PDO::PARAM_STR);
      $stmt->bindParam(':pAprBy', $this->aprBy, PDO::PARAM_STR);
      $stmt->bindParam(':pAprDate', $this->aprDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAprRemark', $this->aprRemark, PDO::PARAM_STR);
      $stmt->bindParam(':pPayDate', $this->payDate, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_rmb SET
      
parent_id=:pParentId,
rmb_no=:pRmbNo,
rmb_date=:pRmbDate,
rmb_cat=:pRmbCat,
rmb_type=:pRmbType,
rmb_date_start=:pRmbDateStart,
rmb_date_end=:pRmbDateEnd,
rmb_val=:pRmbVal,
apr_by=:pAprBy,
apr_date=:pAprDate,
apr_remark=:pAprRemark,
pay_date=:pPayDate,
remark=:pRemark,
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

      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbNo', $this->rmbNo, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbDate', $this->rmbDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbCat', $this->rmbCat, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbType', $this->rmbType, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbDateStart', $this->rmbDateStart, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbDateEnd', $this->rmbDateEnd, PDO::PARAM_STR);
      $stmt->bindParam(':pRmbVal', $this->rmbVal, PDO::PARAM_STR);
      $stmt->bindParam(':pAprBy', $this->aprBy, PDO::PARAM_STR);
      $stmt->bindParam(':pAprDate', $this->aprDate, PDO::PARAM_STR);
      $stmt->bindParam(':pAprRemark', $this->aprRemark, PDO::PARAM_STR);
      $stmt->bindParam(':pPayDate', $this->payDate, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_rmb
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
rmb_no,
rmb_date,
rmb_cat,
rmb_type,
rmb_date_start,
rmb_date_end,
rmb_val,
apr_by,
apr_date,
apr_remark,
pay_date,
remark,
status,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_rmb 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->rmbNo = $row["rmb_no"];
        $this->rmbDate = $row["rmb_date"];
        $this->rmbCat = $row["rmb_cat"];
        $this->rmbType = $row["rmb_type"];
        $this->rmbDateStart = $row["rmb_date_start"];
        $this->rmbDateEnd = $row["rmb_date_end"];
        $this->rmbVal = $row["rmb_val"];
        $this->aprBy = $row["apr_by"];
        $this->aprDate = $row["apr_date"];
        $this->aprRemark = $row["apr_remark"];
        $this->payDate = $row["pay_date"];
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
rmb_no,
rmb_date,
rmb_cat,
rmb_type,
rmb_date_start,
rmb_date_end,
rmb_val,
apr_by,
apr_date,
apr_remark,
pay_date,
remark,
status,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_rmb 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpRmb("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->rmbNo = $row["rmb_no"];
        $ret0->rmbDate = $row["rmb_date"];
        $ret0->rmbCat = $row["rmb_cat"];
        $ret0->rmbType = $row["rmb_type"];
        $ret0->rmbDateStart = $row["rmb_date_start"];
        $ret0->rmbDateEnd = $row["rmb_date_end"];
        $ret0->rmbVal = $row["rmb_val"];
        $ret0->aprBy = $row["apr_by"];
        $ret0->aprDate = $row["apr_date"];
        $ret0->aprRemark = $row["apr_remark"];
        $ret0->payDate = $row["pay_date"];
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
      FROM emp_rmb ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpRmb("C");

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
t1.rmb_no rmbNo,
t1.rmb_date rmbDate,
t1.rmb_cat rmbCat,
t1.rmb_type rmbType,
t1.rmb_date_start rmbDateStart,
t1.rmb_date_end rmbDateEnd,
t1.rmb_val rmbVal,
t1.apr_by aprBy,
t1.apr_date aprDate,
t1.apr_remark aprRemark,
t1.pay_date payDate,
t1.remark remark,
t1.status status,
case when coalesce(t1.pay_date,NULL) IS NULL THEN 0 ELSE 1 END stPayment,

      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_rmb t1
      JOIN mst_data t2 on t1.rmb_cat=t2.kodeData
      JOIN mst_data t3 on t1.rmb_type=t3.kodeData
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

  public function loadTablePeriod($cmonth = NULL, $cyear = NULL, $cjenis = NULL) {
	  
	if(!empty($cjenis)) $filter=" and t1.rmb_jenis='".$cjenis."'";
	  
    $sql = " SELECT

t1.id id,
t1.parent_id parentId,
t1.rmb_no rmbNo,
t1.rmb_date rmbDate,
t1.rmb_cat rmbCat,
t1.rmb_type rmbType,
t1.rmb_date_start rmbDateStart,
t1.rmb_date_end rmbDateEnd,
t1.rmb_val rmbVal,
t1.apr_by aprBy,
t1.apr_date aprDate,
t1.apr_remark aprRemark,
t1.pay_date payDate,
t1.remark remark,
t1.status status,
case when coalesce(t1.pay_date,NULL) IS NULL THEN 0 ELSE 1 END stPayment,

      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_rmb t1
      JOIN mst_data t2 on t1.rmb_cat=t2.kodeData
      JOIN mst_data t3 on t1.rmb_type=t3.kodeData
      WHERE parent_id=:pParentId
      AND date_format(t1.rmb_date,'%Y%m')='" . $cyear . $cmonth ."' ".$filter."
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

  function loadTableBalance($year = NULL) {
    if ($year == NULL) {
      $year = date("Y");
    }
    $sql = " SELECT

t1.id id,
t1.parent_id parentId,
t1.rmb_no rmbNo,
t1.rmb_date rmbDate,
t1.rmb_cat rmbCat,
t1.rmb_type rmbType,
t1.rmb_date_start rmbDateStart,
t1.rmb_date_end rmbDateEnd,
format(t1.rmb_val,0) rmbVal,
t1.rmb_val rmbValue,
t1.apr_by aprBy,
t1.apr_date aprDate,
t1.apr_remark aprRemark,
t1.pay_date payDate,
t1.remark remark,
t1.status status,
case when coalesce(t1.pay_date,NULL) IS NULL THEN 0 ELSE 1 END stPayment,

      t2.namaData catName,
      t3.namaData typeName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_rmb t1
      JOIN mst_data t2 on t1.rmb_cat=t2.kodeData
      JOIN mst_data t3 on t1.rmb_type=t3.kodeData
      WHERE parent_id=:pParentId
      AND t1.pay_date IS NOT NULL
      AND year(t1.rmb_date)='$year'
      ORDER BY t1.rmb_date ASC
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
    $cutil = new Common();
    if ($this->id == "") {
      $this->rmbNo = $cutil->generateId("RMB", 14, "rmb_no", "emp_rmb");
      $this->persist();
    } else {
      $this->update();
    }
    $fileIds = array();
    $fileIdPost = $_POST["dfId"];
    if (count($fileIdPost) > 0) {
      $dcn = 0;
      $fileArr = $this->reArrayFiles($_FILES["dfFile"]);
      foreach ($fileIdPost as $fid) {
        $qf = new EmpRmbFiles();
        $qf->id = $fid;
        if ($qf->id != "") {
          $qf = $qf->getById();
        }
        $qf->parentId = $this->id;
        $qf->description = $_POST["dfDesc"][$dcn];
        $file = $fileArr[$dcn];
        if ($file["tmp_name"] != "") {
          $dirPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "rmb" . DS;
          if (!file_exists($dirPath)) {
            mkdir($dirPath, "0777", true);
          }
          $fname = "rmb_" . date("Ymd_His") . "_" . $dcn . "_" . $this->parentId . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
          if (move_uploaded_file($file["tmp_name"], $dirPath . $fname)) {
            $qf->filename = $fname;
          }
        }
        if ($qf->id == null || $qf->id == "") {
          $qf = $qf->persist();
        } else {
          $qf->update();
        }
        $fileIds[] = $qf->id;
        $dcn++;
      }
    }
    $listUsedFileId = implode(",", $fileIds);
    if (count($fileIds) > 0) {
      $cutil->execute("DELETE FROM emp_rmb_files WHERE parent_id='" . $this->id . "' AND id NOT IN (" . $listUsedFileId . ")");
    } else {
      $cutil->execute("DELETE FROM emp_rmb_files WHERE parent_id='" . $this->id . "'");
    }
    return $this;
  }

  function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
      foreach ($file_keys as $key) {
        $file_ary[$i][$key] = $file_post[$key][$i];
      }
    }

    return $file_ary;
  }

}

?>
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recpost.inc
 *
 * @author mazte
 */
class RecPost extends DAL {

  public $id;
  public $planId;
  public $vacId;
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
    $sql = "INSERT INTO rec_post (
plan_id,
vac_id,
remark,
		
          cre_by,
          cre_date
          ) VALUES (
:pPlanId, 
:pVacId, 
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
      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $this->vacId, PDO::PARAM_STR);
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
    $sql = " UPDATE rec_post SET
plan_id=:pPlanId,
vac_id=:pVacId,
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

      $stmt->bindParam(':pPlanId', $this->planId, PDO::PARAM_STR);
      $stmt->bindParam(':pVacId', $this->vacId, PDO::PARAM_STR);
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
    $sql = " DELETE FROM rec_post
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
vac_id,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->planId = $row["plan_id"];
        $this->vacId = $row["vac_id"];
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

  function getByIdVacId() {
    $sql = " SELECT 
id,
plan_id,
vac_id,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post 
      WHERE vac_id=:pVacId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pVacId', $this->vacId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->planId = $row["plan_id"];
        $this->vacId = $row["vac_id"];
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

  function getAll() {
    $sql = " SELECT 
id,
plan_id,
vac_id,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecPost("C");
        $ret0->id = $row["id"];
        $ret0->planId = $row["plan_id"];
        $ret0->vacId = $row["vac_id"];
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

  function loadTable() {
    $sql = " SELECT

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM rec_post t1 
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
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    ###DTLS
    $dtlIds = $_SESSION["postdtl"];
    $c = 0;
    $fileArr = $this->reArrayFiles($_FILES["postFileName"]);
    foreach ($dtlIds as $id) {
//      echo "DTL-ID =>" . $id . "<br>";
      $rpod = new RecPostDtl();
      $rpod->id = $id;
      $rpod = $rpod->getById();
//      echo "POST_DTL[BEFORE]: $rpod->id <br>";
//      var_dump($rpod);
      $rpod->postValue = $_POST["postValue"][$c];
      $rpod->remark = $_POST["dtlRemark"][$c];
      $rpod->postTypeField = (isset($_POST["postTypeField"][$c]) ? 0 : 1);
      $file = $fileArr[$c];
      $filePostTmp = $file["tmp_name"];
      $filePostName = $file["name"];
      if ($filePostTmp != "" && $filePostTmp != "none") {
        $postPath = HOME_DIR . DS . "files" . DS . "recruit" . DS . "post" . DS;
        if (!file_exists($postPath)) {
          mkdir($postPath, "0777", true);
        }
        $fpostName = "post_dtl_" . date("Ymd_His") . "_" . $this->id . "_" . $rpod->id . "." . pathinfo($filePostName, PATHINFO_EXTENSION);
        if (move_uploaded_file($filePostTmp, $postPath . $fpostName)) {
          $rpod->postFilename = $fpostName;
        }
      }
      $rpod->update();
//      echo "POST_DTL[AFTER]: $rpod->id <br>";
//      var_dump($rpod);
      $c++;
    }

//    die();
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
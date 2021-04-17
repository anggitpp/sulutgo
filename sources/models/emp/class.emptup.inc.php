<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp_tupoksi.inc.php
 *
 * @author mazte
 */
class EmpTup extends DAL {

  public $id;
  public $parentId;
  public $code;
  public $positionName;
  public $leaderId;
  public $description;
  public $target;
  public $result;
  public $resource;
  public $spvResp;
  public $spvRole;
  public $eduId;
  public $experience;
  public $skill;
  public $filename;
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
    $sql = "INSERT INTO emp_tupoksi (
      
parent_id,
code,
position_name,
leader_id,
description,
target,
result,
resource,
spv_resp,
spv_role,
edu_id,
experience,
skill,
filename,


          cre_by,
          cre_date
          ) VALUES (

:pParentId, 
:pCode, 
:pPositionName, 
:pLeaderId, 
:pDescription, 
:pTarget, 
:pResult, 
:pResource, 
:pSpvResp, 
:pSpvRole, 
:pEduId, 
:pExperience, 
:pSkill, 
:pFilename, 

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
      $stmt->bindParam(':pCode', $this->code, PDO::PARAM_STR);
      $stmt->bindParam(':pPositionName', $this->positionName, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaderId', $this->leaderId, PDO::PARAM_STR);
      $stmt->bindParam(':pDescription', $this->description, PDO::PARAM_STR);
      $stmt->bindParam(':pTarget', $this->target, PDO::PARAM_STR);
      $stmt->bindParam(':pResult', $this->result, PDO::PARAM_STR);
      $stmt->bindParam(':pResource', $this->resource, PDO::PARAM_STR);
      $stmt->bindParam(':pSpvResp', $this->spvResp, PDO::PARAM_STR);
      $stmt->bindParam(':pSpvRole', $this->spvRole, PDO::PARAM_STR);
      $stmt->bindParam(':pEduId', $this->eduId, PDO::PARAM_STR);
      $stmt->bindParam(':pExperience', $this->experience, PDO::PARAM_STR);
      $stmt->bindParam(':pSkill', $this->skill, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);


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
    $sql = " UPDATE emp_tupoksi SET
parent_id=:pParentId,
code=:pCode,
position_name=:pPositionName,
leader_id=:pLeaderId,
description=:pDescription,
target=:pTarget,
result=:pResult,
resource=:pResource,
spv_resp=:pSpvResp,
spv_role=:pSpvRole,
edu_id=:pEduId,
experience=:pExperience,
skill=:pSkill,
filename=:pFilename,


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
      $stmt->bindParam(':pCode', $this->code, PDO::PARAM_STR);
      $stmt->bindParam(':pPositionName', $this->positionName, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaderId', $this->leaderId, PDO::PARAM_STR);
      $stmt->bindParam(':pDescription', $this->description, PDO::PARAM_STR);
      $stmt->bindParam(':pTarget', $this->target, PDO::PARAM_STR);
      $stmt->bindParam(':pResult', $this->result, PDO::PARAM_STR);
      $stmt->bindParam(':pResource', $this->resource, PDO::PARAM_STR);
      $stmt->bindParam(':pSpvResp', $this->spvResp, PDO::PARAM_STR);
      $stmt->bindParam(':pSpvRole', $this->spvRole, PDO::PARAM_STR);
      $stmt->bindParam(':pEduId', $this->eduId, PDO::PARAM_STR);
      $stmt->bindParam(':pExperience', $this->experience, PDO::PARAM_STR);
      $stmt->bindParam(':pSkill', $this->skill, PDO::PARAM_STR);
      $stmt->bindParam(':pFilename', $this->filename, PDO::PARAM_STR);

      $stmt->bindParam(':pUpdBy', $this->updBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updDate, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM emp_tupoksi
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

  function desparent() {
    $sql = " DELETE FROM emp_tupoksi
				 WHERE parent_id=:pId
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
code,
position_name,
leader_id,
description,
target,
result,
resource,
spv_resp,
spv_role,
edu_id,
experience,
skill,
filename,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_tupoksi 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->code = $row["code"];
        $this->positionName = $row["position_name"];
        $this->leaderId = $row["leader_id"];
        $this->description = $row["description"];
        $this->target = $row["target"];
        $this->result = $row["result"];
        $this->resource = $row["resource"];
        $this->spvResp = $row["spv_resp"];
        $this->spvRole = $row["spv_role"];
        $this->eduId = $row["edu_id"];
        $this->experience = $row["experience"];
        $this->skill = $row["skill"];
        $this->filename = $row["filename"];


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

  function getSingleRowByParentId() {
    $sql = " SELECT 
      
id,
parent_id,
code,
position_name,
leader_id,
description,
target,
result,
resource,
spv_resp,
spv_role,
edu_id,
experience,
skill,
filename,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_tupoksi 
      WHERE parent_id=:pParentId
      LIMIT 1";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->code = $row["code"];
        $this->positionName = $row["position_name"];
        $this->leaderId = $row["leader_id"];
        $this->description = $row["description"];
        $this->target = $row["target"];
        $this->result = $row["result"];
        $this->resource = $row["resource"];
        $this->spvResp = $row["spv_resp"];
        $this->spvRole = $row["spv_role"];
        $this->eduId = $row["edu_id"];
        $this->experience = $row["experience"];
        $this->skill = $row["skill"];
        $this->filename = $row["filename"];


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
code,
position_name,
leader_id,
description,
target,
result,
resource,
spv_resp,
spv_role,
edu_id,
experience,
skill,
filename,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_tupoksi 
      WHERE parent_id=:pParentId
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $this->parentId, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpTup("C");
        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->code = $row["code"];
        $ret0->positionName = $row["position_name"];
        $ret0->leaderId = $row["leader_id"];
        $ret0->description = $row["description"];
        $ret0->target = $row["target"];
        $ret0->result = $row["result"];
        $ret0->resource = $row["resource"];
        $ret0->spvResp = $row["spv_resp"];
        $ret0->spvRole = $row["spv_role"];
        $ret0->eduId = $row["edu_id"];
        $ret0->experience = $row["experience"];
        $ret0->skill = $row["skill"];
        $ret0->filename = $row["filename"];


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
      FROM emp_tupoksi ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpTup("C");

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
t1.code code,
t1.position_name positionName,
t1.leader_id leaderId,
t1.description description,
t1.target target,
t1.result result,
t1.resource resource,
t1.spv_resp spvResp,
t1.spv_role spvRole,
t1.edu_id eduId,
t1.experience experience,
t1.skill skill,
t1.filename filename,

t2.namaData eduName,
t3.name leaderName,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_tupoksi t1
      JOIN mst_data t2 on t1.edu_id=t2.kodeData
      JOIN emp t3 on t1.leader_id=t3.id
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
    $this->parentId = $_SESSION["parent_id"];

    $fileKtpTmp = $_FILES["filename"]["tmp_name"];
    $fileKtpName = $_FILES["filename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "tupoksi" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "tupoksi_" . date("Ymd_His") . "_" . $this->parentId . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
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
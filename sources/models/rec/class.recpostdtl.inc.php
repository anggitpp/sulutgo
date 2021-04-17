<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.recpostdtl
 *
 * @author mazte
 */
class RecPostDtl extends DAL {

  public $id;
  public $parentId;
  public $postTypeId;
  public $postSort;
  public $postFilename;
  public $postTypeField;
  public $postValue;
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
    $sql = "INSERT INTO rec_post_detail (
parent_id,
post_type_id,
post_sort,
post_filename,
post_type_field,
post_value,
remark,
		
          cre_by,
          cre_date
          ) VALUES (
:pParentId, 
:pPostTypeId, 
:pPostSort, 
:pPostFilename, 
:pPostTypeField, 
:pPostValue, 
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
      $stmt->bindParam(':pPostTypeId', $this->postTypeId, PDO::PARAM_STR);
      $stmt->bindParam(':pPostSort', $this->postSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPostFilename', $this->postFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pPostTypeField', $this->postTypeField, PDO::PARAM_STR);
      $stmt->bindParam(':pPostValue', $this->postValue, PDO::PARAM_STR);
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
    $sql = " UPDATE rec_post_detail SET

parent_id=:pParentId,
post_type_id=:pPostTypeId,
post_sort=:pPostSort,
post_filename=:pPostFilename,
post_type_field=:pPostTypeField,
post_value=:pPostValue,
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
      $stmt->bindParam(':pPostTypeId', $this->postTypeId, PDO::PARAM_STR);
      $stmt->bindParam(':pPostSort', $this->postSort, PDO::PARAM_STR);
      $stmt->bindParam(':pPostFilename', $this->postFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pPostTypeField', $this->postTypeField, PDO::PARAM_STR);
      $stmt->bindParam(':pPostValue', $this->postValue, PDO::PARAM_STR);
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
    $sql = " DELETE FROM rec_post_detail
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
post_type_id,
post_sort,
post_filename,
post_type_field,
post_value,
remark,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post_detail 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->parentId = $row["parent_id"];
        $this->postTypeId = $row["post_type_id"];
        $this->postSort = $row["post_sort"];
        $this->postFilename = $row["post_filename"];
        $this->postTypeField = $row["post_type_field"];
        $this->postValue = $row["post_value"];
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
parent_id,
post_type_id,
post_sort,
post_filename,
post_type_field,
post_value,
remark,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post_detail ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecPostDtl("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->postTypeId = $row["post_type_id"];
        $ret0->postSort = $row["post_sort"];
        $ret0->postFilename = $row["post_filename"];
        $ret0->postTypeField = $row["post_type_field"];
        $ret0->postValue = $row["post_value"];
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

  function getAllByParentId() {
    $sql = " SELECT 
id,
parent_id,
post_type_id,
post_sort,
post_filename,
post_type_field,
post_value,
remark,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM rec_post_detail 
      WHERE 
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new RecPostDtl("C");

        $ret0->id = $row["id"];
        $ret0->parentId = $row["parent_id"];
        $ret0->postTypeId = $row["post_type_id"];
        $ret0->postSort = $row["post_sort"];
        $ret0->postFilename = $row["post_filename"];
        $ret0->postTypeField = $row["post_type_field"];
        $ret0->postValue = $row["post_value"];
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
      FROM rec_post_detail t1 
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
    return $this;
  }

}

?>
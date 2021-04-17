<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.mstdata.inc
 *
 * @author mazte
 */
class MstData extends DAL {

  public $kodeData;
  public $kodeInduk;
  public $kodeMenu;
  public $kodeReport;
  public $kodeCategory;
  public $namaData;
  public $keteranganData;
  public $urutanData;
  public $statusData;
  public $createBy;
  public $createTime;
  public $updateBy;
  public $updateTime;

  public function __construct($dbo = NULL) {
    if ($dbo == NULL) {
      parent::__construct($dbo);
    }
  }

  function persist() {
    $sql = "INSERT INTO mst_data (
kodeData,
kodeInduk,
kodeMenu,
kodeReport,
kodeCategory,
namaData,
keteranganData,
urutanData,
statusData,
		
          createBy,
          createTime
          ) VALUES (
:pKodeData, 
:pKodeInduk, 
:pKodeMenu, 
:pKodeReport, 
:pKodeCategory, 
:pNamaData, 
:pKeteranganData, 
:pUrutanData, 
:pStatusData, 

          :pCreBy,
          :pCreDate
          )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->createBy = $cUsername;
      $this->createTime = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);

      $stmt->bindParam(':pKodeData', $this->kodeData, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeInduk', $this->kodeInduk, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeMenu', $this->kodeMenu, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeReport', $this->kodeReport, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeCategory', $this->kodeCategory, PDO::PARAM_STR);
      $stmt->bindParam(':pNamaData', $this->namaData, PDO::PARAM_STR);
      $stmt->bindParam(':pKeteranganData', $this->keteranganData, PDO::PARAM_STR);
      $stmt->bindParam(':pUrutanData', $this->urutanData, PDO::PARAM_STR);
      $stmt->bindParam(':pStatusData', $this->statusData, PDO::PARAM_STR);

      $stmt->bindParam(':pCreBy', $this->createBy, PDO::PARAM_STR);
      $stmt->bindParam(':pCreDate', $this->createTime, PDO::PARAM_STR);

      $stmt->execute();
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function update() {
    $sql = " UPDATE mst_data SET

kodeInduk=:pKodeInduk,
kodeMenu=:pKodeMenu,
kodeReport=:pKodeReport,
kodeCategory=:pKodeCategory,
namaData=:pNamaData,
keteranganData=:pKeteranganData,
urutanData=:pUrutanData,
statusData=:pStatusData,


          updateBy=:pUpdBy,
          updateTime=:pUpdDate
          WHERE kodeData=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updateBy = $cUsername;
      $this->updateTime = date('Y-m-d H:i:s');
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pKodeInduk', $this->kodeInduk, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeMenu', $this->kodeMenu, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeReport', $this->kodeReport, PDO::PARAM_STR);
      $stmt->bindParam(':pKodeCategory', $this->kodeCategory, PDO::PARAM_STR);
      $stmt->bindParam(':pNamaData', $this->namaData, PDO::PARAM_STR);
      $stmt->bindParam(':pKeteranganData', $this->keteranganData, PDO::PARAM_STR);
      $stmt->bindParam(':pUrutanData', $this->urutanData, PDO::PARAM_STR);
      $stmt->bindParam(':pStatusData', $this->statusData, PDO::PARAM_STR);


      $stmt->bindParam(':pUpdBy', $this->updateBy, PDO::PARAM_STR);
      $stmt->bindParam(':pUpdDate', $this->updateTime, PDO::PARAM_STR);
      $stmt->bindParam(':pId', $this->kodeData, PDO::PARAM_STR);
      $stmt->execute();
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function destroy() {
    $sql = " DELETE FROM mst_data
				 WHERE kodeData=:pId
				 ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->kodeData, PDO::PARAM_STR);
      $stmt->execute();
      return 1;
    } catch (Exception $ex) {
      var_dump($ex);
      return 0;
    }
  }

  function getById() {
    $sql = " SELECT 

t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
t1.statusData statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1
      WHERE t1.kodeData=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->kodeData, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->kodeData = $row["kodeData"];
        $this->kodeInduk = $row["kodeInduk"];
        $this->kodeMenu = $row["kodeMenu"];
        $this->kodeReport = $row["kodeReport"];
        $this->kodeCategory = $row["kodeCategory"];
        $this->namaData = $row["namaData"];
        $this->keteranganData = $row["keteranganData"];
        $this->urutanData = $row["urutanData"];
        $this->statusData = $row["statusData"];
        $this->createBy = $row["createBy"];
        $this->createTime = $row["createTime"];
        $this->updateBy = $row["updateBy"];
        $this->updateTime = $row["updateTime"];
      }
      $stmt->closeCursor();
      return $this;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAll() {
    $sql = " SELECT 

      createBy,
      createTime,
      updateBy,
      updateTime
      FROM mst_data ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new MstData("C");

        $ret0->createBy = $row["createBy"];
        $ret0->createTime = $row["createTime"];
        $ret0->updateBy = $row["updateBy"];
        $ret0->updateTime = $row["updateTime"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAllByCat($catId, $id = "") {
    $sql = " SELECT 

t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
t1.statusData statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1 
      WHERE t1.kodeCategory=:pCatId
      ";
    if (!empty($id)) {
      $sql.=" AND t1.kodeData='$id'";
    }
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pCatId', $catId);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new MstData("C");
        $ret0->kodeData = $row["kodeData"];
        $ret0->kodeInduk = $row["kodeInduk"];
        $ret0->kodeMenu = $row["kodeMenu"];
        $ret0->kodeReport = $row["kodeReport"];
        $ret0->kodeCategory = $row["kodeCategory"];
        $ret0->namaData = $row["namaData"];
        $ret0->keteranganData = $row["keteranganData"];
        $ret0->urutanData = $row["urutanData"];
        $ret0->statusData = $row["statusData"];

        $ret0->createBy = $row["createBy"];
        $ret0->createTime = $row["createTime"];
        $ret0->updateBy = $row["updateBy"];
        $ret0->updateTime = $row["updateTime"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAllByParent($parentId) {
    $sql = " SELECT 

t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
t1.statusData statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1 
      WHERE t1.kodeInduk=:pParentId
      order by t1.urutanData";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $parentId);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new MstData("C");
        $ret0->kodeData = $row["kodeData"];
        $ret0->kodeInduk = $row["kodeInduk"];
        $ret0->kodeMenu = $row["kodeMenu"];
        $ret0->kodeReport = $row["kodeReport"];
        $ret0->kodeCategory = $row["kodeCategory"];
        $ret0->namaData = $row["namaData"];
        $ret0->keteranganData = $row["keteranganData"];
        $ret0->urutanData = $row["urutanData"];
        $ret0->statusData = $row["statusData"];

        $ret0->createBy = $row["createBy"];
        $ret0->createTime = $row["createTime"];
        $ret0->updateBy = $row["updateBy"];
        $ret0->updateTime = $row["updateTime"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function getAllByParentTupoksi($parentId) {
    $sql = " SELECT 

t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
coalesce((select count(id) from emp_tupoksi t2 where t2.parent_id=t1.kodeData),0) statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1 
      WHERE t1.kodeInduk=:pParentId
      order by t1.urutanData";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $parentId);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new MstData("C");
        $ret0->kodeData = $row["kodeData"];
        $ret0->kodeInduk = $row["kodeInduk"];
        $ret0->kodeMenu = $row["kodeMenu"];
        $ret0->kodeReport = $row["kodeReport"];
        $ret0->kodeCategory = $row["kodeCategory"];
        $ret0->namaData = $row["namaData"];
        $ret0->keteranganData = $row["keteranganData"];
        $ret0->urutanData = $row["urutanData"];
        $ret0->statusData = $row["statusData"];

        $ret0->createBy = $row["createBy"];
        $ret0->createTime = $row["createTime"];
        $ret0->updateBy = $row["updateBy"];
        $ret0->updateTime = $row["updateTime"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function countChildren($parentId) {
    $sql = " SELECT count(kodeData) cnt FROM mst_data where kodeInduk=:pParentId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParentId', $parentId);
      $stmt->execute();
      $row = $stmt->fetch();
      return intval($row["cnt"]);
    } catch (Exception $ex) {
      var_dump($ex);
      return -1;
    }
  }

  function loadTableByCat($cat) {
    $sql = " SELECT
      
t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
t1.statusData statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1
      WHERE t1.kodeCategory=:pCat
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pCat', $cat);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return json_encode(array("sEcho" => 1, "aaData" => $result));
  }

  function loadTableByParent($parentId) {
    $sql = " SELECT
      
t1.kodeData kodeData,
t1.kodeInduk kodeInduk,
t1.kodeMenu kodeMenu,
t1.kodeReport kodeReport,
t1.kodeCategory kodeCategory,
t1.namaData namaData,
t1.keteranganData keteranganData,
t1.urutanData urutanData,
t1.statusData statusData,

      t1.createBy,
      t1.createTime,
      t1.updateBy,
      t1.updateTime
      FROM mst_data t1
      WHERE t1.kodeInduk=:pParent
      ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pParent', $parentId);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return json_encode(array("sEcho" => 1, "aaData" => $result));
  }

  function loadTableOrg($dept) {
    $sql = "
     SELECT
	t_outer.kodeData,	
	t_outer.kodeCategory,
 	CONCAT(' ',REPEAT('-', LEVEL_ - 1),' ', t_outer.namaData) description, 
	t_outer.kodeInduk,
	t_outer.namaData namaData,
	t_outer.namaData namaData1,
	t_outer.namaData namaData2,
  t_outer.urutanData orderNo,
	t_outer.statusData,
	LEVEL_ pLevel
FROM (
		SELECT
			fn_dept_connect_by_prior (kodeData) AS id,@LEVEL AS LEVEL_
		FROM ( SELECT
					@start_with := 0,
					@id := @start_with,
					@LEVEL := 0
			) vars,
			mst_data dd
		WHERE
			@id IS NOT NULL
	) t_inner
JOIN mst_data t_outer ON t_outer.kodeData = t_inner.id
WHERE t_outer.kodeCategory IN ('X04','X05','X06','X07','X08','X09')
      ";
    if (!empty($dept)) {
      $sql.=" AND t_outer.kodeData=";
    }
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
            $this->$var = "";
          }
        } else {
          $this->$var = implode(";", $value);
        }
      }
    }
    return $this;
  }

  function processForm() {
    $this->kodeData = $_SESSION["entity_id"];
    $this->kodeMenu = 0;
    $this->kodeReport = 0;
    $this->keteranganData = "";
    $this->urutanData = 1;
    $this->updateTime = "0000-00-00 00:00:00";
    $this->updateBy = "";
    $this->getById();
    $this->populateWithPost();
    $this->kodeCategory = $_SESSION["entity_cat"];
    if ($this->kodeData == "") {
      $cutil = new Common();
      $this->kodeData = $cutil->getDescription("SELECT coalesce(MAX(kodeData),0)+1 id from mst_data", "id");
      $this->persist();
    } else {
      $this->update();
    }
//    die();
    return $this;
  }

  public function loadOrg() {
    $html = '';
    $sql = "
      select 0 id, 'Direktur Utama' dept_name, NULL parent_, 'X04' kodeCategory, 0 urutanData
        UNION ALL
        select kodeData id, namaData dept_name, kodeInduk parent_, kodeCategory, urutanData from mst_data where kodeCategory IN('X04','X05','X06','X07','X08','X09') order by kodeCategory,urutanData
";
    try {
      $stmt1 = $this->db->prepare($sql);
      $stmt1->execute();
      $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
//      var_dump($result);
      foreach ($result as $row) {
        $p = $row['parent_'];
        $id = $row['id'];
        $rows[$id] = array('id' => $id, 'parent_' => $p, 'dept_name' => $row['dept_name'], 'children' => array());
      }
      $stmt1->closeCursor();
      foreach ($rows as &$v) {
//        var_dump($v);
        if (isset($v['parent_']) && isset($v['id'])) {
          if ($v['parent_'] == $v['id'])
            continue;
          $rows[$v['parent_']]['children'][] = &$v;
        }
      }
      array_splice($rows, 1);
      $content = $this->loadedHierarchy($rows[0]);
    } catch (PDOException $ex) {
      echo $ex->getMessage();
    }
    $html = $html . $content;
    echo $html;
  }

  function loadedHierarchy($o) {
    echo "<li>{$o['dept_name']}<ul>";
    foreach ($o['children'] as $v) {
      $this->loadedHierarchy($v);
    }
    echo "</ul></li>";
  }

}

?>
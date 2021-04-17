<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.empinfodoc.inc
 *
 * @author mazte
 */
class EmpInfoNews extends DAL {

  public $id;
  public $subject;
  public $newsSource;
  public $newsDate;
  public $remark;
  public $fileNews;
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
    $sql = "INSERT INTO emp_info_news (
		
subject,
news_source,
news_date,
remark,
file_news,
status,

          cre_by,
          cre_date
          ) VALUES (
:pSubject, 
:pNewsSource, 
:pNewsDate, 
:pRemark, 
:pFileNews, 
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
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pNewsSource', $this->newsSource, PDO::PARAM_STR);
      $stmt->bindParam(':pNewsDate', $this->newsDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileNews', $this->fileNews, PDO::PARAM_STR);
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
    $sql = " UPDATE emp_info_news SET
      
subject=:pSubject,
news_source=:pNewsSource,
news_date=:pNewsDate,
remark=:pRemark,
file_news=:pFileNews,
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
      $stmt->bindParam(':pSubject', $this->subject, PDO::PARAM_STR);
      $stmt->bindParam(':pNewsSource', $this->newsSource, PDO::PARAM_STR);
      $stmt->bindParam(':pNewsDate', $this->newsDate, PDO::PARAM_STR);
      $stmt->bindParam(':pRemark', $this->remark, PDO::PARAM_STR);
      $stmt->bindParam(':pFileNews', $this->fileNews, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp_info_news
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
subject,
news_source,
news_date,
remark,
file_news,
status,


      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_info_news 
      WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {
        $this->id = $row["id"];
        $this->subject = $row["subject"];
        $this->newsSource = $row["news_source"];
        $this->newsDate = $row["news_date"];
        $this->remark = $row["remark"];
        $this->fileNews = $row["file_news"];
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
subject,
news_source,
news_date,
remark,
file_news,
status,

      cre_by,
      cre_date,
      upd_by,
      upd_date
      FROM emp_info_news ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new EmpInfoNews("C");
        $ret0->id = $row["id"];
        $ret0->subject = $row["subject"];
        $ret0->newsSource = $row["news_source"];
        $ret0->newsDate = $row["news_date"];
        $ret0->remark = $row["remark"];
        $ret0->fileNews = $row["file_news"];
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
t1.subject subject,
t1.news_source newsSource,
t1.news_date newsDate,
t1.remark remark,
t1.file_news fileNews,
t1.status status,

      t1.cre_by creBy,
      t1.cre_date creDate,
      t1.upd_by updBy,
      t1.upd_date updDate
      FROM emp_info_news t1 
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

    $fileNewsTmp = $_FILES["fileNews"]["tmp_name"];
    $fileNewsName = $_FILES["fileNews"]["name"];
    if ($fileNewsTmp != "" && $fileNewsTmp != "none") {
      $newsPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "news" . DS;
      if (!file_exists($newsPath)) {
        mkdir($newsPath, "0777", true);
      }
      $fnewsName = "news_" . date("Ymd_His") . "_" . $this->id . "." . pathinfo($fileNewsName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileNewsTmp, $newsPath . $fnewsName)) {
        $this->fileNews = $fnewsName;
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
<?php

/*
 *  Build on pojay.dev @42A
 */

/**
 * Description of class.emp.inc.php
 *
 * @author mazte
 */
class Emp extends DAL {

  public $id;
  public $cat;
  public $name;
  public $alias;
  public $regNo;
  public $birthPlace;
  public $birthDate;
  public $ktpNo;
  public $ktpFilename;
  public $ktpValid;
  public $ktpLifetime;
  public $gender;
  public $lembur;
  public $ktpAddress;
  public $ktpProv;
  public $ktpCity;
  public $domAddress;
  public $domProv;
  public $domCity;
  public $phoneNo;
  public $cellNo;
  public $email;
  public $marital;
  public $ptkp;
  public $religion;
  public $picFilename;
  public $npwpNo;
  public $npwpDate;
  public $bpjsNo;
  public $bpjsDate;
  public $bpjsNoKS;
  public $bpjsDateKS;
  public $bloodType;
  public $bloodResus;
  public $uniCloth;
  public $uniPant;
  public $uniShoe;
  public $status;
  public $joinDate;
  public $leaveDate;
  public $nation;
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
    $sql = "INSERT INTO emp (
    cat,
    name,
    alias,
    reg_no,
    birth_place,
    birth_date,
    ktp_no,
    ktp_filename,
    ktp_valid,
    ktp_lifetime,
    gender,
    lembur,
    ktp_address,
    ktp_prov,
    ktp_city,
    dom_address,
    dom_prov,
    dom_city,
    phone_no,
    cell_no,
    email,
    marital,
    ptkp,
    religion,
    pic_filename,
    npwp_no,
    npwp_date,
    bpjs_no,
    bpjs_date,
    bpjs_no_ks,
    bpjs_date_ks,
    blood_type,
    blood_resus,
    uni_cloth,
    uni_pant,
    uni_shoe,

    join_date,
    leave_date,
    nation,
    status,

    create_by,
    create_date
    ) VALUES (

    :pCat, 
    :pName, 
    :pAlias, 
    :pRegNo, 
    :pBirthPlace, 
    :pBirthDate, 
    :pKtpNo, 
    :pKtpFilename, 
    :pKtpValid, 
    :pKtpLifetime, 
    :pGender, 
    :pLembur,
    :pKtpAddress, 
    :pKtpProv, 
    :pKtpCity, 
    :pDomAddress, 
    :pDomProv, 
    :pDomCity, 
    :pPhoneNo, 
    :pCellNo, 
    :pEmail, 
    :pMarital, 
    :pPtkp, 
    :pReligion, 
    :pPicFilename, 
    :pNpwpNo,
    :pNpwpDate, 
    :pBpjsNo, 
    :pBpjsDate, 
    :pBpjsNoKS, 
    :pBpjsDateKS, 
    :pBloodType, 
    :pBloodResus, 
    :pUniCloth, 
    :pUniPant, 
    :pUniShoe, 

    :pJoinDate,
    :pLeaveDate,
    :pNation,
    :pStatus,

    :pCreBy,
    :pCreDate
    )";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->creBy = $cUsername;
      $this->creDate = date('Y-m-d H:i:s');
      $this->birthDate = setTanggal($this->birthDate);
      $this->ktpValid = setTanggal($this->ktpValid);
      $this->joinDate = setTanggal($this->joinDate);
      $this->leaveDate = setTanggal($this->leaveDate);
      $this->bpjsDate = setTanggal($this->bpjsDate);
      $this->bpjsDateKS = setTanggal($this->bpjsDateKS);
      $this->npwpDate = setTanggal($this->npwpDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pCat', $this->cat, PDO::PARAM_STR);
      $stmt->bindParam(':pName', $this->name, PDO::PARAM_STR);
      $stmt->bindParam(':pAlias', $this->alias, PDO::PARAM_STR);
      $stmt->bindParam(':pRegNo', $this->regNo, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthPlace', $this->birthPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthDate', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpNo', $this->ktpNo, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpFilename', $this->ktpFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpValid', $this->ktpValid, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpLifetime', $this->ktpLifetime, PDO::PARAM_STR);
      $stmt->bindParam(':pGender', $this->gender, PDO::PARAM_STR);
      $stmt->bindParam(':pLembur', $this->lembur, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpAddress', $this->ktpAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpProv', $this->ktpProv, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpCity', $this->ktpCity, PDO::PARAM_STR);
      $stmt->bindParam(':pDomAddress', $this->domAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pDomProv', $this->domProv, PDO::PARAM_STR);
      $stmt->bindParam(':pDomCity', $this->domCity, PDO::PARAM_STR);
      $stmt->bindParam(':pPhoneNo', $this->phoneNo, PDO::PARAM_STR);
      $stmt->bindParam(':pCellNo', $this->cellNo, PDO::PARAM_STR);
      $stmt->bindParam(':pEmail', $this->email, PDO::PARAM_STR);
      $stmt->bindParam(':pMarital', $this->marital, PDO::PARAM_STR);
      $stmt->bindParam(':pPtkp', $this->ptkp, PDO::PARAM_STR);
      $stmt->bindParam(':pReligion', $this->marital, PDO::PARAM_STR);
      $stmt->bindParam(':pPicFilename', $this->picFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pNpwpNo', $this->npwpNo, PDO::PARAM_STR);
      $stmt->bindParam(':pNpwpDate', $this->npwpDate, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsNo', $this->bpjsNo, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsDate', $this->bpjsDate, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsNoKS', $this->bpjsNoKS, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsDateKS', $this->bpjsDateKS, PDO::PARAM_STR);
      $stmt->bindParam(':pBloodType', $this->bloodType, PDO::PARAM_STR);
      $stmt->bindParam(':pBloodResus', $this->bloodResus, PDO::PARAM_STR);
      $stmt->bindParam(':pUniCloth', $this->uniCloth, PDO::PARAM_STR);
      $stmt->bindParam(':pUniPant', $this->uniPant, PDO::PARAM_STR);
      $stmt->bindParam(':pUniShoe', $this->uniShoe, PDO::PARAM_STR);

      $stmt->bindParam(':pJoinDate', $this->joinDate, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaveDate', $this->leaveDate, PDO::PARAM_STR);
      $stmt->bindParam(':pNation', $this->nation, PDO::PARAM_STR);
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
    $sql = " UPDATE emp SET

    id=:pId,
    cat=:pCat,
    name=:pName,
    alias=:pAlias,
    reg_no=:pRegNo,
    birth_place=:pBirthPlace,
    birth_date=:pBirthDate,
    ktp_no=:pKtpNo,
    ktp_filename=:pKtpFilename,
    ktp_valid=:pKtpValid,
    ktp_lifetime=:pKtpLifetime,
    gender=:pGender,
    lembur=:pLembur,
    ktp_address=:pKtpAddress,
    ktp_prov=:pKtpProv,
    ktp_city=:pKtpCity,
    dom_address=:pDomAddress,
    dom_prov=:pDomProv,
    dom_city=:pDomCity,
    phone_no=:pPhoneNo,
    cell_no=:pCellNo,
    email=:pEmail,
    marital=:pMarital,
    ptkp=:pPtkp,
    religion=:pReligion,
    pic_filename=:pPicFilename,
    npwp_no=:pNpwpNo,
    npwp_date=:pNpwpDate,
    bpjs_no=:pBpjsNo,
    bpjs_date=:pBpjsDate,
    bpjs_no_ks=:pBpjsNoKS,
    bpjs_date_ks=:pBpjsDateKS,
    blood_type=:pBloodType,
    blood_resus=:pBloodResus,
    uni_cloth=:pUniCloth,
    uni_pant=:pUniPant,
    uni_shoe=:pUniShoe,

    join_date=:pJoinDate,
    leave_date=:pLeaveDate,
    nation=:pNation,
    status=:pStatus,

    update_by=:pUpdBy,
    update_date=:pUpdDate
    WHERE id=:pId";
    try {
      global $db,$cUsername;
      date_default_timezone_set('Asia/Jakarta');
      $this->updBy = $cUsername;
      $this->updDate = date('Y-m-d H:i:s');
      $this->birthDate = setTanggal($this->birthDate);
      $this->ktpValid = setTanggal($this->ktpValid);
      $this->joinDate = setTanggal($this->joinDate);
      $this->leaveDate = setTanggal($this->leaveDate);
      $this->bpjsDate = setTanggal($this->bpjsDate);
      $this->bpjsDateKS = setTanggal($this->bpjsDateKS);
      $this->npwpDate = setTanggal($this->npwpDate);
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->bindParam(':pCat', $this->cat, PDO::PARAM_STR);
      $stmt->bindParam(':pName', $this->name, PDO::PARAM_STR);
      $stmt->bindParam(':pAlias', $this->alias, PDO::PARAM_STR);
      $stmt->bindParam(':pRegNo', $this->regNo, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthPlace', $this->birthPlace, PDO::PARAM_STR);
      $stmt->bindParam(':pBirthDate', $this->birthDate, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpNo', $this->ktpNo, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpFilename', $this->ktpFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpValid', $this->ktpValid, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpLifetime', $this->ktpLifetime, PDO::PARAM_STR);
      $stmt->bindParam(':pGender', $this->gender, PDO::PARAM_STR);
      $stmt->bindParam(':pLembur', $this->lembur, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpAddress', $this->ktpAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpProv', $this->ktpProv, PDO::PARAM_STR);
      $stmt->bindParam(':pKtpCity', $this->ktpCity, PDO::PARAM_STR);
      $stmt->bindParam(':pDomAddress', $this->domAddress, PDO::PARAM_STR);
      $stmt->bindParam(':pDomProv', $this->domProv, PDO::PARAM_STR);
      $stmt->bindParam(':pDomCity', $this->domCity, PDO::PARAM_STR);
      $stmt->bindParam(':pPhoneNo', $this->phoneNo, PDO::PARAM_STR);
      $stmt->bindParam(':pCellNo', $this->cellNo, PDO::PARAM_STR);
      $stmt->bindParam(':pEmail', $this->email, PDO::PARAM_STR);
      $stmt->bindParam(':pMarital', $this->marital, PDO::PARAM_STR);
      $stmt->bindParam(':pPtkp', $this->ptkp, PDO::PARAM_STR);
      $stmt->bindParam(':pReligion', $this->religion, PDO::PARAM_STR);
      $stmt->bindParam(':pPicFilename', $this->picFilename, PDO::PARAM_STR);
      $stmt->bindParam(':pNpwpNo', $this->npwpNo, PDO::PARAM_STR);
      $stmt->bindParam(':pNpwpDate', $this->npwpDate, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsNo', $this->bpjsNo, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsDate', $this->bpjsDate, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsNoKS', $this->bpjsNoKS, PDO::PARAM_STR);
      $stmt->bindParam(':pBpjsDateKS', $this->bpjsDateKS, PDO::PARAM_STR);
      $stmt->bindParam(':pBloodType', $this->bloodType, PDO::PARAM_STR);
      $stmt->bindParam(':pBloodResus', $this->bloodResus, PDO::PARAM_STR);
      $stmt->bindParam(':pUniCloth', $this->uniCloth, PDO::PARAM_STR);
      $stmt->bindParam(':pUniPant', $this->uniPant, PDO::PARAM_STR);
      $stmt->bindParam(':pUniShoe', $this->uniShoe, PDO::PARAM_STR);

      $stmt->bindParam(':pJoinDate', $this->joinDate, PDO::PARAM_STR);
      $stmt->bindParam(':pLeaveDate', $this->leaveDate, PDO::PARAM_STR);
      $stmt->bindParam(':pNation', $this->nation, PDO::PARAM_STR);
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
    $sql = " DELETE FROM emp
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
        kode,
    cat,
    name,
    alias,
    reg_no,
    birth_place,
    birth_date,
    ktp_no,
    ktp_filename,
    ktp_valid,
    ktp_lifetime,
    gender,
    lembur,
    ktp_address,
    ktp_prov,
    ktp_city,
    dom_address,
    dom_prov,
    dom_city,
    phone_no,
    cell_no,
    email,
    marital,
    ptkp,
    religion,
    pic_filename,
    npwp_no,
    npwp_date,
    bpjs_no,
    bpjs_date,
    bpjs_no_ks,
    bpjs_date_ks,
    blood_type,
    blood_resus,
    uni_cloth,
    uni_pant,
    uni_shoe,

    join_date,
    leave_date,
    nation,
    status,

    create_by,
    create_date,
    update_by,
    update_date
    FROM emp 
    WHERE id=:pId";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(':pId', $this->id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($result as $row) {

        $this->id = $row["id"];
        $this->cat = $row["cat"];
        $this->name = $row["name"];
        $this->alias = $row["alias"];
          $this->regNo = $row["reg_no"];
          $this->kode = $row["kode"];
        $this->birthPlace = $row["birth_place"];
        $this->birthDate = $row["birth_date"];
        $this->ktpNo = $row["ktp_no"];
        $this->ktpFilename = $row["ktp_filename"];
        $this->ktpValid = $row["ktp_valid"];
        $this->ktpLifetime = $row["ktp_lifetime"];
        $this->gender = $row["gender"];
        $this->lembur = $row["lembur"];
        $this->ktpAddress = $row["ktp_address"];
        $this->ktpProv = $row["ktp_prov"];
        $this->ktpCity = $row["ktp_city"];
        $this->domAddress = $row["dom_address"];
        $this->domProv = $row["dom_prov"];
        $this->domCity = $row["dom_city"];
        $this->phoneNo = $row["phone_no"];
        $this->cellNo = $row["cell_no"];
        $this->email = $row["email"];
        $this->marital = $row["marital"];
        $this->ptkp = $row["ptkp"];
        $this->religion = $row["religion"];
        $this->npwpNo = $row["npwp_no"];
        $this->npwpDate = $row["npwp_date"];
        $this->bpjsNo = $row["bpjs_no"];
        $this->bpjsDate = $row["bpjs_date"];
        $this->bpjsNoKS = $row["bpjs_no_ks"];
        $this->bpjsDateKS = $row["bpjs_date_ks"];
        $this->bloodType = $row["blood_type"];
        $this->bloodResus = $row["blood_resus"];
        $this->uniCloth = $row["uni_cloth"];
        $this->uniPant = $row["uni_pant"];
        $this->uniShoe = $row["uni_shoe"];

        $this->joinDate = $row["join_date"];
        $this->leaveDate = $row["leave_date"];
        $this->nation = $row["nation"];
        $this->status = $row["status"];
        $this->picFilename = $row["pic_filename"];

        $this->creBy = $row["create_by"];
        $this->creDate = $row["create_date"];
        $this->updBy = $row["update_by"];
        $this->updDate = $row["update_date"];
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
    name,
    alias,
    reg_no,
    birth_place,
    birth_date,
    ktp_no,
    ktp_filename,
    ktp_valid,
    ktp_lifetime,
    gender,
    lembur,
    ktp_address,
    ktp_prov,
    ktp_city,
    dom_address,
    dom_prov,
    dom_city,
    phone_no,
    cell_no,
    email,
    marital,
    ptkp,
    religion,
    npwp_no,
    npwp_date,
    bpjs_no,
    bpjs_date,
    bpjs_no_ks,
    bpjs_date_ks,
    blood_type,
    blood_resus,
    uni_cloth,
    uni_pant,
    uni_shoe,

    pic_filename,
    join_date,
    leave_date,
    nation,
    status,      

    create_by,
    create_date,
    update_by,
    update_date
    FROM emp ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $ret = array();
      foreach ($result as $row) {
        $ret0 = new Emp("C");
        $ret0->id = $row["id"];
        $ret0->cat = $row["cat"];
        $ret0->name = $row["name"];
        $ret0->alias = $row["alias"];
        $ret0->regNo = $row["reg_no"];
        $ret0->birthPlace = $row["birth_place"];
        $ret0->birthDate = $row["birth_date"];
        $ret0->ktpNo = $row["ktp_no"];
        $ret0->ktpFilename = $row["ktp_filename"];
        $ret0->ktpValid = $row["ktp_valid"];
        $ret0->ktpLifetime = $row["ktp_lifetime"];
        $ret0->gender = $row["gender"];
        $ret0->lembur = $row["lembur"];
        $ret0->ktpAddress = $row["ktp_address"];
        $ret0->ktpProv = $row["ktp_prov"];
        $ret0->ktpCity = $row["ktp_city"];
        $ret0->domAddress = $row["dom_address"];
        $ret0->domProv = $row["dom_prov"];
        $ret0->domCity = $row["dom_city"];
        $ret0->phoneNo = $row["phone_no"];
        $ret0->cellNo = $row["cell_no"];
        $ret0->email = $row["email"];
        $ret0->marital = $row["marital"];
        $ret0->ptkp = $row["ptkp"];
        $ret0->religion = $row["religion"];
        $ret0->npwpNo = $row["npwp_no"];
        $ret0->npwpDate = $row["npwp_date"];
        $ret0->bpjsNo = $row["bpjs_no"];
        $ret0->bpjsDate = $row["bpjs_date"];
        $ret0->bpjsNoKS = $row["bpjs_no_ks"];
        $ret0->bpjsDateKS = $row["bpjs_date_ks"];
        $ret0->bloodType = $row["blood_type"];
        $ret0->bloodResus = $row["blood_resus"];
        $ret0->uniCloth = $row["uni_cloth"];
        $ret0->uniPant = $row["uni_pant"];
        $ret0->uniShoe = $row["uni_shoe"];

        $ret0->joinDate = $row["join_date"];
        $ret0->leaveDate = $row["leave_date"];
        $ret0->nation = $row["nation"];
        $ret0->status = $row["status"];
        $ret0->picFilename = $row["pic_filename"];

        $ret0->creBy = $row["create_by"];
        $ret0->creDate = $row["create_date"];
        $ret0->updBy = $row["update_by"];
        $ret0->updDate = $row["update_date"];
        $ret[] = $ret0;
      }
      $stmt->closeCursor();
      return $ret;
    } catch (Exception $ex) {
      var_dump($ex);
    }
  }

  function loadTable($status) {
    global $areaCheck;
    $sql = " SELECT
    t1.id id,
    t1.cat cat,
    upper(t1.name) name,
    t1.alias alias,
    t1.reg_no regNo,
    t1.birth_place birthPlace,
    t1.birth_date birthDate,
    t1.ktp_no ktpNo,
    t1.ktp_filename ktpFilename,
    t1.ktp_valid ktpValid,
    t1.ktp_lifetime ktpLifetime,
    t1.gender gender,
    t1.lembur lembur,
    t1.ktp_address ktpAddress,
    t1.ktp_prov ktpProv,
    t1.ktp_city ktpCity,
    t1.dom_address domAddress,
    t1.dom_prov domProv,
    t1.dom_city domCity,
    t1.phone_no phoneNo,
    t1.cell_no cellNo,
    t1.email email,
    t1.nation nation,
    t1.pic_filename picFilename,
    t3.namaData divisi,
    '' lahir,
    t2.pos_name jabatan,
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja,
    t1.marital,
    t1.ptkp,
    t1.npwp_no npwpNo,

    t1.create_by creBy,
    t1.create_date creDate,
    t1.update_by updBy,
    t1.update_date updDate
    FROM emp t1
    LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
    LEFT JOIN mst_data t3 ON t3.kodeData=t2.div_id
    WHERE t1.cat='$_SESSION[emp_cat]'
    AND t1.status='$status'
    AND t2.location IN ($areaCheck);
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

  function exportToExcelForm($fSearch, $pos, $div_id, $location, $subDept, $unitId) {
    global $areaCheck, $arrParameter, $par, $arrParam, $s;
    $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");
    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");     
    $sWhere= " where status='".$status."' and cat='".$par[empType]."'";

    if (!empty($fSearch))
      $sWhere.= " and (lower(name) like '%".mysql_real_escape_string(strtolower($fSearch))."%' or lower(reg_no) like '%".mysql_real_escape_string(strtolower($fSearch))."%' or lower(pos_name) like '%".mysql_real_escape_string(strtolower($fSearch))."%')";
    if(!empty($pos))
      $sWhere.=" and pos_name = '".$pos."'";
    if(!empty($div_id))
      $sWhere.=" and div_id = '".$div_id."'";
    if(!empty($location))
      $sWhere.=" and location = '".$location."'";
    if(!empty($subDept))
      $sWhere.=" and dept_id = '".$subDept."'";
    if(!empty($unitId))
      $sWhere.=" and unit_id = '".$unitId."'";

    $sWhere .= " AND location IN ($areaCheck)";
    $sql = "
    SELECT 
    UPPER(`name`) `Nama`,
    t1.reg_no `NPP`,
    t1.pos_name `JABATAN`,
    
    t2.namaData `AGAMA`,
    t3.namaData `DEPARTEMEN`,
    t4.namaData `ALOKASI BIAYA`,
    (CASE WHEN t1.gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN t1.gender = 'F' THEN 'Perempuan' ELSE '' END) END) `GENDER`, 
    t7.namaData `PENDIDIKAN TERAKHIR`,
    t5.namaData `TEMPAT LAHIR`,
    t1.birth_date `TANGGAL LAHIR`,
    CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, CURRENT_DATE ) % 12, ' bln') `USIA`,
    t1.start_date `MULAI BEKERJA`,
    REPLACE(
    CASE WHEN COALESCE(t1.leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, CURRENT_DATE ) % 12, ' bln')
    WHEN t1.leave_date = '0000-00-00' THEN
    CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, t1.leave_date) % 12, ' bln')
    END,' 0 bln','') `MASA KERJA`


    
    FROM dta_pegawai t1
    LEFT JOIN mst_data t2 on t1.religion = t2.kodeData
    LEFT JOIN mst_data t3 on t1.dept_id = t3.kodeData
    LEFT JOIN mst_data t4 on t1.proses_id = t4.kodeData
    LEFT JOIN mst_data t5 on t1.birth_place = t5.kodeData
    LEFT JOIN (
    SELECT x1.parent_id, MAX(x1.edu_type) edu_type, 
    (SELECT edu_dept FROM emp_edu WHERE edu_type = MAX(x1.edu_type) AND parent_id = x1.parent_id LIMIT 1) edu_dept
    FROM emp_edu x1
    GROUP BY x1.parent_id
    ) t6 ON t6.parent_id=t1.id
    LEFT JOIN mst_data t7 ON t7.kodeData=t6.edu_type
     $sWhere
    ORDER BY name
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function exportToExcelReportJab($locId) {
    global $areaCheck;
    $sql = "
    SELECT IFNULL(t4.pos_name,'') `Jabatan`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId ";
    $sql.=" GROUP BY t4.pos_name
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportJab($locId) {
    global $areaCheck;
    $sql = "
    SELECT t4.id jabId, IFNULL(t4.pos_name,'') posName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId ";
    $sql.=" GROUP BY t4.pos_name
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

  function exportToExcelReportDep($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
    $sql = "
    SELECT IFNULL(t5.namaData,'') `Departemen`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1	
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
	LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t5.namaData
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }



  function loadTableReportDep($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
	$sql = "
    SELECT t4.id deptId, IFNULL(t5.namaData,'') deptName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1	
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
	LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";

    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t5.namaData
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

  function exportToExcelReportKat($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
    $sql = "
    SELECT IFNULL(t5.namaData,'') `Departemen`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1 
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
   JOIN mst_data t5 ON t5.kodeData=t4.kategori
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t5.namaData
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }
  
  function loadTableReportKat($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
  $sql = "
    SELECT t4.id deptId, IFNULL(t5.namaData,'') deptName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1 
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
   JOIN mst_data t5 ON t5.kodeData=t4.kategori
    WHERE t1.`status`=535
    AND t3.location IN ($areaCheck)
    ";

    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t5.namaData
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
  

  function exportToExcelReportContractExpiring($locId, $divId, $deptId, $unitId) {
    global $areaCheck,$arrParameter;
    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

    $kontrakBulan = date('m') > 9 ? date('m') + 3 - 12 : date('m') + 3;
    $kontrakTahun = date('m') > 9 ? date('Y') + 1 : date('Y');		
    $kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);

    $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) between '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."' and  '".$kontrakMax."'";
    if (!empty($locId))
      $filter.=" AND location=$locId";
    if (!empty($divId))
      $filter.=" AND div_id=$divId";
    if (!empty($deptId))
      $filter.=" AND dept_id=$deptId";
    if (!empty($unitId))
      $filter.=" AND unit_id=$unitId";

	$sql="select t.name `Nama`, t.deptName `Bagian`, IFNULL(t.posName,'') `Jabatan`, t.startDate `Tanggal Mulai`, t.expireDate `Tanggal Selesai`, expDays `Waktu Sisa` from (
	select d1.id, d1.jabId, d1.name, d2.namaData deptName, d1.posName, d1.startDate, d1.expireDate, d1.expDays from (select t1.id id, t1.div_id, t1.dept_id jabId, upper(t1.name) name, t1.pos_name posName, t1.start_date startDate, t2.end_date expireDate, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t2.end_date),' Hari') expDays from dta_pegawai t1 join emp_contract t2 on (t1.id=t2.parent_id) where t2.status='1' ".$filter." and t1.status='".$status."' AND t1.location IN ($areaCheck)) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
	union
	select t2.id id, t2.dept_id jabId, upper(t2.name) name, t3.namaData deptName, t2.pos_name posName, t2.start_date startDate, t2.end_date expireDate, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t2.end_date),' Hari') expDays from dta_pegawai t2 left join mst_data t3 on (t2.div_id=t3.kodeData) where t2.status='".$status."' AND t2.location IN ($areaCheck) ".$filter."
	) as t group by t.id
	";
	
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
}

function loadTableReportContractExpiring($locId, $divId, $deptId, $unitId) {
  global $areaCheck,$arrParameter;
  $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

  $kontrakBulan = date('m') > 9 ? date('m') + 3 - 12 : date('m') + 3;
  $kontrakTahun = date('m') > 9 ? date('Y') + 1 : date('Y');		
  $kontrakMax = $kontrakTahun.str_pad($kontrakBulan, 2, "0", STR_PAD_LEFT);

  $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) between '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."' and  '".$kontrakMax."'";

  if (!empty($locId))
    $filter.=" AND location=$locId";
  if (!empty($divId))
    $filter.=" AND div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND unit_id=$unitId";

	$sql="select * from (
	select d1.id, d1.jabId, d1.name, d2.namaData deptName, d1.posName, d1.startDate, d1.expireDate, d1.expDays from (select t1.id id, t1.div_id, t1.dept_id jabId, upper(t1.name) name, t1.pos_name posName, t1.start_date startDate, t2.end_date expireDate, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t2.end_date),' Hari') expDays from dta_pegawai t1 join emp_contract t2 on (t1.id=t2.parent_id) where t2.status='1' ".$filter." and t1.status='".$status."' AND t1.location IN ($areaCheck)) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
	union
	select t2.id id, t2.dept_id jabId, upper(t2.name) name, t3.namaData deptName, t2.pos_name posName, t2.start_date startDate, t2.end_date expireDate, CONCAT(TIMESTAMPDIFF(DAY, CURRENT_DATE, t2.end_date),' Hari') expDays from dta_pegawai t2 left join mst_data t3 on (t2.div_id=t3.kodeData) where t2.status='".$status."' AND t2.location IN ($areaCheck) ".$filter."
	) as t group by t.id
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

function exportToExcelReportContractExpired($cyear, $cmonth, $locId, $divId, $deptId, $unitId) {
 global $areaCheck,$arrParameter;
 $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

 $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) = '".$cyear.str_pad($cmonth, 2, "0", STR_PAD_LEFT)."'";
 if(empty($cyear) && empty($cmonth)) $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."'";
 if (!empty($locId))
  $filter.=" AND location=$locId";
if (!empty($divId))
  $filter.=" AND div_id=$divId";
if (!empty($deptId))
  $filter.=" AND dept_id=$deptId";
if (!empty($unitId))
  $filter.=" AND unit_id=$unitId";

	$sql="select upper(t.name) `Nama`, t.deptName `Bagian`, IFNULL(t.posName,'') `Jabatan`, t.startDate `Tangal Mulai`,   t.expireDate `Tanggal Akhir` from (
	select d1.id, d1.jabId, d1.name, d2.namaData deptName, d1.posName, d1.startDate, d1.expireDate from (select t1.id id, t1.div_id, t1.dept_id jabId, upper(t1.name) name, t1.pos_name posName, t1.start_date startDate, t2.end_date expireDate from dta_pegawai t1 join emp_contract t2 on (t1.id=t2.parent_id) where t2.status='1' ".$filter." and t1.status='".$status."' AND t1.location IN ($areaCheck)) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
	union
	select t2.id id, t2.dept_id jabId, upper(t2.name) name, t3.namaData deptName, t2.pos_name posName, t2.start_date startDate, t2.end_date expireDate from dta_pegawai t2 left join mst_data t3 on (t2.div_id=t3.kodeData) where t2.status='".$status."' ".$filter." AND t2.location IN ($areaCheck)
	) as t group by t.id
	";
	
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
}

function loadTableReportContractExpired($cyear, $cmonth, $locId, $divId, $deptId, $unitId) {
  global $areaCheck,$arrParameter;
  $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

  $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) = '".$cyear.str_pad($cmonth, 2, "0", STR_PAD_LEFT)."'";
  if(empty($cyear) && empty($cmonth)) $filter = " and concat(year(t2.end_date), LPAD(month(t2.end_date),2,'0')) <= '".date('Y').str_pad(date('m'), 2, "0", STR_PAD_LEFT)."'";
  if (!empty($locId))
    $filter.=" AND location=$locId";
  if (!empty($divId))
    $filter.=" AND div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND unit_id=$unitId";
  $sql="select * from (
  select d1.id, d1.jabId, d1.name, d2.namaData deptName, d1.posName, d1.startDate, d1.expireDate from (select t1.id id, t1.div_id, t1.dept_id jabId, upper(t1.name) name, t1.pos_name posName, t1.start_date startDate, t2.end_date expireDate from dta_pegawai t1 join emp_contract t2 on (t1.id=t2.parent_id) where t2.status='1' ".$filter." and t1.status='".$status."' AND t1.location IN ($areaCheck)) as d1 left join mst_data d2 on (d1.div_id=d2.kodeData)
  union
  select t2.id id, t2.dept_id jabId, upper(t2.name) name, t3.namaData deptName, t2.pos_name posName, t2.start_date startDate, t2.end_date expireDate from dta_pegawai t2 left join mst_data t3 on (t2.div_id=t3.kodeData) where t2.status='".$status."' ".$filter." AND t2.location IN ($areaCheck)
  ) as t group by t.id
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

function loadTableReportEmpStat($cyear, $cmonth, $locId, $divId, $deptId, $unitId) {
  global $areaCheck,$arrParameter;
  // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

  $filter = " where t2.status = '0'";
  // if(empty($cyear) && empty($cmonth)) $filter.= " and month(t2.end_date) = '".date('m')."' and year(t2.end_date) = '".date('Y')."' ";
  if(!empty($cyear) && !empty($cmonth)) $filter.= " and month(t2.end_date) = '$cmonth' and year(t2.end_date) = '$cyear' ";
  if (!empty($locId))
    $filter.=" AND t2.location=$locId";
  if (!empty($divId))
    $filter.=" AND t2.div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND t2.dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND t2.unit_id=$unitId";
 
 $sql = "select t3.namaData as deptName, t1.name as name, t1.reg_no as nik, t2.pos_name as jabatan, t2.end_date as tanggalSelesai from emp t1 join emp_phist t2 on t1.id = t2.parent_id left join mst_data t3 on t2.dept_id = t3.kodeData $filter order by t1.name";
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

function exportToExcelReportEmpStat($cyear, $cmonth, $locId, $divId, $deptId, $unitId) {
 global $areaCheck,$arrParameter;
 // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

 $filter = " where t2.status = '0'";
  // if(empty($cyear) && empty($cmonth)) $filter.= " and month(t2.end_date) = '".date('m')."' and year(t2.end_date) = '".date('Y')."' ";
  if(!empty($cyear) && !empty($cmonth)) $filter.= " and month(t2.end_date) = '$cmonth' and year(t2.end_date) = '$cyear' ";
  if (!empty($locId))
    $filter.=" AND t2.location=$locId";
  if (!empty($divId))
    $filter.=" AND t2.div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND t2.dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND t2.unit_id=$unitId";
 
 $sql = "select  t1.name as Nama,t3.namaData as Departemen, t1.reg_no as NPP, t2.pos_name as Jabatan, t2.end_date as 'Tanggal Selesai' from emp t1 join emp_phist t2 on t1.id = t2.parent_id left join mst_data t3 on t2.dept_id = t3.kodeData $filter order by t1.name";
 // echo $sql;
 // die();
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
}

function loadTableReportHistoryKat($cyear, $cmonth, $locId, $divId, $deptId, $unitId,$katId) {
  global $areaCheck,$arrParameter;
  // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

  $filter = " where t2.kategori != '0'";
  // if(empty($cyear) && empty($cmonth)) $filter.= " and month(t2.end_date) = '".date('m')."' and year(t2.end_date) = '".date('Y')."' ";
  if(!empty($cyear) && !empty($cmonth)) $filter.= " and month(t2.end_date) = '$cmonth' and year(t2.end_date) = '$cyear' ";
  if (!empty($locId))
    $filter.=" AND t2.location=$locId";
  if (!empty($divId))
    $filter.=" AND t2.div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND t2.dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND t2.unit_id=$unitId";
  if (!empty($katId))
    $filter.=" AND t2.kategori=$katId";
 
 $sql = "select t3.namaData as deptName, t4.namaData as kategori, t1.name as name, t1.reg_no as nik, t2.pos_name as jabatan, t2.end_date as tanggalSelesai from emp t1 join emp_phist t2 on t1.id = t2.parent_id left join mst_data t3 on t2.dept_id = t3.kodeData left join mst_data t4 on t2.kategori = t4.kodeData $filter order by t1.name";
 // echo $sql;
 // die();
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

function exportToExcelReportHistoryKat($cyear, $cmonth, $locId, $divId, $deptId, $unitId,$katId) {
 global $areaCheck,$arrParameter;
 // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

 $filter = " where t2.kategori != '0'";
  // if(empty($cyear) && empty($cmonth)) $filter.= " and month(t2.end_date) = '".date('m')."' and year(t2.end_date) = '".date('Y')."' ";
  if(!empty($cyear) && !empty($cmonth)) $filter.= " and month(t2.end_date) = '$cmonth' and year(t2.end_date) = '$cyear' ";
  if (!empty($locId))
    $filter.=" AND t2.location=$locId";
  if (!empty($divId))
    $filter.=" AND t2.div_id=$divId";
  if (!empty($deptId))
    $filter.=" AND t2.dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND t2.unit_id=$unitId";
  if (!empty($katId))
    $filter.=" AND t2.kategori=$katId";
 
 $sql = "select t1.name as NAMA, t1.reg_no as NPP, t2.pos_name as JABATAN,t3.namaData as DEPARTEMEN, t4.namaData as KATEGORI, t2.end_date as 'TANGGAL SELESAI' from emp t1 join emp_phist t2 on t1.id = t2.parent_id left join mst_data t3 on t2.dept_id = t3.kodeData left join mst_data t4 on t2.kategori = t4.kodeData $filter order by t1.name";
 // echo $sql;
 // die();
  try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
  } catch (PDOException $ex) {
    var_dump($ex->errorInfo);
  }
  return $result;
}



function exportToExcelReportEdu($eduId, $locId, $divId, $deptId, $unitId) {
  global $areaCheck;
    $sql = "
    SELECT IFNULL(t3.namaData,'') `Pendidikan`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1 
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1 
    LEFT JOIN (
    SELECT x1.parent_id, MAX(edu_type) edu_type
    FROM emp_edu x1
    GROUP BY x1.parent_id
    ) t2 ON t2.parent_id=t1.id
    LEFT JOIN mst_data t3 ON t3.kodeData=t2.edu_type AND t3.kodeCategory='R11'
    WHERE t1.`status`=535";
    if (!empty($locId))
      $sql.=" AND t4.location=$locId";
    if (!empty($divId))
      $sql.=" AND t4.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t4.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t4.unit_id=$unitId";

    if (!empty($eduId))
      $sql.=" AND t2.edu_type=$eduId ";
    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t3.kodeData
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportEdu($eduId, $locId, $divId, $deptId, $unitId) {
    global $areaCheck;
    $sql = "
    SELECT t3.kodeData eduId, IFNULL(t3.namaData,'') eduName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1 
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1 
    LEFT JOIN (
    SELECT x1.parent_id, MAX(edu_type) edu_type
    FROM emp_edu x1
    GROUP BY x1.parent_id
    ) t2 ON t2.parent_id=t1.id
    LEFT JOIN mst_data t3 ON t3.kodeData=t2.edu_type AND t3.kodeCategory='R11'
    WHERE t1.`status`=535";
    if (!empty($locId))
      $sql.=" AND t4.location=$locId";
    if (!empty($divId))
      $sql.=" AND t4.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t4.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t4.unit_id=$unitId";

    if (!empty($eduId))
      $sql.=" AND t2.edu_type=$eduId ";
    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t3.kodeData
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

  function exportToExcelReportAge($divId, $locId, $deptId, $unitId) {
    global $areaCheck;
    $sql = " 
    SELECT `Usia`, `Jumlah Laki-Laki`, `Jumlah Perempuan`,  `Total` FROM (
    SELECT
    CASE 
    WHEN  x1.empAge <20 THEN 1
    WHEN  x1.empAge BETWEEN 20 AND 30 THEN 2
    WHEN  x1.empAge BETWEEN 31 AND 40 THEN 3
    WHEN  x1.empAge BETWEEN 41 AND 50 THEN 4
    WHEN  x1.empAge BETWEEN 51 AND 55 THEN 5
    ELSE 5
    END `No`,
    CASE 
    WHEN  x1.empAge <20 THEN ' < 20' 
    WHEN  x1.empAge BETWEEN 20 AND 30 THEN ' 20 - 30 ' 
    WHEN  x1.empAge BETWEEN 31 AND 40 THEN ' 31 - 40 ' 
    WHEN  x1.empAge BETWEEN 41 AND 50 THEN ' 41 - 50 ' 
    WHEN  x1.empAge BETWEEN 51 AND 55 THEN ' 51 - 55 ' 
    ELSE ' >55'
    END `Usia`,
    SUM(CASE 
    WHEN  x1.gender='M' AND x1.empAge <20 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 20 AND 30 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 31 AND 40 THEN 1 
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 41 AND 50 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 51 AND 55 THEN 1
    WHEN  x1.gender='M' THEN 1
    ELSE 0
    END) `Jumlah Laki-Laki`,
    SUM(CASE 
    WHEN  x1.gender='F' AND x1.empAge <20 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 20 AND 30 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 31 AND 40 THEN 1 
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 41 AND 50 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 51 AND 55 THEN 1
    WHEN  x1.gender='F' THEN 1
    ELSE 0
    END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM (
    SELECT 
    t1.gender, t2.div_id,
    TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ) empAge
    FROM emp t1
    LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
    WHERE t1.status=535";
    if (!empty($locId))
      $sql.=" AND t2.location=$locId ";
    if (!empty($deptId))
      $sql.=" AND t2.dept_id=$deptId ";
    if (!empty($unitId))
      $sql.=" AND t2.unit_id=$unitId ";

    if (!empty($divId))
      $sql.=" AND t2.div_id=$divId ";
    $sql.=" 
    AND t2.location IN ($areaCheck)
    ) x1
    GROUP BY `Usia`
    ORDER BY `No`
    ) x2
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportAge($divId, $locId, $deptId, $unitId) {
    global $areaCheck;
    $sql = "
    SELECT
    CASE 
    WHEN  x1.empAge <20 THEN 0
    WHEN  x1.empAge BETWEEN 20 AND 30 THEN 1
    WHEN  x1.empAge BETWEEN 31 AND 40 THEN 2
    WHEN  x1.empAge BETWEEN 41 AND 50 THEN 3
    WHEN  x1.empAge BETWEEN 51 AND 55 THEN 4
    ELSE 5
    END ageId,
    CASE 
    WHEN  x1.empAge <20 THEN ' < 20' 
    WHEN  x1.empAge BETWEEN 20 AND 30 THEN ' 20 - 30 ' 
    WHEN  x1.empAge BETWEEN 31 AND 40 THEN ' 31 - 40 ' 
    WHEN  x1.empAge BETWEEN 41 AND 50 THEN ' 41 - 50 ' 
    WHEN  x1.empAge BETWEEN 51 AND 55 THEN ' 51 - 55 ' 
    ELSE ' >55'
    END ageName,
    SUM(CASE 
    WHEN  x1.gender='M' AND x1.empAge <20 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 20 AND 30 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 31 AND 40 THEN 1 
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 41 AND 50 THEN 1
    WHEN  x1.gender='M' AND x1.empAge BETWEEN 51 AND 55 THEN 1
    WHEN  x1.gender='M' THEN 1
    ELSE 0
    END) cmale,
    SUM(CASE 
    WHEN  x1.gender='F' AND x1.empAge <20 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 20 AND 30 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 31 AND 40 THEN 1 
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 41 AND 50 THEN 1
    WHEN  x1.gender='F' AND x1.empAge BETWEEN 51 AND 55 THEN 1
    WHEN  x1.gender='F' THEN 1
    ELSE 0
    END) cfemale,
    count(*) ctotal
    FROM (
    SELECT 
    t1.gender, t2.div_id,
    TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ) empAge
    FROM emp t1
    LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
    WHERE t1.status=535";
    if (!empty($locId))
      $sql.=" AND t2.location=$locId ";
    if (!empty($deptId))
      $sql.=" AND t2.dept_id=$deptId ";
    if (!empty($unitId))
      $sql.=" AND t2.unit_id=$unitId ";

    if (!empty($divId))
      $sql.=" AND t2.div_id=$divId ";
    $sql.=" 
    AND t2.location IN ($areaCheck)
    ) x1
    GROUP BY ageName
    ORDER BY ageId
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

  function exportToExcelReportCat($divId, $locId, $deptId, $unitId) {
    $sql = " 
    SELECT IFNULL(t2.namaData,'') `Status Pegawai`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t2  ON t2.kodeData=t1.cat
    WHERE t1.status=535
    ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";
    if (!empty($deptId))
      $sql.=" AND t4.dept_id=$deptId ";
    if (!empty($unitId))
      $sql.=" AND t4.unit_id=$unitId ";

    if (!empty($divId))
      $sql.=" AND t4.div_id=$divId ";
    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t2.kodeData
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportCat($divId, $locId, $deptId, $unitId) {
    global $areaCheck;
    $sql = " 
    SELECT t2.kodeData catId, IFNULL(t2.namaData,'') catName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t2  ON t2.kodeData=t1.cat
    WHERE t1.status=535
    ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";
    if (!empty($deptId))
      $sql.=" AND t4.dept_id=$deptId ";
    if (!empty($unitId))
      $sql.=" AND t4.unit_id=$unitId ";

    if (!empty($divId))
      $sql.=" AND t4.div_id=$divId ";
    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t2.kodeData
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

  function loadTableReportLoc($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
    $sql = " 
    SELECT t2.kodeData locId, IFNULL(t2.namaData,'') locName,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) cmale,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) cfemale,
    count(*) ctotal
    FROM emp t1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
    WHERE t1.status=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t2.kodeData
    ";
    // echo $sql;
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

  function exportToExcelReportLoc($locId, $divId, $deptId, $unitId) {
    global $areaCheck;
    $sql = " 
    SELECT IFNULL(t2.namaData,'') `Lokasi`,
    SUM(CASE WHEN t1.gender='M' THEN 1 ELSE 0 END) `Jumlah Laki-Laki`,
    SUM(CASE WHEN t1.gender='F' THEN 1 ELSE 0 END) `Jumlah Perempuan`,
    count(*) `Total`
    FROM emp t1
    LEFT JOIN emp_phist t3 ON t3.parent_id=t1.id
    LEFT JOIN mst_data t2  ON t2.kodeData=t3.location
    WHERE t1.status=535
    AND t3.location IN ($areaCheck)
    ";
    if (!empty($locId))
      $sql.=" AND t3.location=$locId";
    if (!empty($divId))
      $sql.=" AND t3.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t3.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t3.unit_id=$unitId";
    $sql.=" GROUP BY t2.kodeData
    ";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function exportToExcelReportStatus($locId, $stsId, $jabName) {
    global $areaCheck;
    $sql = "
    SELECT upper(t1.`name`) `Nama`, 
    t1.`reg_no` `NPP`, 
    t5.namaData `Divisi`,
    IFNULL(t4.pos_name,'') `Jabatan`, 
    t1.birth_date `Tgl. Lahir`,  
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') `Masa Kerja`
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id      
    WHERE t1.`status`=535";

    if (!empty($stsId))
      $sql.=" AND t1.cat=$stsId ";
    
    if (!empty($jabName))
      $sql.=" AND t4.pos_name='$jabName' ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";

    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t1.id";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportStatus($locId, $stsId, $jabName) {
    global $areaCheck;
    $sql = "
    SELECT upper(t1.`name`) name, 
    t1.`reg_no` reg_no, 
    t5.namaData deptName,
    t4.id jabId, 
    IFNULL(t4.pos_name,'') posName, 
    t1.birth_date birth_date,  
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id      
    WHERE t1.`status`=535";

    if (!empty($stsId))
      $sql.=" AND t1.cat=$stsId ";

    if (!empty($jabName))
      $sql.=" AND t4.pos_name='$jabName' ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";

    $sql.=" 
    AND t4.location IN ($areaCheck) 
    GROUP BY t1.id";

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
  
  function exportToExcelReportPurna($locId, $stsId, $month, $year) {
    global $areaCheck;
    $sql = "
    SELECT upper(t1.`name`) `Nama`, 
    t1.`reg_no` `NPP`, 
    t6.namaData `Status`,
    t5.namaData `Divisi`,
    IFNULL(t4.pos_name,'') `Jabatan`, 
    t1.join_date `Tgl. Masuk`,
    t1.leave_date `Tgl. Keluar`,
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') `Masa Kerja`
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id      
    LEFT JOIN mst_data t6 ON t6.kodeData=t1.cat
    WHERE t1.`status`!=535";

    if (!empty($stsId))
      $sql.=" AND t1.cat=$stsId ";
    
    if (!empty($month))
      $sql.=" AND month(t1.leave_date)=$month ";

    if (!empty($year))
      $sql.=" AND year(t1.leave_date)=$year ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";

    $sql.=" 
    AND t4.location IN ($areaCheck)
    GROUP BY t1.id";
    try {
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
    } catch (PDOException $ex) {
      var_dump($ex->errorInfo);
    }
    return $result;
  }

  function loadTableReportPurna($locId, $stsId, $month, $year) {
    global $areaCheck;
    $sql = "
    SELECT upper(t1.`name`) name, 
    t1.`reg_no` reg_no, 
    t6.namaData statusPeg,
    t5.namaData deptName,
    t4.id jabId, 
    IFNULL(t4.pos_name,'') posName, 
    t1.join_date joinDate,
    t1.leave_date leaveDate,
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja
    FROM emp t1
    LEFT JOIN emp_phist t4 ON t4.parent_id=t1.id AND t4.status=1
    LEFT JOIN mst_data t5 ON t5.kodeData=t4.dept_id      
    LEFT JOIN mst_data t6 ON t6.kodeData=t1.cat      
    WHERE t1.`status`!=535";

    if (!empty($stsId))
      $sql.=" AND t1.cat=$stsId ";

    if (!empty($month))
      $sql.=" AND month(t1.leave_date)=$month ";

    if (!empty($year))
      $sql.=" AND year(t1.leave_date)=$year ";

    if (!empty($locId))
      $sql.=" AND t4.location=$locId ";

    $sql.=" 
    AND t4.location IN ($areaCheck) 
    GROUP BY t1.id";

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
  
  function loadTablePast($status) {
    $sql = " SELECT
    t1.id id,
    t1.cat cat,
    t1.name name,
    t1.alias alias,
    t1.reg_no regNo,
    t1.birth_place birthPlace,
    t1.birth_date birthDate,
    t1.ktp_no ktpNo,
    t1.ktp_filename ktpFilename,
    t1.ktp_valid ktpValid,
    t1.ktp_lifetime ktpLifetime,
    t1.gender gender,
    t1.lembur lembur,
    t1.ktp_address ktpAddress,
    t1.ktp_prov ktpProv,
    t1.ktp_city ktpCity,
    t1.dom_address domAddress,
    t1.dom_prov domProv,
    t1.dom_city domCity,
    t1.phone_no phoneNo,
    t1.cell_no cellNo,
    t1.email email,
    t1.status status,
    t1.join_date joinDate,
    t1.leave_date leaveDate,
    t1.pic_filename picFilename,
    t3.namaData divisi,
    t4.namaData empType,
    t2.pos_name jabatan,
    replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja,
    t1.marital,
    t1.ptkp,
    t1.npwp_no npwpNo,

    t1.create_by creBy,
    t1.create_date creDate,
    t1.update_by updBy,
    t1.update_date updDate
    FROM emp t1
    LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
    LEFT JOIN mst_data t3 ON t3.kodeData=t2.div_id
    LEFT JOIN mst_data t4 ON t4.kodeData=t1.cat
    WHERE t1.status='$status';
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

  function getByIdHeader() {
    $sql = " SELECT
    t1.id id,
    t1.cat cat,
    t1.name name,
    t1.alias alias,
    t1.reg_no regNo,
    t1.birth_place birthPlace,
    t1.birth_date birthDate,
    t1.ktp_no ktpNo,
    t1.ktp_filename ktpFilename,
    t1.ktp_valid ktpValid,
    t1.ktp_lifetime ktpLifetime,
    t1.gender gender,
    t1.lembur lembur,
    t1.ktp_address ktpAddress,
    t1.ktp_prov ktpProv,
    t1.ktp_city ktpCity,
    t1.dom_address domAddress,
    t1.dom_prov domProv,
    t1.dom_city domCity,
    t1.phone_no phoneNo,
    t1.cell_no cellNo,
    t1.email email,
    t1.nation nation,
    t1.pic_filename picFilename,
    t3.namaData divisi,
    t4.namaData bagian,
    t5.namaData unit,
    t6.namaData category,
    '' lahir,
    t2.pos_name jabatan,
    '' masaKerja,
    t1.marital,
    t1.ptkp,
    t1.npwp_no npwpNo,

    t1.create_by creBy,
    t1.create_date creDate,
    t1.update_by updBy,
    t1.update_date updDate
    FROM emp t1
    LEFT JOIN emp_phist t2 ON t2.parent_id=t1.id AND t2.status=1
    LEFT JOIN mst_data t3 ON t3.kodeData=t2.div_id
    LEFT JOIN mst_data t4 ON t4.kodeData=t2.dept_id
    LEFT JOIN mst_data t5 ON t5.kodeData=t2.unit_id
    LEFT JOIN mst_data t6 ON t6.kodeData=t1.cat
    
    WHERE t1.id=$this->id";
    try {
      $stmt = $this->db->prepare($sql);
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
    $this->ktpLifetime = isset($_POST['ktpLifetime']) ? "t" : "f";
    return $this;
  }

  function processForm() {
    $this->id = $_SESSION["entity_id"];
    $this->getById();
    $this->populateWithPost();
    $cutil = new Common();
    $this->name = strtoupper($this->name);
    if (empty($this->cat)) {
      $this->cat = $_SESSION["emp_cat"];
    }

    //HANDLE FILE UPLOAD KTP
    $fileKtpTmp = $_FILES["ktpFilename"]["tmp_name"];
    $fileKtpName = $_FILES["ktpFilename"]["name"];
    if ($fileKtpTmp != "" && $fileKtpTmp != "none") {
      $ktpPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "ktp" . DS;
      if (!file_exists($ktpPath)) {
        mkdir($ktpPath, "0777", true);
      }
      $fktpName = "ktp_" . date("Ymd_His") . "_" . $this->id . "." . pathinfo($fileKtpName, PATHINFO_EXTENSION);
      if (move_uploaded_file($fileKtpTmp, $ktpPath . $fktpName)) {
        $this->ktpFilename = $fktpName;
      }
    }
    //HANDLE FILE UPLOAD FOTO
    $filePicTmp = $_FILES["picFilename"]["tmp_name"];
    $filePicName = $_FILES["picFilename"]["name"];
    if ($filePicTmp != "" && $filePicTmp != "none") {
      $picPath = HOME_DIR . DS . "files" . DS . "emp" . DS . "pic" . DS;
      if (!file_exists($picPath)) {
        mkdir($picPath, "0777", true);
      }
      $fpicName = "pic_" . date("Ymd_His") . "_" . $this->id . "." . pathinfo($filePicName, PATHINFO_EXTENSION);
      if (move_uploaded_file($filePicTmp, $picPath . $fpicName)) {
        $this->picFilename = $fpicName;
      }
    }
    if ($this->id == "") {
      $this->persist();
    } else {
      $this->update();
    }
    //GET DETAIL
    $jabId = $_POST["pJabId"];
//    echo "D-ROWS: " . count($jabId);
    $dno = 0;
    if (count($jabId) > 0) {
      $usedId = array();
      foreach ($jabId as $dId) {
        $empHist = new EmpPhist();
        $empHist->id = $dId;
        $empHist->parentId = $this->id;
        $empHist->posName = $_POST["pJabPosName"][$dno];
        $empHist->skNo = $_POST["pJabSkNo"][$dno];
        $empHist->skDate = $_POST["pJabSkDate"][$dno];
        $empHist->dirId = $_POST["pJabDirId"][$dno];
        $empHist->divId = $_POST["pJabDivId"][$dno];
        $empHist->deptId = $_POST["pJabDeptId"][$dno];
        $empHist->unitId = $_POST["pJabUnitId"][$dno];
        $empHist->provId = $_POST["pJabProvId"][$dno];
        $empHist->cityId = $_POST["pJabCityId"][$dno];
        $empHist->location = $_POST["pJabLocation"][$dno];
        $empHist->rank = $_POST["pJabRank"][$dno];
        $empHist->grade = $_POST["pJabGrade"][$dno];
        $empHist->startDate = $_POST["pJabStartDate"][$dno];
        $empHist->endDate = $_POST["pJabEndDate"][$dno];
        $empHist->leaderId = $_POST["pJabLeaderId"][$dno];
        $empHist->administrationId = $_POST["pJabAdministrationId"][$dno];
        $empHist->replacementId = $_POST["pJabReplacementId"][$dno];
        $empHist->replacement2Id = $_POST["pJabReplacement2Id"][$dno];
        $empHist->lembur = $_POST["pJabLembur"][$dno];
        $empHist->payrollId = $_POST["pJabPayrollId"][$dno];
        $empHist->prosesId = $_POST["pJabProsesId"][$dno];
	     	$empHist->groupId = empty($_POST["pJabGroupId"][$dno])? $_POST["pJabLocation"][$dno] : $_POST["pJabGroupId"][$dno];
        $empHist->penilaianId = $_POST["pJabPenilaianId"][$dno];
        $empHist->shiftId = $_POST["pJabShiftId"][$dno];
        $empHist->companyId = $_POST["pJabCompanyId"][$dno];
        $empHist->kategori = $_POST["pJabKategori"][$dno];
        $empHist->perdin = $_POST["pJabPerdin"][$dno];
        $empHist->obat = $_POST["pJabObat"][$dno];
        $empHist->topId = $_POST["pJabTopId"][$dno];
        $empHist->filename = $_POST["pJabFilename"][$dno];
        $empHist->managerId = $_POST["pJabManagerId"][$dno];
        if ($empHist->skDate == "0000-00-00" || $empHist->skDate == "")
          $empHist->skDate = null;
        if ($empHist->endDate == "0000-00-00" || $empHist->endDate == "")
          $empHist->endDate = null;
        $empHist->status = $_POST["pJabStatus"][$dno];
        $empHist->remark = $_POST["pJabRemark"][$dno];
        if ($empHist->id == "") {
          $empHist = $empHist->persist();
        } else {
          $empHist->update();
        }
        $usedId[] = $empHist->id;
        $dno++;
//        var_dump($empHist);
//        echo "<br/>";
      }
      //DELETE UNUSED ID
      $usedDtlIds = implode(",", $usedId);
     $cutil->execute("DELETE FROM emp_phist WHERE parent_id='$this->id' AND id NOT IN ($usedDtlIds)");
    } else {
     $cutil->execute("DELETE FROM emp_phist WHERE parent_id='$this->id'");
    }
    $pFasId = $_POST["pFasId"];
    $dno = 0;
    if (count($pFasId) > 0) {
      $usedId = array();
      foreach ($pFasId as $fasId) {
        $empFas = new EmpPlaf();
        $empFas->id = $fasId;
        $empFas->parentId = $this->id;
        $empFas->plafonId = $_POST["pFasPlafonId"][$dno];
        $empFas->satuanId = $_POST["pFasSatuanId"][$dno];
        $empFas->plafonValue = $_POST["pFasPlafonValue"][$dno];
        $empFas->satuanPos = $_POST["pFasSatuanPos"][$dno];
        $empFas->remark = $_POST["pFasRemark"][$dno];
        $empFas->mulai = $_POST["pFasMulai"][$dno];
        $empFas->selesai = $_POST["pFasSelesai"][$dno];
        $empFas->toleransi = $_POST["pFasToleransi"][$dno];
        if ($empFas->id == "") {
          $empFas = $empFas->persist();
        } else {
          $empFas->update();
        }
        $usedId[] = $empFas->id;
        $dno++;
      }
      $usedDtlIds = implode(",", $usedId);
      $cutil->execute("DELETE FROM emp_plafon WHERE parent_id='$this->id' AND id NOT IN ($usedDtlIds)");
    } else {
      $cutil->execute("DELETE FROM emp_plafon WHERE parent_id='$this->id'");
    }
//    die();
    return $this;
  }

}

?>
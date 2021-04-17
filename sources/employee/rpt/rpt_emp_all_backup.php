<?php
  if (!isset($menuAccess[$s]["view"]))
    echo "<script>logout();</script>";

  $fFile = "files/export/";

    $ret = getData($statId,$tipe, $gender,$eduId,$nation,$kawin,$durasi, $locId,$dirId, $divId, $deptId, $unitId);
    echo json_encode(array("sEcho" => 1, "aaData" => $ret));
    exit();
  }
  ?>
  <div class="pageheader">
    <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
    <?= getBread() ?>
    <span class="pagedesc">&nbsp;</span>
  </div>
  <?= empLocHeader(); ?>
  <div id="contentwrapper" class="contentwrapper">
    <form action="?<?php echo getPar($par, "mode") ?>" id="form" method="post" class="stdform">
      <table style="width:100%">
        <tr>
          <td style="width:50%; text-align:left; vertical-align:top;">
            <table>
             <tr>
              <td>
                <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">STATUS PEGAWAI</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboArray("cStat", array("ALL", "AKTIF", "TIDAK AKTIF"), $cStat, "onchange=\"document.getElementById('form').submit();\"", "250px") ?>
                  </div>
                </p>
                 <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">TIPE PEGAWAI</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S04' ORDER BY urutanData", "kodeData", "namaData", "cTipe", "-- TIPE --", $cTipe, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                  </div>
                </p>
                 
              </td>
            </tr>
            <tr>
              <td style="vertical-align:top;">				
                <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">LOKASI</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "cLocId", "--LOKASI--", $cLocId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                  </div>
                </p>
              </td>
              <td style="vertical-align:top; padding-top: 4px;" id="bView">
                <input type="button" value="+" style="font-size:26px; padding:0 6px;" class="btn btn_search btn-small" onclick="
                document.getElementById('bView').style.display = 'none';
                document.getElementById('bHide').style.display = 'table-cell';
                document.getElementById('dFilter').style.visibility = 'visible';              
                document.getElementById('fSet').style.height = 'auto';
                " />
              </td>
              <td style="vertical-align:top; padding-top: 4px; display:none;" id="bHide">
                <input type="button" value="-" style="font-size:26px; padding:0 8px;" class="btn btn_search btn-small" style="display:none" onclick="
                document.getElementById('bView').style.display = 'table-cell';
                document.getElementById('bHide').style.display = 'none';
                document.getElementById('dFilter').style.visibility = 'collapse';             
                document.getElementById('fSet').style.height = '0px';
                " />          
              </td>   
            </tr>			
          </table>          
          <fieldset id="fSet" style="padding:0px; border: 0px; height:0px;">
            <div id="dFilter" style="visibility:collapse;">
            <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">JENIS KELAMIN</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboKey("cGender", array("ALL", "M"=>"LAKI-LAKI", "F"=>"PEREMPUAN"), $cGender, "onchange=\"document.getElementById('form').submit();\"", "250px") ?>
                  </div>
                </p>
                <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">PENDIDIKAN</label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'R11' ORDER BY urutanData", "kodeData", "namaData", "cEduId", "--PENDIDIKAN--", $cEduId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
               <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">ASAL NEGARA</label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S01' ORDER BY urutanData", "kodeData", "namaData", "cNation", "--ASAL NEGARA--", $cNation, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">STATUS PERKAWINAN</label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S08' ORDER BY urutanData", "kodeData", "namaData", "cKawin", "--STATUS PERKAWINAN--", $cKawin, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                  <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;">LAMA KERJA</label>
                  <div class="field" style="margin-left:240px;">
                    <?php echo comboKey("cDurasi", array("ALL", "1"=>"Kurang dari 1 tahun", "2"=>"Lebih dari 1 tahun"), $cDurasi, "onchange=\"document.getElementById('form').submit();\"", "250px") ?>
                  </div>
                </p>
               <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[38]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X04' ORDER BY urutanData", "kodeData", "namaData", "cDirId", "--".strtoupper($arrParameter[38])."--", $cDirId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' AND kodeInduk = '$cDirId' ORDER BY urutanData", "kodeData", "namaData", "cDivId", "--".strtoupper($arrParameter[39])."--", $cDivId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$cDivId' ORDER BY urutanData", "kodeData", "namaData", "cDeptId", "--".strtoupper($arrParameter[40])."--", $cDeptId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
              <p>
                <label class="l-input-small" style="width:200px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
                <div class="field" style="margin-left:150px;">
                  <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$cDeptId' ORDER BY urutanData", "kodeData", "namaData", "cUnitId", "--".strtoupper($arrParameter[41])."--", $cUnitId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") ?>
                </div>
              </p>
            </div>
          </fieldset>
        </div>        
      </td>
      <td style="width:50%; text-align:right; vertical-align:top;">
        <a href="#export" id="btnExport" class="btn btn1 btn_inboxo"><span>Export</span></a>
      </td>

      <?php

      if($_GET['json'] == "excel"){      
        echo "<iframe src=\"download.php?d=exp&f=Laporan Seluruh Pegawai ".date('Y-m-d H:i').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
      } 
      ?>

    </tr>
  </table>
</form>
<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
  <thead>
    <tr>
      <th style="width:20; vertical-align: middle;">No.</th>
      <th style="vertical-align: middle;">NAMA LENGKAP</th>
      <th style="vertical-align: middle;">NPP</th>
      <th style="vertical-align: middle;">STATUS PEGAWAI</th>
      <th style="vertical-align: middle;">LOKASI</th>
      <th style="vertical-align: middle;">JABATAN</th>
      <th style="vertical-align: middle;">GOLONGAN</th>
      <th style="vertical-align: middle;">DIREKTORAT</th>
      <th style="vertical-align: middle;">DIVISI</th>
      <th style="vertical-align: middle;">DEPARTEMEN</th>
      <th style="vertical-align: middle;">UNIT</th>
      <th style="vertical-align: middle;">JENIS KELAMIN</th>
      <th style="vertical-align: middle;">TEMPAT LAHIR</th>
      <th style="vertical-align: middle;">TANGGAL LAHIR</th>
      <th style="vertical-align: middle;">USIA</th>
      <th style="vertical-align: middle;">NO KTP</th>
      <th style="vertical-align: middle;">AGAMA</th>
      <th style="vertical-align: middle;">PENDIDIKAN TERAKHIR</th>
      <th style="vertical-align: middle;">JURUSAN</th>
      <th style="vertical-align: middle;">NO REKENING</th>
      <th style="vertical-align: middle;">ALAMAT DOMISILI</th>
      <th style="vertical-align: middle;">PROPINSI</th>
      <th style="vertical-align: middle;">KOTA</th>
      <th style="vertical-align: middle;">ALAMAT KTP</th>
      <th style="vertical-align: middle;">PROPINSI</th>
      <th style="vertical-align: middle;">KOTA</th>
      <th style="vertical-align: middle;">NO TELP</th>
      <th style="vertical-align: middle;">NO HP</th>
      <th style="vertical-align: middle;">EMAIL</th>
      <th style="vertical-align: middle;">STATUS PERKAWINAN</th>
      <th style="vertical-align: middle;">JUMLAH ANAK</th>
      <th style="vertical-align: middle;">NAMA PASANGAN</th>
      <th style="vertical-align: middle;">ANAK 1</th>
      <th style="vertical-align: middle;">ANAK 2</th>
      <th style="vertical-align: middle;">ANAK 3</th>
      <th style="vertical-align: middle;">AYAH</th>
      <th style="vertical-align: middle;">IBU</th>
      <th style="vertical-align: middle;">GOL. DARAH</th>
      <th style="vertical-align: middle;">NO NPWP</th>
      <th style="vertical-align: middle;">NO BPJS TK</th>
      <th style="vertical-align: middle;">NO BPJS KESEHATAN</th>
      <th style="vertical-align: middle;">MULAI BEKERJA</th>
      <th style="vertical-align: middle;">LAMA KERJA</th>
      <th style="vertical-align: middle;">SALARY</th>
    </tr>  
  </thead>
  <tbody>

  </tbody>
</table>
</div>
<script>
jQuery(document).ready(function () {
  ot = jQuery('#datatable').dataTable({
    "bSort": true,
    "bFilter": true,
    "iDisplayStart": 0,
    "sScrollX": "100%",
    "sAjaxSource": sajax + "&json=1&cstat=<?= $cStat ?>&ctipe=<?= $cTipe ?>&cgender=<?= $cGender ?>&ceduid=<?= $cEduId ?>&cnation=<?= $cNation ?>&ckawin=<?= $cKawin ?>&cdurasi=<?= $cDurasi ?>&clocid=<?= $cLocId ?>&cdirid=<?= $cDirId ?>&cdivid=<?= $cDivId ?>&cdeptid=<?= $cDeptId ?>&cunitid=<?= $cUnitId ?>",
    "aoColumns": [
    {"mData": null, "sClass": "alignRight", "bSortable": false},
    {"mData": "empName"},
    {"mData": "empNik"},
    {"mData": "empStatus"},
    {"mData": "empLocation"},
    {"mData": "empJab"},
    {"mData": "empGrade"},
    {"mData": "empDir"},
    {"mData": "empDiv"},
    {"mData": "empDept"},
    {"mData": "empSubDept"},
    {"mData": "empGender"},
    {"mData": "empBirthPlace"},
    {"mData": "empBirthDate"},
    {"mData": "empBirthAge"},
    {"mData": "empKtpNo"},
    {"mData": "empReligion"},
    {"mData": "empLastEdu"},
    {"mData": "empLastEduDept"},
    {"mData": "empAccountNo"},
    {"mData": "empDomAddr"},
    {"mData": "empDomProv"},
    {"mData": "empDomCity"},
    {"mData": "empKtpAddr"},
    {"mData": "empKtpProv"},
    {"mData": "empKtpCity"},
    {"mData": "empPhoneNo"},
    {"mData": "empCellNo"},
    {"mData": "empEmail"},
    {"mData": "empMarital"},
    {"mData": "empFamTotalChild"},
    {"mData": "empFamPartner"},
    {"mData": "empFamChild1"},
    {"mData": "empFamChild2"},
    {"mData": "empFamChild3"},
    {"mData": "empFamFather"},
    {"mData": "empFamMother"},
    {"mData": "empBloodType"},
    {"mData": "empNpwpNo"},
    {"mData": "empBpjsTK"},
    {"mData": "empBpjsKS"},
    {"mData": "empWorkIn"},
    {"mData": "empWorkDuration"},
    {"mData": "empGaji"},
    ],
    "aaSorting": [[1, "asc"]],
    "sPaginationType": "full_numbers",
    "fnInitComplete": function (oSettings) {
      oSettings.oLanguage.sZeroRecords = "No data available";
    },
    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
      jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
      return nRow;
    },
    "bProcessing": true,
    "oLanguage": {
      "sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
    },
    "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
  });

  jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "8px").css("font-size", "14px");
  jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");


  jQuery("#btnExport").live("click", function(e){
    e.preventDefault();
    window.location = "?json=excel&params=<?= $cStat."~".$cTipe."~".$cGender."~".$cEduId."~".$cNation."~".$cKawin."~".$cDurasi."~".$cLocId."~".$cDirId."~".$cDivId."~".$cDeptId."~".$cUnitId.getPar($par, "mode"); ?>";
  });

  <?php
  if(!empty($cNation) ||!empty($cKawin) ||!empty($cDurasi) ||!empty($cEduId) ||!empty($cGender) ||!empty($cDirId) ||!empty($cDivId) || !empty($cDeptId) || !empty($cUnitId)){
    echo "jQuery('#bView > input').click();";
  }
  ?>
});
  </script>
  <?php
  function getData($statId, $tipe, $gender,$eduId,$nation,$kawin,$durasi,$locId, $dirId,$divId, $deptId, $unitId){
    global $areaCheck, $_unique_partner, $_unique_anak, $_unique_ibu, $_unique_bapak, $arrParameter;

    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	

    $filter .= " WHERE t1.id IS NOT NULL AND t3.location IN ( $areaCheck )";

    if($statId == "AKTIF"){
     $filter.=" AND t1.status=$status";
   }else if($statId == "TIDAK AKTIF"){
     $filter.=" AND t1.status!=$status";
   }


    if($gender == "M"){
     $filter.=" AND upper(t1.gender)='M'";
   }else if($gender == "F"){
     $filter.=" AND upper(t1.gender)='F'";
   }

   if($durasi == "1"){
     $filter.=" AND TIMESTAMPDIFF(MONTH,t1.join_date, CURRENT_DATE) < '12'";
   }else if($durasi == "2"){
     $filter.=" AND TIMESTAMPDIFF(MONTH,t1.join_date, CURRENT_DATE) >= '12'";
   }



if (!empty($nation))
    $filter.=" AND t1.nation=$nation";
if (!empty($eduId))
    $filter.=" AND t28.edu_type=$eduId";
if (!empty($kawin))
    $filter.=" AND t1.marital=$kawin";

   if (!empty($locId))
    $filter.=" AND t3.location=$locId";

  if (!empty($divId))
    $filter.=" AND t3.div_id=$divId";
if (!empty($dirId))
    $filter.=" AND t3.dir_id=$dirId";
  if (!empty($deptId))
    $filter.=" AND t3.dept_id=$deptId";
  if (!empty($unitId))
    $filter.=" AND t3.unit_id=$unitId";
if (!empty($tipe))
    $filter.=" AND t1.cat=$tipe";

  $sql = "SELECT 
  t1.id, t1.name empName,t27.nilaiPokok empGaji, t1.reg_no empNik, t2.namaData empStatus, t4.namaData empLocation, 
  t3.pos_name empJab, t5.namaData empGrade, t6.namaData empDir, t7.namaData empDiv, t8.namaData empDept, 
  t9.namaData empSubDept, 
  (CASE WHEN t1.gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN t1.gender = 'F' THEN 'Perempuan' ELSE '' END) END) empGender, 
  t10.namaData empBirthPlace, t1.birth_date empBirthDate, 
  CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn') empBirthAge,
  t1.ktp_no empKtpNo, t11.namaData empReligion, IFNULL(t16.namaData,'') empLastEdu, IFNULL(t17.namaData,'') empLastEduDept, 
  (SELECT CONCAT(x1.account_no, ' ( ', x2.namaData, ' )') FROM emp_bank x1 JOIN mst_data x2 ON x2.kodeData = x1.bank_id WHERE x1.parent_id = t1.id AND x1.status = 1 LIMIT 1) empAccountNo, 
  t1.dom_address empDomAddr,t1.ktp_address empKtpAddr, t12.namaData empDomProv, t13.namaData empDomCity ,t25.namaData empKtpProv,t26.namaData empKtpCity, t1.phone_no empPhoneNo, 
  t1.cell_no empCellNo, t1.email empEmail, t14.namaData empMarital, 
  t1.blood_type empBloodType, t1.npwp_no empNpwpNo, 
  t1.bpjs_no empBpjsTK, t1.bpjs_no_ks empBpjsKS, t1.join_date empWorkIn,
  REPLACE(
    CASE WHEN COALESCE(t1.leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, t1.leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, t1.leave_date) % 12, ' bln')
    END,' 0 bln','') empWorkDuration
  FROM emp t1 
  LEFT JOIN mst_data t2 ON ( t2.kodeData = t1.status AND t2.statusData = 't') 
  LEFT JOIN emp_phist t3 ON ( t3.parent_id = t1.id AND t3.status = 1 ) 
  LEFT JOIN mst_data t4 ON ( t4.kodeData = t3.location ) 
  LEFT JOIN mst_data t5 ON ( t5.kodeData = t3.grade )
  LEFT JOIN mst_data t6 ON ( t6.kodeData = t3.dir_id ) 
  LEFT JOIN mst_data t7 ON ( t7.kodeData = t3.div_id ) 
  LEFT JOIN mst_data t8 ON ( t8.kodeData = t3.dept_id ) 
  LEFT JOIN mst_data t9 ON ( t9.kodeData = t3.unit_id ) 
  LEFT JOIN mst_data t10 ON ( t10.kodeData = t1.birth_place ) 
  LEFT JOIN mst_data t11 ON ( t11.kodeData = t1.religion ) 
  LEFT JOIN mst_data t12 ON ( t12.kodeData = t1.dom_prov ) 
  LEFT JOIN mst_data t13 ON ( t13.kodeData = t1.dom_city ) 
  LEFT JOIN mst_data t14 ON ( t14.kodeData = t1.marital )
  LEFT JOIN mst_data t25 ON ( t25.kodeData = t1.ktp_prov ) 
  LEFT JOIN mst_data t26 ON ( t26.kodeData = t1.ktp_city )
  LEFT JOIN pay_pokok t27 ON ( t27.idPegawai = t1.id ) 
  LEFT JOIN emp_edu t28 ON ( t28.parent_id = t1.id ) 
 
  LEFT JOIN (
    SELECT x1.parent_id, MAX(x1.edu_type) edu_type, 
    (SELECT edu_dept FROM emp_edu WHERE edu_type = MAX(x1.edu_type) AND parent_id = x1.parent_id LIMIT 1) edu_dept
    FROM emp_edu x1
    GROUP BY x1.parent_id
    ) t15 ON t15.parent_id=t1.id
  LEFT JOIN mst_data t16 ON t16.kodeData=t15.edu_type
  LEFT JOIN mst_data t17 ON t17.kodeData=t15.edu_dept
  $filter 
  GROUP BY t1.id
  ORDER BY t1.name";
  // echo $sql;
  $res = db($sql);
  while($r = mysql_fetch_assoc($res)){	  
    $r['empBirthDate'] = getTanggal($r['empBirthDate']);
    $r['empWorkIn'] = getTanggal($r['empWorkIn']);
    $r['empGaji'] = getAngka($r['empGaji']);

    $r['empFamTotalChild'] = getField("(SELECT COUNT(*) FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_anak')");
    $r['empFamPartner'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel NOT IN (".implode(", ", $_unique_partner).") LIMIT 1)");
    $r['empFamChild1'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_anak' LIMIT 0,1)");
    $r['empFamChild2'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_anak' LIMIT 1,2)");
    $r['empFamChild3'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_anak' LIMIT 2,3)");
    $r['empFamFather'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_bapak')");
    $r['empFamMother'] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r['id']."' AND x1.rel = '$_unique_ibu')");
    $ret[] = $r;
  }

  return isset($ret) ? $ret : array();
}


function xls($statId,$tipe, $gender,$eduId,$nation,$kawin,$durasi, $locId,$dirId, $divId, $deptId, $unitId){   
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile, $cutil;
  require_once 'plugins/PHPExcel.php';

  $objPHPExcel = new PHPExcel();        
  $objPHPExcel->getProperties()->setCreator($cNama)
  ->setLastModifiedBy($cNama)
  ->setTitle("Laporan Seluruh Pegawai");

  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);  
  $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AO')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AP')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AQ')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getColumnDimension('AR')->setWidth(20); 
  $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

  $objPHPExcel->getActiveSheet()->mergeCells('A1:AR1');
  $objPHPExcel->getActiveSheet()->mergeCells('A2:AR2');
  $objPHPExcel->getActiveSheet()->mergeCells('A3:AR3');

  $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
  $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

  $objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
  $objPHPExcel->getActiveSheet()->setCellValue('A2', "Lokasi: " . (!empty($locId) ? $cutil->getMstDataDesc($locId) : "ALL") . ", ".$arrParameter[39].": " . (!empty($divId) ? $cutil->getMstDataDesc($divId) : "ALL") . ", ".$arrParameter[40].": " . (!empty($deptId) ? $cutil->getMstDataDesc($deptId) : "ALL") . ", ".$arrParameter[41].": " . (!empty($unitId) ? $cutil->getMstDataDesc($unitId) : "ALL"));

  $objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getFont()->setBold(true); 
  $objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
  $objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
  $objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA LENGKAP');
  $objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
  $objPHPExcel->getActiveSheet()->setCellValue('D4', 'STATUS PEGAWAI');
  $objPHPExcel->getActiveSheet()->setCellValue('E4', 'LOKASI');       
  $objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
  $objPHPExcel->getActiveSheet()->setCellValue('G4', 'GOLONGAN');
  $objPHPExcel->getActiveSheet()->setCellValue('H4', 'DIREKTORAT');
  $objPHPExcel->getActiveSheet()->setCellValue('I4', 'DIVISI');
  $objPHPExcel->getActiveSheet()->setCellValue('J4', 'DEPARTEMENT');
  $objPHPExcel->getActiveSheet()->setCellValue('K4', 'UNIT');
  $objPHPExcel->getActiveSheet()->setCellValue('L4', 'JENIS KELAMIN');
  $objPHPExcel->getActiveSheet()->setCellValue('M4', 'TEMPAT LAHIR');
  $objPHPExcel->getActiveSheet()->setCellValue('N4', 'TANGGAL LAHIR');
  $objPHPExcel->getActiveSheet()->setCellValue('O4', 'USIA');
  $objPHPExcel->getActiveSheet()->setCellValue('P4', 'NO. KTP');
  $objPHPExcel->getActiveSheet()->setCellValue('Q4', 'AGAMA');
  $objPHPExcel->getActiveSheet()->setCellValue('R4', 'PENDIDIKAN TERAKHIR');
  $objPHPExcel->getActiveSheet()->setCellValue('S4', 'JURUSAN');
  $objPHPExcel->getActiveSheet()->setCellValue('T4', 'NO. REKENING');
  $objPHPExcel->getActiveSheet()->setCellValue('U4', 'ALAMAT DOMISILI');
  $objPHPExcel->getActiveSheet()->setCellValue('V4', 'PROVINSI');
  $objPHPExcel->getActiveSheet()->setCellValue('W4', 'KOTA');
  $objPHPExcel->getActiveSheet()->setCellValue('X4', 'NO. TELP');
  $objPHPExcel->getActiveSheet()->setCellValue('Y4', 'NO. HP');
  $objPHPExcel->getActiveSheet()->setCellValue('Z4', 'EMAIL');
  $objPHPExcel->getActiveSheet()->setCellValue('AA4', 'STATUS PERKAWINAN');
  $objPHPExcel->getActiveSheet()->setCellValue('AB4', 'JUMLAH ANAK');
  $objPHPExcel->getActiveSheet()->setCellValue('AC4', 'NAMA PASANGAN');
  $objPHPExcel->getActiveSheet()->setCellValue('AD4', 'ANAK 1');
  $objPHPExcel->getActiveSheet()->setCellValue('AE4', 'ANAK 2');
  $objPHPExcel->getActiveSheet()->setCellValue('AF4', 'ANAK 3');
  $objPHPExcel->getActiveSheet()->setCellValue('AG4', 'AYAH');
  $objPHPExcel->getActiveSheet()->setCellValue('AH4', 'IBU');
  $objPHPExcel->getActiveSheet()->setCellValue('AI4', 'GOL. DARAH');
  $objPHPExcel->getActiveSheet()->setCellValue('AJ4', 'NO. NPWP');
  $objPHPExcel->getActiveSheet()->setCellValue('AK4', 'NO. BPJS TK');
  $objPHPExcel->getActiveSheet()->setCellValue('AL4', 'NO. BPJS KESEHATAN');
  $objPHPExcel->getActiveSheet()->setCellValue('AM4', 'MULAI BEKERJA');
  $objPHPExcel->getActiveSheet()->setCellValue('AN4', 'LAMA BEKERJA');
  $objPHPExcel->getActiveSheet()->setCellValue('AO4', 'ALAMAT DOMISILI');
  $objPHPExcel->getActiveSheet()->setCellValue('AP4', 'PROVINSI');
  $objPHPExcel->getActiveSheet()->setCellValue('AQ4', 'KOTA');
  $objPHPExcel->getActiveSheet()->setCellValue('AR4', 'SALARY');



  $rows = 5;
  foreach(getData($statId, $gender,$eduId,$nation,$kawin,$durasi,$locId, $divId, $deptId, $unitId) as $r){
    $no++;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':AO'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);       
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[empName]));
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[empNik]);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[empStatus]);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[empLocation]);      
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[empJab]);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[empGrade]);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $r[empDir]);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $r[empDiv]);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $r[empDept]);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $r[empSubDept]);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$rows, $r[empGender]);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $r[empBirthPlace]);
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$rows, $r[empBirthDate]);
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$rows, $r[empBirthAge]);
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$rows, $r[empKtpNo]);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$rows, $r[empReligion]);
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$rows, $r[empLastEdu]);
    $objPHPExcel->getActiveSheet()->setCellValue('S'.$rows, $r[empLastEduDept]);
    $objPHPExcel->getActiveSheet()->setCellValue('T'.$rows, $r[empAccountNo]);
    $objPHPExcel->getActiveSheet()->setCellValue('U'.$rows, $r[empDomAddr]);
    $objPHPExcel->getActiveSheet()->setCellValue('V'.$rows, $r[empDomProv]);
    $objPHPExcel->getActiveSheet()->setCellValue('W'.$rows, $r[empDomCity]);
    $objPHPExcel->getActiveSheet()->setCellValue('X'.$rows, $r[empPhoneNo]);
    $objPHPExcel->getActiveSheet()->setCellValue('Y'.$rows, $r[empCellNo]);
    $objPHPExcel->getActiveSheet()->setCellValue('Z'.$rows, $r[empEmail]);
    $objPHPExcel->getActiveSheet()->setCellValue('AA'.$rows, $r[empMarital]);
    $objPHPExcel->getActiveSheet()->setCellValue('AB'.$rows, $r[empFamTotalChild]);
    $objPHPExcel->getActiveSheet()->setCellValue('AC'.$rows, $r[empFamPartner]);
    $objPHPExcel->getActiveSheet()->setCellValue('AD'.$rows, $r[empFamChild1]);
    $objPHPExcel->getActiveSheet()->setCellValue('AE'.$rows, $r[empFamChild2]);
    $objPHPExcel->getActiveSheet()->setCellValue('AF'.$rows, $r[empFamChild3]);
    $objPHPExcel->getActiveSheet()->setCellValue('AG'.$rows, $r[empFamFather]);
    $objPHPExcel->getActiveSheet()->setCellValue('AH'.$rows, $r[empFamMother]);
    $objPHPExcel->getActiveSheet()->setCellValue('AI'.$rows, $r[empBloodType]);
    $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$rows, $r[empNpwpNo]);
    $objPHPExcel->getActiveSheet()->setCellValue('AK'.$rows, $r[empBpjsTK]);
    $objPHPExcel->getActiveSheet()->setCellValue('AL'.$rows, $r[empBpjsKS]);
    $objPHPExcel->getActiveSheet()->setCellValue('AM'.$rows, $r[empWorkIn]);
    $objPHPExcel->getActiveSheet()->setCellValue('AN'.$rows, $r[empWorkDuration]);
    $objPHPExcel->getActiveSheet()->setCellValue('AO'.$rows, $r[empKtpAddr]);
    $objPHPExcel->getActiveSheet()->setCellValue('AP'.$rows, $r[empKtpCity]);
    $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$rows, $r[empKtpProv]);
    $objPHPExcel->getActiveSheet()->setCellValue('AR'.$rows, $r[empGaji]);

    $rows++;              
  }

  $rows--;
  $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('N4:N'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('O4:O'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('P4:P'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Q4:Q'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('R4:R'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('S4:S'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('T4:T'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('U4:U'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('V4:V'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('W4:W'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('X4:X'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Y4:Y'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('Z4:Z'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AA4:AA'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AB4:AB'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AC4:AC'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AD4:AD'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AE4:AE'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AF4:AF'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AG4:AG'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AH4:AH'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AI4:AI'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AJ4:AJ'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AK4:AK'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AL4:AL'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AM4:AM'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AN4:AN'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AO4:AO'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AP4:AP'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AQ4:AQ'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('AR4:AR'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

  $objPHPExcel->getActiveSheet()->getStyle('A1:AR'.$rows)->getAlignment()->setWrapText(true);
  $objPHPExcel->getActiveSheet()->getStyle('A1:AR'.$rows)->getFont()->setName('Arial');
  $objPHPExcel->getActiveSheet()->getStyle('A6:AR'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

  $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
  $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325); 

  $objPHPExcel->getActiveSheet()->setTitle("Laporan Seluruh Pegawai");
  $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file
  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
  $objWriter->save($fFile."Laporan Seluruh Pegawai ".date('Y-m-d H:i').".xls");
}

?>
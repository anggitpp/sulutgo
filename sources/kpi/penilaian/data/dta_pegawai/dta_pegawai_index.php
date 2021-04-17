<?php
global $s, $par, $menuAccess;
$folder_upload = "images/foto/";
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){

	case "deletePeg":
	 deletePeg();
	break;

	case "delete_file":
	 delete_file();
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "dta_pegawai_form.php";
	else
		echo "<script>closeBox();</script>";
	break;
	case "kode":
	 kode();
	break;
    case "bulan":
	 bulan();
	break;
	
	case "add":
	if(isset($menuAccess[$s]['edit']))
		include "dta_pegawai_form.php";
	else
		echo "<script>closeBox();</script>";	
	break;
	
	case "data":
	if(isset($menuAccess[$s]['add']))
		include "dta_pegawai_data.php";
	else
		echo "<script>closeBox();</script>";
	break;

	case "dataTambah":
	if(isset($menuAccess[$s]['add']))
		include "form_pegawai.php";
	else
		echo "<script>closeBox();</script>";
	break;
	
	case "del":
		if(isset($menuAccess[$s]['delete']))
			db("delete from pen_pegawai where id='".$par[id]."'");		
		include "dta_pegawai_detail.php";	
	break;
	
	case "det":	
		include "dta_pegawai_detail.php";	
	break;
    
    case "getLvl_1":
	 getLvl_1();
	break;
    
    case "getLvl_2":
	 getLvl_2();
	break;
    
    case "getLvl_3":
	 getLvl_3();
	break;
    
    case "getLvl_4":
	 getLvl_4();
	break;
    case "subData":
       subData();
        break;
	
	default:
	include "dta_pegawai_list.php";
	break;
}

function subData()
{
    global $par;

    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]' order by namaData");

    echo implode("\n", $data);
}

function getLvl_4() {
  global $s, $id, $inp, $par, $arrParameter;
  
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from str_org where kodeInduk='$par[str_lv3]' order by namaData asc");
  echo implode("\n", $data);
}

function getLvl_3() {
  global $s, $id, $inp, $par, $arrParameter;
  
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from str_org where kodeInduk='$par[str_lv2]' order by namaData asc");
  echo implode("\n", $data);
}

function getLvl_2() {
  global $s, $id, $inp, $par, $arrParameter;
  
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from str_org where kodeInduk='$par[str_lv1]' order by namaData asc");
  echo implode("\n", $data);
}

function getLvl_1() {
  global $s, $id, $inp, $par, $arrParameter;
  
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from str_org where kodeInduk='$par[str_kategori]' order by namaData asc");
  echo implode("\n", $data);
}

function kode() {
  global $s, $id, $inp, $par, $arrParameter;
  // echo "Test";
  // echo "select concat(idKode, '\t', subKode) from pen_setting_kode where statusKode='t' and kodeTipe='$par[tipePenilaian]' order by idKode";
  $data = arrayQuery("select concat(idKode, '\t', subKode) from pen_setting_kode where statusKode='t' and kodeTipe='$par[tipePenilaian]' order by idKode");
  echo implode("\n", $data);
}

function bulan() {
  global $s, $id, $inp, $par, $arrParameter;
  // echo "Test";
  // echo "select concat(idKode, '\t', subKode) from pen_setting_kode where statusKode='t' and kodeTipe='$par[tipePenilaian]' order by idKode";
  $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where kodeInduk='$par[tahunPenilaian]' order by urutanData");
  echo implode("\n", $data);
}

function deletePeg(){
	global $s, $id, $inp, $par, $arrParameter;
    
    $cek = getField("select id_pegawai from pen_realisasi_individu where id_pegawai = $par[idPegawai]");
    
    if(!empty($cek))
    {
        echo"<script>alert('Data tidak bisa dihapus, Pegawai sudah dinilai!');</script>";
    }
    else
    {
        db("DELETE FROM emp WHERE id = $par[idPegawai]");
    	db("DELETE FROM emp_phist WHERE parent_id = $par[idPegawai]");
    	db("DELETE FROM pen_pegawai WHERE idPegawai = $par[idPegawai]");
        
        echo"<script>alert('Data berhasil di hapus');</script>";
    }
	
	echo"<script>window.location='index.php?".getPar($par,"mode, idPegawai")."';</script>";
}

function delete_file(){
	global $s, $inp, $par, $folder_upload;
	$file = getField("SELECT pic_filename FROM emp WHERE id ='$par[idf]'");
	if (file_exists($folder_upload . $file) and $file != "") unlink($folder_upload . $file);

	$sql = "UPDATE emp set pic_filename = '' WHERE id = '$par[idf]'";
	db($sql);
	echo "<script>alert('hapus foto berhasil');window.location.href='popup.php?par[mode]=dataTambah&par[idPegawai]=$par[idf]".getPar($par,"mode")."';</script>";
}
?>
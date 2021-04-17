<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/penilaian/setting/konversi/";
switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "konversi_penilaian_edit.php";
	else
		include "konversi_penilaian_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "konversi_penilaian_edit.php";
	else
		include "konversi_penilaian_list.php";
	break;
	case "tipe":
	tipe();
	break;

	case "dlg":
	if(isset($menuAccess[$s]['edit']))
		include "dlg_ambil_data.php";
	else
		echo "<script>closeBox();</script>";
	break;

	case "del":
	if(isset($menuAccess[$s]['delete']))
		hapusData();
	else
		include "konversi_penilaian_list.php";
	break;

	case "delFile":
	hapusFile();
	break;

	default:
	include "konversi_penilaian_list.php";
	break;
}

function hapusFile() {
	global $s, $inp, $par, $fFile, $cUsername;
	$fileSK = getField("select skKonversi from pen_setting_konversi WHERE idKonversi='$par[idKonversi]'");
	if (file_exists($fFile . $fileSK) and $fileSK != "")
		unlink($fFile . $fileSK);
	$sql = "update pen_setting_konversi set skKonversi='' where idKonversi='$par[idKonversi]'";
	db($sql);
	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}
function tipe() {
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(idKode, '\t', subKode) from pen_setting_kode where statusKode='t' and idTipe='$par[tipePenilaian]' and kodeCategory='S03' order by namaData");
  return implode("\n", $data);
}
function hapusData(){
	global $s,$par, $fFile;			
	$fileSK = getField("select skKonversi from pen_setting_konversi where idKonversi='$par[idKonversi]'");
	if(file_exists($fFile.$fileSK) and $fileSK!="")unlink($fFile.$fileSK);

	$sql="DELETE FROM pen_setting_konversi WHERE idKonversi = '$par[idKonversi]'";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?par[kodeKonversi]=$par[kodeKonversi]".getPar($par, "mode,idKonversi,kodeKonversi")."';</script>";
}
?>
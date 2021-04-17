<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/penilaian/setting/kode/";
switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "kode_penilaian_edit.php";
	else
		include "kode_penilaian_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "kode_penilaian_edit.php";
	else
		include "kode_penilaian_list.php";
	break;

	case "del":
	if(isset($menuAccess[$s]['delete']))
		hapusData();
	else
		include "kode_penilaian_list.php";
	break;

	case "delFile":
	hapusFile();
	break;

	case "chk":
	chk();
	break;

	default:
	include "kode_penilaian_list.php";
	break;
}

function hapusData() {
	global $s, $inp, $par, $fFile, $cUsername;
	$fileSK = getField("select skKode from pen_setting_kode WHERE idKode='$par[idKode]'");
	if (file_exists($fFile . $fileSK) and $fileSK != "")
		unlink($fFile . $fileSK);

	$sql = "DELETE FROM pen_setting_kode WHERE idKode='$par[idKode]'";
	db($sql);

	echo "<script>window.location='?" . getPar($par, "mode,idKode") . "';</script>";
}

function hapusFile() {
	global $s, $inp, $par, $fFile, $cUsername;
	$fileSK = getField("select skKode from pen_setting_kode WHERE idKode='$par[idKode]'");
	if (file_exists($fFile . $fileSK) and $fileSK != "")
		unlink($fFile . $fileSK);
	$sql = "update pen_setting_kode set skKode='' where idKode='$par[idKode]'";
	db($sql);
	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function chk(){
	global $s, $inp, $par, $fFile, $cUsername;

	$resKode = getField("SELECT CONCAT(kodeKonversi, '~', kodeAspek, '~', kodePrespektif) FROM pen_setting_kode WHERE idKode = '$par[idKode]'");
	list($kodeKonversi, $kodeAspek, $kodePrespektif) = explode("~", $resKode);
	
	$resKonversi = getField("SELECT COUNT(*) FROM pen_setting_konversi WHERE kodeKonversi = '$kodeKonversi'");
	$resAspek = getField("SELECT COUNT(*) FROM pen_setting_aspek WHERE kodeAspek = '$kodeAspek'");
	$resPrespektif = getField("SELECT COUNT(*) FROM pen_setting_prespektif WHERE kodePrespektif = '$kodePrespektif'");
	
	if($resKonversi > 0 || $resAspek > 0 || $resPrespektif > 0){
		echo "sorry, data has been used";
	}
}
?>
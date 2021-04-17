<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/penilaian/setting/penilaian/";

switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "setting_penilaian_edit.php";
	else
		include "setting_penilaian_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "setting_penilaian_edit.php";
	else
		include "setting_penilaian_list.php";
	break;

	case "del":
	if(isset($menuAccess[$s]['delete']))
		hapusData();
	else
		include "setting_penilaian_list.php";
	break;

	case "delFile":
	hapusFile();
	break;

	default:
	include "setting_penilaian_list.php";
	break;
}

function hapusFile() {
	global $s, $inp, $par, $fFile, $cUsername;
	$skSetting = getField("select skSetting from pen_setting_penilaian WHERE idSetting='$par[idSetting]'");
	if (file_exists($fFile . $skSetting) and $skSetting != "")
		unlink($fFile . $skSetting);
	$sql = "update pen_setting_penilaian set skSetting='' where idSetting='$par[idSetting]'";
	db($sql);
	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapusData(){
	global $s,$inp,$par,$fFile,$cUsername;
	$skSetting = getField("select skSetting from pen_setting_penilaian where idSetting='$par[idSetting]'");
	if(file_exists($fFile.$skSetting) and $skSetting!="")unlink($fFile.$skSetting);

	$sql="delete from pen_setting_penilaian where idSetting='$par[idSetting]'";
	db($sql);
	echo "<script>window.location='?".getPar($par, "mode,idSetting")."';</script>";
}

/* End of file setting_penilaian_index.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/setting_penilaian/setting_penilaian_index.php */
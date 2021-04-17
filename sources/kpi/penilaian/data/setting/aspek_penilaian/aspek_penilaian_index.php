<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "aspek_penilaian_edit.php";
	else
		include "aspek_penilaian_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "aspek_penilaian_edit.php";
	else
		include "aspek_penilaian_list.php";
	break;

	case "dlgTarget":
	if(isset($menuAccess[$s]['edit']))
		include "dlg_target.php";
	else
		echo "<script>closeBox();</script>";
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
		include "aspek_penilaian_list.php";
	break;

	case "delFile":
	hapusFile();
	break;

	default:
	include "aspek_penilaian_list.php";
	break;
}

function hapusData(){
	global $s,$par;			

	$sql="DELETE FROM pen_setting_aspek WHERE idAspek = '$par[idAspek]'";
	// echo $sql;
	db($sql);

	$sql="DELETE FROM pen_setting_aspek_detail WHERE idAspek = '$par[idAspek]'";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?par[kodeAspek]=$par[kodeAspek]".getPar($par, "mode,idAspek,kodeAspek")."';</script>";
}
?>
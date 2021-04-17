<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "prespektif_penilaian_edit.php";
	else
		include "prespektif_penilaian_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "prespektif_penilaian_edit.php";
	else
		include "prespektif_penilaian_list.php";
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
		include "prespektif_penilaian_list.php";
	break;

	case "addIndikator":
	if(isset($menuAccess[$s]['add']))
		include "prespektif_penilaian_form.php";
	else
		include "prespektif_penilaian_indikator.php";
	break;
	
	case "editIndikator":
	if(isset($menuAccess[$s]['edit']))
		include "prespektif_penilaian_form.php";
	else
		include "prespektif_penilaian_indikator.php";
	break;
	
	case "delIndikator":
	if(isset($menuAccess[$s]['add']))
		hapusIndikator();
	else
		include "prespektif_penilaian_indikator.php";
	break;
	
	case "det":
	if(isset($menuAccess[$s]['edit']))
		include "prespektif_penilaian_indikator.php";
	else
		include "prespektif_penilaian_list.php";
	break;
	
	case "delFile":
	hapusFile();
	break;
    
    case "getKode" :
		getKode();
	break;        

	default:
	include "prespektif_penilaian_list.php";
	break;
}

function getKode(){
    global $s, $id, $inp, $par, $arrParameter,$db;
    $getData = queryAssoc("SELECT idKode, subKode FROM pen_setting_kode WHERE kodeTipe = $par[idTipeJS] AND statusKode = 't' order by idKode ASC");
    echo json_encode($getData);
}

function hapusIndikator(){
	global $s,$par;			
	
	$sql="DELETE FROM pen_setting_prespektif_indikator WHERE idPrespektif = '$par[idPrespektif]' and (kodeIndikator = '$par[kodeIndikator]' or indukIndikator = '$par[kodeIndikator]')";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?par[mode]=det".getPar($par, "mode,kodeIndikator")."';</script>";
}

function hapusData(){
	global $s,$par;			
    
   

	$sql="DELETE FROM pen_setting_prespektif WHERE idPrespektif = '$par[idPrespektif]'";
	// echo $sql;
	db($sql);

	$sql="DELETE FROM pen_setting_prespektif_detail WHERE idPrespektif = '$par[idPrespektif]'";
	// echo $sql;
	db($sql);
    
    $sql="DELETE FROM pen_setting_prespektif_indikator WHERE idPrespektif = '$par[idPrespektif]'";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?".getPar($par, "mode,idPrespektif")."';</script>";
}
?>
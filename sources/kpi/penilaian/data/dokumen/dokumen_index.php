<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

$fFile = "files/penilaian/dokumen/";
switch($par[mode]){
	case "add":
	if(isset($menuAccess[$s]['add']))
		include "dokumen_edit.php";
	else
		include "dokumen_list.php";
	break;

	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "dokumen_edit.php";
	else
		include "dokumen_list.php";
	break;

	case "del":
	if(isset($menuAccess[$s]['delete']))
		hapusData();
	else
		include "dokumen_list.php";
	break;

	case "delFile":
	hapusFile();
	break;

	default:
	include "dokumen_list.php";
	break;
}

function hapusFile() {
	global $s, $inp, $par, $fFile, $cUsername;
	$fileDokumen = getField("select fileDokumen from pen_dokumen WHERE kodeDokumen='$par[kodeDokumen]'");
	if (file_exists($fFile . $fileDokumen) and $fileDokumen != "")
		unlink($fFile . $fileDokumen);
	$sql = "update pen_dokumen set fileDokumen='' where kodeDokumen='$par[kodeDokumen]'";
	db($sql);
	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapusData(){
	global $s,$inp,$par,$fFile,$cUsername;
	$fileDokumen = getField("select fileDokumen from pen_dokumen where kodeDokumen='$par[kodeDokumen]'");
	if(file_exists($fFile.$fileDokumen) and $fileDokumen!="")unlink($fFile.$fileDokumen);

	$sql="delete from pen_dokumen where kodeDokumen='$par[kodeDokumen]'";
	db($sql);
	echo "<script>window.location='?".getPar($par, "mode,kodeDokumen")."';</script>";
}
?>
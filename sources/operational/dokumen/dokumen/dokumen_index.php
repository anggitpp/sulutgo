<?php
if(!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$path_document 	= "files/perencanaan/dokumen/";

$arr_status 	= ["t" => "Aktif", "f" => "Tidak Aktif"];

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
			delete();
		else
			include "dokumen_list.php";
	break;

	case "remove":
		remove();
	break;

	default:
		include "dokumen_list.php";
	break;
}

function remove() {

	global $par, $path_document;

	$file = getField("SELECT `fileDokumen` FROM `pen_dokumen` WHERE `kodeDokumen` = '$par[id]'");

	if (file_exists($path_document . $file))
		unlink($path_document . $file);

	db("UPDATE `pen_dokumen` SET `fileDokumen` = '' WHERE `kodeDokumen` = '$par[id]'");

	echo "<script>window.location='?" . getPar($par, "mode") . "&par[mode]=edit';</script>";
}

function delete() {

	global $par, $path_document;

	$file = getField("SELECT `fileDokumen` FROM `pen_dokumen` WHERE `kodeDokumen` = '$par[id]'");

	if(file_exists($path_document . $file))
		unlink($path_document . $file);

	db("DELETE FROM `pen_dokumen` WHERE `kodeDokumen` = '$par[id]'");

	echo "<script>window.location='?".getPar($par, "mode, id")."';</script>";
}
?>
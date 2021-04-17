<?php
global $s, $par, $menuAccess;
if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){
	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "sasaran_penilaian_edit.php";
	else
		include "sasaran_penilaian_list.php";
	break;
	
	case "addSubyek":
	if(isset($menuAccess[$s]['edit']))
		include "dlg_subyek.php";
	else
		echo "<script>closeBox();</script>";
	break;

	case "editSubyek":
	if(isset($menuAccess[$s]['edit']))
		include "dlg_subyek.php";
	else
		echo "<script>closeBox();</script>";
	break;

	case "ambil":
	if(isset($menuAccess[$s]['edit']))
		include "dlg_ambil_data.php";
	else
		echo "<script>closeBox();</script>";
	break;

	default:
	include "sasaran_penilaian_list.php";
	break;
}
?>
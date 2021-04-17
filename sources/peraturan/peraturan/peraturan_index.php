<?php
global $s, $par, $menuAccess;

if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){
	case 'det':
		include "peraturan_view.php";
		break;

	case 'add':
		include "peraturan_edit.php";
		break;

	case 'edit':
		include "peraturan_edit.php";
		break;

	case "del":
	if(isset($menuAccess[$s]['delete']))
		del();
	else
		include "peraturan_view.php";
	break;

	default:
	include "peraturan_list.php";
	break;
}

function del(){
	global $s,$par;			

	$sql="DELETE FROM per_pasal_ayat WHERE idAyat = '$par[idAyat]'";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?par[mode]=det".getPar($par, "mode,idAyat")."';</script>";
}
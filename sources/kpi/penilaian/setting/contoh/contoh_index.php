<?php
global $s, $par, $menuAccess;

if(!isset($menuAccess[$s]['view']))
	echo "<script>logout();</script>";

switch($par[mode]){
	case "edit":
	if(isset($menuAccess[$s]['edit']))
		include "contoh_edit.php";
	else
		include "contoh_list.php";
	break;

	case "add":
	if(isset($menuAccess[$s]['add']))
		include "contoh_edit.php";
	else
		include "contoh_list.php";
	break;	

	case "del":
	if(isset($menuAccess[$s]['delete']))
		del();
	else
		include "contoh_list.php";
	break;
	
	default:
	include "contoh_list.php";
	break;
}

function del(){
	global $s,$par;			

	$sql="DELETE FROM pen_contoh WHERE kodeContoh = '$par[kodeContoh]'";
	// echo $sql;
	db($sql);

	echo "<script>window.location='?".getPar($par, "mode,kodeContoh")."';</script>";
}
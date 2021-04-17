<?php

global $s,$menuAccess,$par;

$fFoto = "files/catatan/catatan/";



switch($par[mode]){



	case "add":

	if(isset($menuAccess[$s]['add']))

		include 'catatan_edit.php';		

	else

		include "catatan_list.php";

	break;



	case 'del':

	if (isset($menuAccess[$s]["delete"]))

		deleteData();

	else

		include 'catatan_list.php';

	break;



	case "delFoto":

	if(isset($menuAccess[$s]['delete']))

		hapusFoto();

	else

		include 'catatan_list.php';		

	break;



	case "edit":

	if(isset($menuAccess[$s]['edit']))

		include 'catatan_edit.php';		

	else

		include "catatan_list.php";

	break;



	// case "kota":

	// kota();

	// break;



	default:

	include 'catatan_list.php';

	break;



}



function deleteData(){

	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;

	repField();



	$file = getField("SELECT File FROM catatan_sistem WHERE idCatatan='$par[idCatatan]'");



	// if (file_exists($fFoto . $file) and $file != "")

	// 	unlink($fFoto . $file);


	$sql = "DELETE from catatan_sistem where idCatatan='$par[idCatatan]'";
	db($sql);

	// debugRacgam($sql);
	// die();
	echo "

	<script type=\"text/javascript\">

		window.location.href='?".getPar($par,'mode,idCatatan')."';

	</script>

	";



}



function hapusFoto() {

	global $s, $inp, $par, $dFile, $cUsername, $fFoto;

	$file = getField("SELECT File FROM catatan_sistem WHERE idCatatan='$par[idCatatan]'");



	if (file_exists($fFoto . $file) and $file != "")

		unlink($fFoto . $file);



	$sql = "UPDATE catatan_sistem SET File = '' WHERE idCatatan='$par[idCatatan]'";

	db($sql);



	echo "

	<script>

		window.parent.location = 'index.php?par[mode]=edit&par[idCatatan]=$par[idCatatan]".getPar('idCatatan')."';

	</script>";		

}



// function kota() {

// 	global $s, $id, $inp, $par, $arrParameter, $db;

// 	$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[propinsi]' order by namaData");

// 	echo implode("\n", $data);

// }



?>
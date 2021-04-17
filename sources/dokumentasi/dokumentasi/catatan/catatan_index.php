<?php

global $s,$menuAccess,$par;

$fFoto = "files/catatan/catatan/";
$fRencana = "files/dokumentasi/rencana/";



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

	case 'delDok':
	if (isset($menuAccess[$s]["delete"]))
		hapusDoc();
	else
		include 'catatan_list.php';
	break;


	case "delFoto":
	if(isset($menuAccess[$s]['delete']))
		hapusFoto();
	else
		include 'catatan_list.php';		
	break;

	case "tambahFile":
	if(isset($menuAccess[$s]["add"])) empty($_submit) ? formFile() : tambahFile(); else include "catatan_list.php";
	break;

	case "editDoc":
	if(isset($menuAccess[$s]["edit"])) empty($_submit) ? formFile() : ubahFile(); else include "catatan_list.php";
	break;


	case "edit":

	if(isset($menuAccess[$s]['edit']))
		include 'catatan_edit.php';		
	else
		include "catatan_list.php";
	break;

	default:
	include 'catatan_list.php';
	break;
}



function deleteData(){

	global $s, $par, $arrParameter, $cUsername, $db, $inp, $fFoto;

	repField();



	$file = getField("SELECT File FROM catatan_sistem WHERE idCatatan='$par[idCatatan]'");

	if (file_exists($fFoto . $file) and $file != "")
		unlink($fFoto . $file);


	$sql = "DELETE from catatan_sistem where idCatatan='$par[idCatatan]'";
	db($sql);

	$sql2 = "DELETE from doc_file where idRencana='$par[idCatatan]'";
	db($sql2);

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

function formFile(){
	global $s,$inp,$par,$menuAccess,$fManual,$cUsername,$arrTitle;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM doc_file WHERE id='$par[idDoc]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	// $r[appr_div_by] = empty($r[appr_div_by]) ? $cUsername : $r[appr_div_by];

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	 	
			<p style=\"position:absolute;right:5px;top:5px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
			</p>
			<div id=\"general\" class=\"subcontent\">


				<p>
					<label class=\"l-input-small\">Nama</label>
					<div class=\"field\">								
						<input type=\"text\" id=\"inp[namaFile]\" name=\"inp[namaFile]\"  value=\"$r[namaFile]\" class=\"mediuminput\" />
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[file])?
						"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:360px;\">
							<input type=\"file\"  id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[file])."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delDok".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"fieldB\">
						<textarea style=\"width:350px;height:50px;\" id=\"inp[keterangan]\" name=\"inp[keterangan]\">$r[keterangan]</textarea>
					</span>
				</p>
			</div>



		</form>	
	</div>";
	echo $text;
}

function tambahFile(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$id = getField("select id from doc_file order by id desc limit 1")+1;
	$inp[file] = uploadDok($id);
	$sql = "insert into doc_file (id, idRencana, namaFile, file, keterangan, createDate, createBy) values ('$id', '$par[idCatatan]','$inp[namaFile]','$inp[file]','$inp[keterangan]','".date('Y-m-d H:i:s')."','$cUsername')";
	db($sql);
	
	echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
}

function ubahFile(){
	global $s, $inp, $par, $cUsername, $arrParam;
	$inp[file] = uploadDok($par[idDoc]);
	$sql = "update doc_file set namaFile = '$inp[namaFile]', file = '$inp[file]', keterangan = '$inp[keterangan]', updateBy = '$cUsername', updateDate = '".date('Y-m-d H:i:s')."' where id = '$par[idDoc]'";
	db($sql);
	
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}

function uploadDok($id) {
	global $s, $inp, $par, $fRencana;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fRencana);
		$foto_file = "rencana_dok-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fRencana, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_file where id ='$id'");

	return $foto_file;
}

function hapusDok() {
	global $s, $inp, $par, $fRencana, $cUsername;

	$foto_file = getField("select file from doc_file where id='$par[idDoc]'");
	if (file_exists($fRencana . $foto_file) and $foto_file != "")
		unlink($fRencana . $foto_file);

	$sql = "delete from doc_file where id='$par[idDoc]'";
	db($sql);

	echo "<script>window.location='?par[mode]=editDoc" . getPar($par, "mode,idDoc") . "';</script>";
}

function uploadFile($id) {
	global $s, $inp, $par, $fRencana;
	$fileUpload = $_FILES["file"]["tmp_name"];
	$fileUpload_name = $_FILES["file"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $fRencana);
		$foto_file = "rencana-" . time() . "." . getExtension($fileUpload_name);
		fileRename($fRencana, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file from doc_rencana where id ='$id'");

	return $foto_file;
}

function hapusFile() {
	global $s, $inp, $par, $fRencana, $cUsername;

	$foto_file = getField("select file from doc_rencana where id='$par[id]'");
	if (file_exists($fRencana . $foto_file) and $foto_file != "")
		unlink($fRencana . $foto_file);

	$sql = "update doc_rencana set file='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapusDoc(){
					global $s,$inp,$par,$fRencana,$cUsername;
					$foto_file = getField("select file from doc_file where id='$par[idDoc]'");
					if(file_exists($fRencana.$foto_file) and $foto_file!="")unlink($fRencana.$foto_file);

					$sql="delete from doc_file where id='$par[idDoc]'";
					db($sql);
					echo "<script>window.location='?par[mode]=edit&par[idCatatan]=$par[idCatatan]" . getPar($par, "mode,idDoc,id") . "';</script>";
				}

?>
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/dinas/";

	$fSK = "files/SK/";

	function hapusFile(){
		global $s,$inp,$par,$fSK,$cUsername;
		$file = getField("select file from str_periode where idSperiode='$par[idSperiode]'");
		if(file_exists($fSK.$file) and $file!="")unlink($fSK.$file);
		
		$sql="update str_periode set file='' where idSperiode='$par[idSperiode]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
		
	function uploadFile($idDinas){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["buktiKlaim"]["tmp_name"];
		$fileUpload_name = $_FILES["buktiKlaim"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$buktiKlaim = "bukti-".$idDinas.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $buktiKlaim);			
		}		
		
		return $buktiKlaim;
	}

	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		// $fileDinas = getField("select fileDinas from ess_dinas where idDinas='$par[idDinas]'");
		// if(file_exists($fFile.$fileDinas) and $fileDinas!="")unlink($fFile.$fileDinas);
		
		// $sql="delete from rawatjalan_klaim where id='$par[id]'";
		// db($sql);
		$sql="delete from str_periode where idSperiode='$par[idSperiode]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idSperiode")."';</script>";
	}

	function ubah(){
		global $db,$s,$inp,$par,$cUsername, $det,$cID,$fSK;
		repField();
		// $idPegawai = getField("select idPegawai from rawatjalan_klaim where id = '$par[id]'");
		// if($inp[idPegawai] != $idPegawai){
		// 	$redirect = "par[mode]=edit";
		// }else if($inp[idPegawai] == $idPegawai){
		// 	$redirect = "";
		// 	$mode = ",id";
		// }		
		$year = date("Y");
    $month = date("m");
    $day = date("d");
    
        $cFile = "$fSK/$year/$month/$day/";
  if(!is_dir("$fSK/$year/")){
        mkdir("$fSK/$year/", 0755, true);
    }
    
    if(!is_dir("$fSK/$year/$month/")){
        mkdir("$fSK/$year/$month/", 0755, true);
    }
    
    if(!is_dir("$fSK/$year/$month/$day/")){
        mkdir("$fSK/$year/$month/$day/", 0755, true);
    }
    
		$fileFile = $_FILES["file"]["tmp_name"];
		$fileFile_name = $_FILES["file"]["name"];
		if(($fileFile!="") and ($fileFile!="none")){						
			fileUpload($fileFile,$fileFile_name,$cFile);			
			$file = "fileSK-".$id.".".getExtension($fileFile_name);
		fileRename($cFile, $fileFile_name, $file);
					$file = "$year/$month/$day/".$file;
					
		}
		if(empty($file)) $file = getField("select file from app_menu where kodeMenu='$par[kodeMenu]'");

		$sql="update str_periode set periode='$inp[periode]',tanggal='".setTanggal($inp[tanggal])."',file='$file',keterangan='$inp[keterangan]',updateBy='$cID', updateDate='".date('Y-m-d H:i:s')."' where idSperiode='$par[idSperiode]'";
		// echo $sql;
		db($sql);

		// die();
		echo "<script>alert('Data Tersimpan');window.location='?$redirect".getPar($par,"mode,idSperiode")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParam,$cID,$fSK;	
		repField();				
		$id = getField("select idSperiode from str_periode order by idSperiode desc limit 1")+1;
		$year = date("Y");
    $month = date("m");
    $day = date("d");
    
        $cFile = "$fSK/$year/$month/$day/";
  if(!is_dir("$fSK/$year/")){
        mkdir("$fSK/$year/", 0755, true);
    }
    
    if(!is_dir("$fSK/$year/$month/")){
        mkdir("$fSK/$year/$month/", 0755, true);
    }
    
    if(!is_dir("$fSK/$year/$month/$day/")){
        mkdir("$fSK/$year/$month/$day/", 0755, true);
    }
    
		$fileFile = $_FILES["file"]["tmp_name"];
		$fileFile_name = $_FILES["file"]["name"];
		if(($fileFile!="") and ($fileFile!="none")){						
			fileUpload($fileFile,$fileFile_name,$cFile);			
			$file = "fileSK-".$id.".".getExtension($fileFile_name);
		fileRename($cFile, $fileFile_name, $file);
					$file = "$year/$month/$day/".$file;
					
		}

		$sql="insert into str_periode (idSperiode,periode,tanggal,file,keterangan,createdBy, createdDate) values ('$id','$inp[periode]','".setTanggal($inp[tanggal])."','$file','$inp[keterangan]','$cID', '".date('Y-m-d H:i:s')."')";
		

		db($sql);
		
		echo "<script>alert('Data Tersimpan');window.location='?$redirect".getPar($par,"mode")."';</script>";
	}
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $arrParam;
		
		$sql="select * from str_periode where idSperiode='$par[idSperiode]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);	

		// $true = $r[apprKlaim] == "t" ? "checked=\"checked\"" : "";
		// $false = $r[apprKlaim] == "f" ? "checked=\"checked\"" : "";
		// $revisi = $r[apprKlaim] == "r" ? "checked=\"checked\"" : "";
		
		// $sTrue = $r[sdmKlaim] == "t" ? "checked=\"checked\"" : "";
		// $sFalse = $r[sdmKlaim] == "f" ? "checked=\"checked\"" : "";
		// $sRevisi = $r[sdmKlaim] == "r" ? "checked=\"checked\"" : "";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		// $r[hubunganPasien] = getField("select rel from emp_family where id = '$r[idPasien]'");

		
		// if(empty($r[nomor])) $r[nomor] = gNomor();
		// if(empty($r[tanggal])) $r[tanggal] = date('Y-m-d');	

		// if(empty($r[tahun])) $r[tahun] = date('Y');		

		$text = getValidation();

		// if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;								
		
		// $sql_="select
		// 	id as idPegawai,
		// 	reg_no as nikPegawai,
		// 	name as namaPegawai, marital, tipe
		// from emp where id='".$r[idPegawai]."'";
		// $res_=db($sql_);
		// $row_=mysql_fetch_array($res_);
		
		// $sql__="select * from emp_phist where parent_id='".$row_[idPegawai]."' and status='1'";
		// // echo $sql__;
		// $res__=db($sql__);
		// $row__=mysql_fetch_array($res__);
		// $row__[namaJabatan] = $row__[pos_name];
		// $row__[namaLokasi] = getField("select namaData from mst_data where kodeData='".$row__[location]."'");

		// $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		// if($arrMaster[$row__[grade]] < 4){
		// 	$arrMaster[$row__[grade]] = 4;
		// }

		// $getNilai = getField("select nilai from pay_tunjangan_umum where idTipe = '$row_[tipe]' AND idGrade = '$row__[grade]' AND idPangkat = '$row__[rank]' and kode = 'rawat'");

		// // echo "select nilai from rawatjalan_plafon where idJenis = '$row_[marital]' AND tahun = '".date('Y')."'";
		// $getJumlahPengambilan = getField("select sum(pengambilan) from rawatjalan_klaim where idPegawai = '$row_[idPegawai]' AND year(tanggalKlaim) = '".date('Y')."'");

		// $getNilai = str_replace(",", ".", $getNilai);

		// $batasNilai = $getNilai - $getJumlahPengambilan;

		// $r[batasNilai] = $r[batasNilai] < 1 ? $batasNilai : $r[batasNilai];
		
				
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
				<fieldset>
					<legend>Data Periode</legend>
						
						<p>
							<label class=\"l-input-small\">Periode</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[periode]\" name=\"inp[periode]\"  value=\"$r[periode]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggal\" name=\"inp[tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggal])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						
						<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[file])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<img src=\"".getIcon($r[file])."\" align=\"left\"style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete file ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\"  value=\"$r[keterangan]\" class=\"mediuminput\" style=\"width:300px;\"  />
							</div>
						</p>
					
					</fieldset>
					<br clear=\"all\"/>
					
				
				</div>";
				$text.="
				<p style=\"position:absolute; top:5px;right:10px;\">					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}

function lihat() {

	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck,$arrParam;

	// $par[idPegawai] = $cID;
	// if(empty($par[tanggalSelesai])) $par[tanggalSelesai]=date('d/m/Y');		
	// if(empty($par[tanggalMulai])) $par[tanggalMulai]=date('d/m/Y', strtotime("-30 days"));	
	
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>";
	
	// require_once "tmpl/__emp_header__.php";

	$text.="<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">";
			
			if(isset($menuAccess[$s]["add"])) 
				$text.="<a href=\"?par[mode]=add".getPar($par,"mode,idSuplesi")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
			
			$text.="
			</div>
				</form>
				<br clear=\"all\" />
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
					<thead>
						<tr>
							<th  width=\"20\">No.</th>
							<th  style=\"min-width:100px;\">PERIODE</th>
							<th  width=\"100\">CREATED</th>
							<th  width=\"100\">File</th>
							<th  width=\"50\">Kontrol</th>
						</thead>
						<tbody>";

		$filter = "";

		// if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		// if(!empty($par[filter]))		
		// $filter.= " and (
		// 	lower(t2.name) like '%".strtolower($par[filter])."%'
		// 	or lower(t1.nomor) like '%".strtolower($par[filter])."%'
		// )";
		
		// $arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[19]."'");

		$res = db("SELECT * FROM `str_periode` $filter");
		
		while ($r = mysql_fetch_array($res)) {
			
			$no++;

			$text.="
			<tr>
			<td>$no.</td>
			<td>$r[periode]</td>
			<td align=\"center\">".getTanggal($r[tanggal])."</td>
			<td align=\"center\"><img src=\"".getIcon($r[file])."\" align=\"center\"style=\"height:25px;\"></td>
			";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";		
				if(in_array($r[persetujuanDinas], array(0,2)))
					if(isset($menuAccess[$s]["edit"])&&$r[persetujuanDinas]!='t') $text.="<a href=\"?par[mode]=edit&par[idSperiode]=$r[idSperiode]".getPar($par,"mode,id")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				

				if(in_array($r[persetujuanDinas], array(0)))
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idSperiode]=$r[idSperiode]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	

		$text.="</tbody>
	</table>
	</div>";

	if($par[mode] == "xls"){            
		xls();          
		$text.="<iframe src=\"download.php?d=exp&f=rawat_jalan.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	$text.="
	<script>
		jQuery(\"#btnExport\").live('click', function(e){
			e.preventDefault();
			window.location.href=\"?par[mode]=xls\"+ \"".getPar($par,"mode","tanggal")."\"+ \"&par[tahun]=\" + jQuery(\"#aSearch\").val();
		});
	</script>";

return $text;
}		

function xls()
{		
global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
$fExport = "files/export/";
$direktori = $fExport;
$namaFile = "rawat_jalan.xls";
$judul = "RAWAT JALAN";
$field = array("no",  "nama", "nik", "jabatan", "tanggal", "nomor", "nilai");

$filter = "where tanggal between '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."'";

$sql="select *,t1.id as idK from rawatjalan_klaim t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter";

$res=db($sql);

$no = 0;
while($r=mysql_fetch_array($res))
{
	$no++;      

	$data[] = array($no . "\t center", 
		$r[name] . "\t left", 
		$r[reg_no] . "\t left",
		$r[pos_name] . "\t right",
		getTanggal($r[tanggal]) . "\t center", 
		$r[nomor] . "\t center",
		getAngka($r[pengambilan]) . "\t right");
}

exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}

function getContent($par) {

	global $db,$s,$_submit,$menuAccess, $cUsername;

	switch($par[mode]){
		case "no":
			$text = gNomor();
		break;
		case "get":
			$text = gPegawai();
		break;
		case "hubungan":
			$text = hubungan();
		break;			
		case "peg":
			$text = pegawai();
		break;		
		case "delFile":
			if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
		break;
		case "del":
			if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
		break;
		case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;
		case "add":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
		break;
		case "delFile":
			if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
		break;
		default:
			$text = lihat();
		break;
	}
	return $text;
}
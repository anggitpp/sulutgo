<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/dokumen/";

function hapusFile(){
	global $db,$s,$inp,$par,$fFile,$cUsername;					
	$fileDokumen = getField("select fileDokumen from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'");
	if(file_exists($fFile.$fileDokumen) and $fileDokumen!="")unlink($fFile.$fileDokumen);

	$sql="update plt_pelatihan_dokumen set fileDokumen='' where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'";
	db($sql);		
	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
}

function hapus(){
	global $db,$s,$inp,$par,$fFile,$cUsername;					
	$fileDokumen = getField("select fileDokumen from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'");
	if(file_exists($fFile.$fileDokumen) and $fileDokumen!="")unlink($fFile.$fileDokumen);

	$sql="delete from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'";
	db($sql);		
	echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idDokumen")."';</script>";
}

function upload($idDokumen){
	global $db,$s,$inp,$par,$fFile;				
	list($fileDokumen, $ukuranDokumen) = explode("\t", getField("select concat(fileDokumen, '\t', ukuranDokumen) from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'"));
	if(!empty($_FILES["fileDokumen"]["tmp_name"]) && ($par[tipeDokumen] != "f" || in_array($_FILES["fileDokumen"]["type"],array('image/jpg','image/jpeg','image/gif','image/png')))){
		$fileUpload = $_FILES["fileDokumen"]["tmp_name"];
		$fileUpload_name = $_FILES["fileDokumen"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){			
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileDokumen = "dokumen-".$par[idPelatihan].".".$idDokumen.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileDokumen);
			$ukuranDokumen = $_FILES["fileDokumen"]["size"];		
		}				
	}
	return $fileDokumen."\t".$ukuranDokumen;
}

function ubah(){
	global $db,$s,$inp,$par,$cUsername;	
	repField();				
	if(empty($_FILES["fileDokumen"]["tmp_name"]) || $par[tipeDokumen] != "f" || in_array($_FILES["fileDokumen"]["type"],array('image/jpg','image/jpeg','image/gif','image/png'))){
		list($fileDokumen, $ukuranDokumen) = explode("\t",upload($par[idDokumen]));
		$sql="update plt_pelatihan_dokumen set judulDokumen='$inp[judulDokumen]', keteranganDokumen='$inp[keteranganDokumen]', fileDokumen='$fileDokumen', ukuranDokumen='$ukuranDokumen', statusDokumen='$inp[statusDokumen]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'";
		db($sql);

		echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par,"mode,idDokumen,tipeDokumen")."';</script>";
	}else{
		echo form()."<script>
		alert('maaf, file harus dalam format image : jpg/jpeg/gif/png');
		window.location='?".getPar($par)."';
	</script>";
}
}

function tambah(){
	global $db,$s,$inp,$par,$cUsername;	
	if(empty($_FILES["fileDokumen"]["tmp_name"]) || $par[tipeDokumen] != "f" || in_array($_FILES["fileDokumen"]["type"],array('image/jpg','image/jpeg','image/gif','image/png'))){
		repField();
		$idDokumen = getField("select idDokumen from plt_pelatihan_dokumen where idPelatihan='".$par[idPelatihan]."' order by idDokumen desc limit 1")+1;
		list($fileDokumen, $ukuranDokumen) = explode("\t",upload($idDokumen));		

		$sql="insert into plt_pelatihan_dokumen (idPelatihan, idDokumen, tipeDokumen, judulDokumen, keteranganDokumen, fileDokumen, ukuranDokumen, statusDokumen, createBy, createTime) values ('$par[idPelatihan]', '$idDokumen', '$par[tipeDokumen]', '$inp[judulDokumen]', '$inp[keteranganDokumen]', '$fileDokumen', '$ukuranDokumen', '$inp[statusDokumen]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);

		echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par,"mode,idDokumen,tipeDokumen")."';</script>";
	}else{
		echo form()."<script>
		alert('maaf, file harus dalam format image : jpg/jpeg/gif/png');
		window.location='?".getPar($par)."';
	</script>";
}
}

function lihat(){
	global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;
	if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."
</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<div style=\"padding-bottom:10px;\">
	</div>
	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<input type=\"hidden\" name=\"_submit\" value=\"t\">
		<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."</div>
		<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
					<td>Search : </td>								
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
					<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
		</div>				
	</form>
	<br clear=\"all\" />
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>					
				<th>Pelatihan</th>					
				<th style=\"width:75px;\">Mulai</th>
				<th style=\"width:75px;\">Selesai</th>
				<th>Lokasi</th>
				<th>Vendor</th>
				<th>PIC</th>
			</tr>
		</thead>
		<tbody>";

			$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
			if(!empty($par[tahunPelatihan]))
				$filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";

			if(!empty($par[idKategori]))
				$filter.=" and t1.idKategori='".$par[idKategori]."'";

			if(!empty($par[filter]))		
				$filter.= " and (
			lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.namaPic) like '%".strtolower($par[filter])."%'
			or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
			)";

			$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from (
			select p1.*, case when p1.pelaksanaanPelatihan='e' then p2.namaTrainer else p1.namaPegawai end as namaPic from (
			select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
			) as p1 left join dta_trainer p2 on (p1.idTrainer=p2.idTrainer)
			) as t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){					
				$no++;			
				$cntPeserta = getField("select count(idPeserta) from plt_pelatihan_peserta where idPelatihan='$r[idPelatihan]'");			

				$text.="<tr>
				<td>$no.</td>			
				<td><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>
				<td align=\"center\">".getTanggal($r[mulaiPelatihan])."</td>
				<td align=\"center\">".getTanggal($r[selesaiPelatihan])."</td>
				<td>$r[lokasiPelatihan]</td>
				<td>$r[namaVendor]</td>
				<td>$r[namaPic]</td>
			</tr>";			
		}

		$text.="</tbody>
	</table>
</div>";

return $text;
}

function form(){
	global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and idDokumen='$par[idDokumen]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);											

	$false =  $r[statusDokumen] == "f" ? "checked=\"checked\"" : "";		
	$true =  empty($false) ? "checked=\"checked\"" : "";	

	setValidation("is_null","inp[judulDokumen]","anda harus mengisi judul");		
	if(empty($r[fileDokumen]))
		setValidation("is_null","fileDokumen","anda harus mengisi file");
	$text = getValidation();
	$title = $par[tipeDokumen] == "f" ? "Foto" : "Dokumen";
	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$title."</h1>
		".getBread(ucwords($par[mode]." ".strtolower($title).""))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Judul</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[judulDokumen]\" name=\"inp[judulDokumen]\" value=\"$r[judulDokumen]\" class=\"mediuminput\" style=\"width:425px;\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";								
						$text.=empty($r[fileDokumen])?
						"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:265px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:325px;\">
							<input type=\"file\" id=\"fileDokumen\" name=\"fileDokumen\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
						</div>":
						"<a href=\"download.php?d=dokumen&f=$par[idPelatihan].$par[idDokumen]\"><img src=\"".getIcon($fFile."/".$r[fileDokumen])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						<input type=\"file\" id=\"fileDokumen\" name=\"fileDokumen\" style=\"display:none;\" />
						<a href=\"?par[mode]=delFile&inp[mode]=set".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganDokumen]\" name=\"inp[keteranganDokumen]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:425px;\">$r[keteranganDokumen]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusDokumen]\" value=\"t\" $true /> <span class=\"sradio\">Tampil</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusDokumen]\" value=\"f\" $false /> <span class=\"sradio\">Tidak</span>
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
		</div>";		
		return $text;
	}	
	
	function detail(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
		$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		$pelaksanaanPelatihan =  $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";		
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."								
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  >	
			<div style=\"top:10px; right:35px; position:absolute\">				
				<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/>
			</div>
			<div id=\"general\">					
				".dtaPelatihan()."					
				<fieldset style=\"padding:10px; border-radius: 10px; margin-top:20px;\">
					<legend style=\"padding:10px; margin-left:20px;\"><h4>FOTO</h4></legend>
					<ul class=\"listfile\">";
						if(isset($menuAccess[$s]["add"]))
							$text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add&par[tipeDokumen]=f".getPar($par,"mode,idDokumen,tipeDokumen")."',725,450);\" style=\"float:right; margin-top:-15px; margin-bottom:10px;  margin-right:10px;\"><span>Tambah Data</span></a>";
						$sql="select * from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and tipeDokumen='f' order by idDokumen";
						$res=db($sql);
						$no=1;
						while($r=mysql_fetch_array($res)){
							$text.="<li>
							<a class=\"image\" href=\"#\">
								<span class=\"img\"><img src=\"".$fFile.$r[fileDokumen]."\" alt=\"$r[judulDokumen]\" width=\"125\" height=\"100\" onclick=\"openBox('view.php?doc=dokumen&id=$r[idDokumen]".getPar($par,"mode,idDokumen")."',1000,550);\"></span>
								<span class=\"filename\">";						
									if(isset($menuAccess[$s]["edit"])) $text.="<span onclick=\"openBox('popup.php?par[mode]=edit&par[idDokumen]=$r[idDokumen]".getPar($par,"mode,idDokumen,tipeDokumen")."',725,450);\" title=\"Edit Data\" style=\"margin-right:5px;\"><img src=\"styles/images/icons/edit.png\"></span>";				

									if(isset($menuAccess[$s]["delete"])) $text.="<span onclick=\"if(confirm('are you sure to delete data ?')) window.location='?par[mode]=del&par[idDokumen]=$r[idDokumen]".getPar($par,"mode,idDokumen,tipeDokumen")."';\" title=\"Delete Data\" style=\"margin-right:5px;\"><img src=\"styles/images/icons/delete.png\"></span>";
									$text.="$r[judulDokumen]</span>
								</a>
							</li>";
							$no++;
						}

						$text.="</ul>
					</fieldset>

					<fieldset style=\"padding:10px; border-radius: 10px; margin-top:30px;\">
						<legend style=\"padding:10px; margin-left:20px;\"><h4>DOKUMEN</h4></legend>";
						if(isset($menuAccess[$s]["add"]))
							$text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add&par[tipeDokumen]=d".getPar($par,"mode,idDokumen,tipeDokumen")."',725,450);\" style=\"float:right; margin-top:-15px; margin-bottom:10px;  margin-right:10px;\"><span>Tambah Data</span></a>";
						$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Dokumen</th>
								<th width=\"50\">View</th>";
								if(isset($menuAccess[$s]['apprlv1']))
									$text .= "<th width=\"50\">Download</th>";
								$text .="<th width=\"100\">Besar</th>";
								if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
								$text.="</tr>
							</thead>
							<tbody>";

								$sql="select * from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and tipeDokumen='d' order by idDokumen";
								$res=db($sql);
								$no=1;
								while($r=mysql_fetch_array($res)){								
									$text.="<tr>
									<td>$no.</td>
									<td>$r[judulDokumen]</td>
									<td align=\"center\"><a href=\"#View\" class=\"detail\" title=\"View Dokumen\" onclick=\"openBox('view.php?doc=dokumen&id=$r[idDokumen]".getPar($par,"mode,idDokumen")."',1000,550);\"><span>Detail Data</span></a></td>";
									if(isset($menuAccess[$s]['apprlv1']))
										$text .= "<td align=\"center\"><a href=\"download.php?d=dokumen&f=$par[idPelatihan].$r[idDokumen]\" title=\"Download Dokumen\" ><img src=\"".getIcon($r[fileDokumen])."\"></a></td>";
									$text .= "<td align=\"right\">".getAngka($r[ukuranDokumen] / 1024)." KB</td>";
									if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
										$text.="<td align=\"center\">";						
										if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idDokumen]=$r[idDokumen]".getPar($par,"mode,idDokumen,tipeDokumen")."',725,450);\"><span>Edit</span></a>";				

										if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idDokumen]=$r[idDokumen]".getPar($par,"mode,idDokumen,tipeDokumen")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
										$text.="</td>";
									}
									$text.="</tr>";			
									$no++;
								}

								if($no == 1){
									$text.="
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>";
										if(isset($menuAccess[$s]['apprlv1']))
											$text .= "<td>&nbsp;</td>";
										$text .= "<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>";
								}
								$text .= "
							</tbody>
						</table>
					</fieldset>
				</div>				
			</form>";			

			return $text;
		}

		function detail2(){
			global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;
			$sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
			$res = db($sql);
			$r = mysql_fetch_array($res);
			if (empty($r[idPelatihan])) {
				$r[idPelatihan] = getField('select idPelatihan from plt_pelatihan order by idPelatihan desc limit 1') + 1;
			}
			if (empty($r[idKategori])) {
				$r[idKategori] = $par[idKategori];
			}
			if (!is_array($detail)) {
				$detail = arrayQuery("select idDetail,concat(keteranganDetail, '\t', DATE_FORMAT(date(mulaiDetail),'%d/%m/%Y'), '\t', substring(time(mulaiDetail),1,5), '\t', DATE_FORMAT(date(selesaiDetail),'%d/%m/%Y'), '\t', substring(time(selesaiDetail),1,5)) from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]'");
			}

			$kodeModul = getField("select kodeModul from app_modul where folderModul = 'katalog'");

			$r[idPegawai] = empty($inp[idPegawai]) ? $r[idPegawai] : $inp[idPegawai];
			$r[idVendor] = empty($inp[idVendor]) ? $r[idVendor] : $inp[idVendor];
			$r[idKategori] = empty($inp[idKategori]) ? $r[idKategori] : $inp[idKategori];
			$r[idDepartemen] = empty($inp[idDepartemen]) ? $r[idDepartemen] : $inp[idDepartemen];
			$r[kodePelatihan] = empty($inp[kodePelatihan]) ? $r[kodePelatihan] : $inp[kodePelatihan];
			$r[judulPelatihan] = empty($inp[judulPelatihan]) ? $r[judulPelatihan] : $inp[judulPelatihan];
			$r[subPelatihan] = empty($inp[subPelatihan]) ? $r[subPelatihan] : $inp[subPelatihan];
			$r[pesertaPelatihan] = empty($inp[pesertaPelatihan]) ? $r[pesertaPelatihan] : setAngka($inp[pesertaPelatihan]);
			$r[pelaksanaanPelatihan] = empty($inp[pelaksanaanPelatihan]) ? $r[pelaksanaanPelatihan] : $inp[pelaksanaanPelatihan];
			$r[lokasiPelatihan] = empty($inp[lokasiPelatihan]) ? $r[lokasiPelatihan] : $inp[lokasiPelatihan];
			$r[biayaPelatihan] = empty($inp[biayaPelatihan]) ? $r[biayaPelatihan] : setAngka($inp[biayaPelatihan]);
			$r[filePelatihan] = empty($fileTemp) ? $r[filePelatihan] : upload($r[idPelatihan]);

			$eksternal = $r[pelaksanaanPelatihan] == 'e' ? 'checked="checked"' : '';
			$internal = empty($eksternal) ? 'checked="checked"' : '';
			$cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

			$text .="
			<style>
        #inp_kodeRekening__chosen{
				min-width:250px;
			}
		</style>
		<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread(ucwords("import data"))."
				<span class=\"pagedesc\">&nbsp;</span> 
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
					<div style=\"position:absolute; right:20px; top:14px;\">
						<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
					</div>
					<!--<fieldset>
					<legend>KATALOG PROGRAM</legend>
					<p>
						<label class=\"l-input-small\">Modul</label>
						<span class=\"field\">
							&nbsp;".getField("SELECT keterangan from app_site WHERE kodeSite = '$r[modul_pelatihan]'")."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Kategori Level</label>
						<span class=\"field\">
							&nbsp;".getField("SELECT namaMenu FROM app_menu WHERE kodeMenu = '$r[kategori_level_pelatihan]'")."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Program</label>
						<span class=\"field\">
							&nbsp;".getField("SELECT program FROM ctg_program WHERE id_program = '$r[program_pelatihan]'")."
						</span>
					</p>
				</fieldset>
				<br>-->
				<fieldset>
					<legend>PELATIHAN</legend>
					<p>
						<label class=\"l-input-small\">Judul Pelatihan</label>
						<span class=\"field\">
							&nbsp;".$r[judulPelatihan]."
						</span>
					</p>
					<table style='width:100%;'>
						<tr>
							<td style='width:50%;'>
								<p>
									<label class=\"l-input-small2\">Tanggal Mulai</label>
									<span class=\"field\">
										&nbsp;".getTanggal($r[mulaiPelatihan])."
									</span>
								</p>
							</td>
							<td style='width:50%;'>
								<p>
									<label class=\"l-input-small2\">Tanggal Selesai</label>
									<span class=\"field\">
										&nbsp;".getTanggal($r[selesaiPelatihan])."
									</span>
								</p>
							</td>
						</tr>
					</table>
					<table style='width:100%;'>
						<tr>
							<td style='width:50%;'>
								<p>
									<label class=\"l-input-small2\">Sub</label>
									<span class=\"field\">
										&nbsp;".$r[subPelatihan]."
									</span>
								</p>
							</td>
							<td style='width:50%;'>
								<p>
									<label class=\"l-input-small2\">Kode</label>
									<span class=\"field\">
										&nbsp;".$r[kodePelatihan]."
									</span>
								</p>
							</td>
						</tr>
					</table>
					<p>
                        <label class=\"l-input-small\">Kategori</label>
                        <span class=\"field\">
                        &nbsp;" . namaData($r[idTraining]) . "
                        </span>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Training</label>
                        <span class=\"field\">
                        &nbsp;" . namaData($r[idKategori]) . "
                        </span>
                    </p>
                    <p>
                        <label class=\"l-input-small\">Level</label>
                        <span class=\"field\">
                        &nbsp;" . namaData($r[idDepartemen]) . "
                        </span>
                    </p>
					<p>
						<label class=\"l-input-small\">Jumlah Peserta</label>
						<span class=\"field\">
							&nbsp;".getAngka($r[pesertaPelatihan])."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Pelaksanaan</label>
						<span class=\"field\">
							&nbsp;".($r[pelaksanaanPelatihan]=='e'?'Eksternal' : 'Internal')."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Vendor</label>
						<span class=\"field\">
							&nbsp;".getField("SELECT namaVendor from dta_vendor where kodeVendor = '$r[idVendor]'")."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Koordinator</label>
						<span class=\"field\">
							&nbsp;".getField("SELECT upper(namaTrainer) as namaTrainer from dta_trainer where idTrainer = '$r[idTrainer]'")."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Lokasi</label>
						<span class=\"field\">
							&nbsp;".$r[lokasiPelatihan]."
						</span>
					</p>
				</fieldset>
			</form>
		</div>
	</div>";
	return $text;
}

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){	
		case "detail2":
		$text = detail2();
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
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
		break;
		case "det":
		$text = detail();
		break;
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>
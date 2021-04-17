<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/materi/";
	
	function kota(){
		global $db,$s,$id,$inp,$par,$arrParameter;				
		$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[idPropinsi]' and kodeCategory='".$arrParameter[4]."' order by namaData");		
		return implode("\n", $data);
	}
	
	function upload($idMateri){
		global $db,$s,$inp,$par,$fFile;		
		$fileMateri = getField("select fileMateri from plt_materi where idMateri='$par[idMateri]'");
		
		$fileUpload = $_FILES["fileMateri"]["tmp_name"];
		$fileUpload_name = $_FILES["fileMateri"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileMateri = "materi-".$idMateri.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileMateri);			
		}		
		
		return $fileMateri;
	}
	
	function uploadDetail($idDetail){
		global $db,$s,$inp,$par,$fFile;		
		$fileDetail = getField("select fileDetail from plt_materi_detail where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'");
		
		$fileUpload = $_FILES["fileDetail"]["tmp_name"];
		$fileUpload_name = $_FILES["fileDetail"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileDetail = "detail-".$par[idMateri].".".$idDetail.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileDetail);			
		}		
		
		return $fileDetail;
	}
	
	function hapusFile_detail(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileDetail = getField("select fileDetail from plt_materi_detail where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'");
		if(file_exists($fFile.$fileDetail) and $fileDetail!="")unlink($fFile.$fileDetail);
		
		$sql="update plt_materi_detail set fileDetail='' where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=editDet".getPar($par,"mode")."';</script>";
	}
	
	function hapusDetail(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileDetail = getField("select fileDetail from plt_materi_detail where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'");
		if(file_exists($fFile.$fileDetail) and $fileDetail!="")unlink($fFile.$fileDetail);
		
		$sql="delete from plt_materi_detail where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'";
		db($sql);
		echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par,"mode,idDetail")."';</script>";
	}
	
	function ubahDetail(){
		global $db,$s,$inp,$par,$cUsername;
		repField();			
		$fileDetail=uploadDetail($par[idDetail]);
		
		$sql="update plt_materi_detail set kodeDetail='$inp[kodeDetail]', judulDetail='$inp[judulDetail]', subDetail='$inp[subDetail]', durasiDetail='".setAngka($inp[durasiDetail])."', keteranganDetail='$inp[keteranganDetail]', pengajaranDetail='$inp[pengajaranDetail]', ringkasanDetail='$inp[ringkasanDetail]', standarDetail='$inp[standarDetail]', dasarDetail='$inp[dasarDetail]', fileDetail='$fileDetail', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'";
		db($sql);
		
		echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par,"mode,idDetail")."';</script>";
	}
	
	function tambahDetail(){
		global $db,$s,$inp,$par,$cUsername;	
		repField();
		$idDetail = getField("select idDetail from plt_materi_detail where idMateri='$par[idMateri]' order by idDetail desc limit 1")+1;
		$fileDetail=uploadDetail($idDetail);
		
		$sql="insert into plt_materi_detail (idMateri, idDetail, kodeDetail, judulDetail, subDetail, durasiDetail, keteranganDetail, pengajaranDetail, ringkasanDetail, standarDetail, dasarDetail, fileDetail, createBy, createTime) values ('$par[idMateri]', '$idDetail', '$inp[kodeDetail]', '$inp[judulDetail]', '$inp[subDetail]', '".setAngka($inp[durasiDetail])."', '$inp[keteranganDetail]', '$inp[pengajaranDetail]', '$inp[ringkasanDetail]', '$inp[standarDetail]', '$inp[dasarDetail]', '$fileDetail', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par,"mode,idDetail")."';</script>";
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileMateri = getField("select fileMateri from plt_materi where idMateri='$par[idMateri]'");
		if(file_exists($fFile.$fileMateri) and $fileMateri!="")unlink($fFile.$fileMateri);
		
		$sql="update plt_materi set fileMateri='' where idMateri='$par[idMateri]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileMateri = getField("select fileMateri from plt_materi where idMateri='$par[idMateri]'");
		if(file_exists($fFile.$fileMateri) and $fileMateri!="")unlink($fFile.$fileMateri);
		
		$sql="delete from plt_materi where idMateri='$par[idMateri]'";
		db($sql);
		$sql="delete from plt_materi_detail where idMateri='$par[idMateri]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idMateri")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField(array("keahlianMateri", "kerjaMateri", "pendidikanMateri"));			
		$fileMateri=upload($par[idMateri]);
		
		$sql="update plt_materi set idKategori='$inp[idKategori]', idTingkat='$inp[idTingkat]', id_modul_materi='$inp[id_modul_materi]',id_kategori_materi='$inp[id_kategori_materi]',id_program_materi='$inp[id_program_materi]',judulMateri='$inp[judulMateri]', subMateri='$inp[subMateri]', metodeMateri='$inp[metodeMateri]', durasiMateri='$inp[durasiMateri]', pertemuanMateri='".setAngka($inp[pertemuanMateri])."', targetMateri='".setAngka($inp[targetMateri])."', menitMateri='".setAngka($inp[menitMateri])."', resumeMateri='$inp[resumeMateri]', detailMateri='$inp[detailMateri]', manfaatMateri='$inp[manfaatMateri]', keteranganMateri='$inp[keteranganMateri]', pesertaMateri='$inp[pesertaMateri]', persyaratanMateri='$inp[persyaratanMateri]', fileMateri='$fileMateri', statusMateri='$inp[statusMateri]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idMateri='$par[idMateri]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idMateri")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;	
		repField(array("keahlianMateri", "kerjaMateri", "pendidikanMateri"));
		$idMateri = getField("select idMateri from plt_materi order by idMateri desc limit 1")+1;
		$fileMateri=upload($idMateri);
		
		$sql="insert into plt_materi (idMateri, idKategori, idTingkat, id_modul_materi,id_kategori_materi,id_program_materi,judulMateri, subMateri, metodeMateri, durasiMateri, pertemuanMateri, targetMateri, menitMateri, resumeMateri, detailMateri, manfaatMateri, keteranganMateri, pesertaMateri, persyaratanMateri, fileMateri, statusMateri, createBy, createTime) values ('$idMateri', '$inp[idKategori]', '$inp[idTingkat]','$inp[id_modul_materi]', '$inp[id_kategori_materi]','$inp[id_program_materi]','$inp[judulMateri]', '$inp[subMateri]', '$inp[metodeMateri]', '$inp[durasiMateri]', '".setAngka($inp[pertemuanMateri])."', '".setAngka($inp[targetMateri])."', '".setAngka($inp[menitMateri])."', '$inp[resumeMateri]', '$inp[detailMateri]', '$inp[manfaatMateri]', '$inp[keteranganMateri]', '$inp[pesertaMateri]', '$inp[persyaratanMateri]', '$fileMateri', '$inp[statusMateri]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idMateri")."';</script>";
	}
	
	function formDetail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from plt_materi_detail where idMateri='$par[idMateri]' and idDetail='$par[idDetail]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);											
				
		setValidation("is_null","inp[judulDetail]","anda harus mengisi judul");				
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Input Materi</h1>
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">	
					<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
					<tr>
					<td width=\"50%\">
						<p>
							<label class=\"l-input-small\">Judul</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[judulDetail]\" name=\"inp[judulDetail]\"  value=\"$r[judulDetail]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
							</div>	
						</p>
					</td>
					<td width=\"50%\">
						&nbsp;
					</td>
					</tr>					
					<tr>
					<td>
						<p>
							<label class=\"l-input-small\">Sub</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[subDetail]\" name=\"inp[subDetail]\"  value=\"$r[subDetail]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
							</div>	
						</p>
					</td>
					<td>
						<p>
							<label class=\"l-input-small\">Kode</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[kodeDetail]\" name=\"inp[kodeDetail]\"  value=\"$r[kodeDetail]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
							</div>	
						</p>
					</td>
					</tr>
					<tr>
					<td>
						<p>
							<label class=\"l-input-small\">Durasi</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[durasiDetail]\" name=\"inp[durasiDetail]\"  value=\"$r[durasiDetail]\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\"/> Menit
							</div>	
						</p>
						<p>
							<label class=\"l-input-small\">Uraian</label>
							<div class=\"field\">								
								<textarea id=\"inp[keteranganDetail]\" name=\"inp[keteranganDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganDetail]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">File</label>
							<div class=\"field\">";								
								$text.=empty($r[fileDetail])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:265px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:325px;\">
										<input type=\"file\" id=\"fileDetail\" name=\"fileDetail\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=detmat&f=$par[idMateri].$par[idDetail]\"><img src=\"".getIcon($fFile."/".$r[fileDetail])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileDetail\" name=\"fileDetail\" style=\"display:none;\" />
									<a href=\"?par[mode]=detFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
					</td>
					<td>
						&nbsp;
					</td>
					</tr>
					<tr>
					<td>						
						<p>
							<label class=\"l-input-small\">Target Pengajaran</label>
							<div class=\"field\">								
								<textarea id=\"inp[pengajaranDetail]\" name=\"inp[pengajaranDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[pengajaranDetail]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Ringkasan</label>
							<div class=\"field\">								
								<textarea id=\"inp[ringkasanDetail]\" name=\"inp[ringkasanDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[ringkasanDetail]</textarea>
							</div>
						</p>
					</td>
					<td>
						<p>
							<label class=\"l-input-small\">Standar Kompetensi</label>
							<div class=\"field\">								
								<textarea id=\"inp[standarDetail]\" name=\"inp[standarDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[standarDetail]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Kompetensi Dasar</label>
							<div class=\"field\">								
								<textarea id=\"inp[dasarDetail]\" name=\"inp[dasarDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[dasarDetail]</textarea>
							</div>
						</p>
					</td>
					</tr>
					</table>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";		
		return $text;
	}	
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$fFile,$menuAccess;
		include "plugins/mces.jsp";
		
		$sql="select * from plt_materi where idMateri='$par[idMateri]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		if(empty($r[idKategori])) $r[idKategori] = $par[idKategori];
		        $kodeModul = getField("select kodeModul from app_modul where folderModul = 'katalog'");
 if(!empty($r[id_kategori_materi])){
            $kodeMenu = "$r[id_kategori_materi]";
        }
		$false =  $r[statusMateri] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[judulMateri]","anda harus mengisi judul pelatihan");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
				<fieldset style=\"padding:10px; border-radius: 10px;\">
               <legend style=\"padding:10px; margin-left:20px;\"><h4>KATALOG PROGRAM</h4></legend>
                <p>
                    <label class=\"l-input-small\">Modul</label>
                    <div class=\"field\">
                        ".comboData("SELECT kodeSite, keterangan from app_site WHERE kodeModul ='$kodeModul' ORDER BY kodeSite ASC","kodeSite","keterangan","inp[id_modul_materi]","Pilih Modul","$r[id_modul_materi]","onchange=\"getKodeSite('" . getPar($par, "mode") . "');\"","520px","chosen-select","")."
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Kategori Level</label>
                    <div class=\"field\">
                        ".comboData("SELECT kodeMenu, namaMenu FROM app_menu WHERE kodeData!='PKTG'  AND kodeSite ='$r[id_modul_materi]' AND kodeInduk='0' ORDER BY urutanMenu", "kodeMenu", "namaMenu","inp[id_kategori_materi]","Pilih Kategori Level","$r[id_kategori_materi]","onchange=\"getKodeMenu('" . getPar($par, "mode") . "');\"","520px","chosen-select","")."
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Program</label>
                    <span class=\"field\">
                        ".comboData("SELECT id_program, program FROM ctg_program WHERE id_kategori = '$kodeMenu' ORDER BY id_program", "id_program", "program","inp[id_program_materi]","Pilih Program","$r[id_program_materi]","onchange=\"getIdProgram('" . getPar($par, "mode") . "');\"","520px","chosen-select","")."
                    </span>
                </p>
    </fieldset>	</br>
      <fieldset  style=\"padding:10px; border-radius: 10px;\">
               <legend style=\"padding:10px; margin-left:20px;\"><h4>PELATIHAN</h4></legend>
					<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
				
					<tr>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Judul Pelatihan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[judulMateri]\" name=\"inp[judulMateri]\"  value=\"$r[judulMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Sub</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[subMateri]\" name=\"inp[subMateri]\"  value=\"$r[subMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<div class=\"field\">								
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[45]."' order by namaData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "360px","chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Metode</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[metodeMateri]\" name=\"inp[metodeMateri]\"  value=\"$r[metodeMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Durasi</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[durasiMateri]\" name=\"inp[durasiMateri]\"  value=\"$r[durasiMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"50\" />
							</div>
						</p>
					</td>
					<td width=\"45%\">
						&nbsp;
					</td>
					</tr>
					</table>	
					<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
					<tr>
					<td width=\"55%\">						
						<p>
							<label class=\"l-input-small\">Pertemuan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[pertemuanMateri]\" name=\"inp[pertemuanMateri]\"  value=\"".getAngka($r[pertemuanMateri])."\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Target Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[targetMateri]\" name=\"inp[targetMateri]\"  value=\"".getAngka($r[targetMateri])."\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
					</td>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Total Menit</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[menitMateri]\" name=\"inp[menitMateri]\"  value=\"".getAngka($r[menitMateri])."\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tingkat</label>
							<div class=\"field\">								
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[46]."' order by namaData","kodeData","namaData","inp[idTingkat]"," ",$r[idTingkat],"", "260px","chosen-select")."
							</div>
						</p>
					</td>
					</tr>
					</table>					
					<br clear=\"all\">
					<ul class=\"hornav\">
					<li class=\"current\"><a href=\"#resume\">Resume</a></li>
					<li><a href=\"#detail\">Detail</a></li>
					<li><a href=\"#manfaat\">Manfaat</a></li>
					</ul>
					<div id=\"resume\" class=\"subcontent\" >
						<textarea id=\"mce1\" name=\"inp[resumeMateri]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[resumeMateri]</textarea>
					</div>
					<div id=\"detail\" class=\"subcontent\" style=\"display:none\" >
						<textarea id=\"mce2\" name=\"inp[detailMateri]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[detailMateri]</textarea>
					</div>
					<div id=\"manfaat\" class=\"subcontent\" style=\"display:none\" >
						<textarea id=\"mce3\" name=\"inp[manfaatMateri]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[manfaatMateri]</textarea>
					</div>
					<br clear=\"all\">
					<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
					<tr>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Materi</label>
							<div class=\"field\">";								
								$text.=empty($r[fileMateri])?
									"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:315px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:375px;\">
										<input type=\"file\" id=\"fileMateri\" name=\"fileMateri\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
									</div>":
									"<a href=\"download.php?d=materi&f=$par[idMateri]\"><img src=\"".getIcon($fFile."/".$r[fileMateri])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"fileMateri\" name=\"fileMateri\" style=\"display:none;\" />
									<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
							$text.="</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">								
								<textarea id=\"inp[keteranganMateri]\" name=\"inp[keteranganMateri]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganMateri]</textarea>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Target Peserta</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[pesertaMateri]\" name=\"inp[pesertaMateri]\"  value=\"$r[pesertaMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Persyaratan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[persyaratanMateri]\" name=\"inp[persyaratanMateri]\"  value=\"$r[persyaratanMateri]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[statusMateri]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
								<input type=\"radio\" id=\"false\" name=\"inp[statusMateri]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
							</div>
						</p>
					</td>
					<td width=\"45%\">
						&nbsp;
					</tr>
					</table>
					</fieldset>
				</div>
				<p style=\"position:absolute;top:10px;right:5px\">					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;						
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">
			</div>
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>								
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[45]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idMateri")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Pelatihan</th>
					<th style=\"width:75px;\">Pertemuan</th>					
					<th style=\"width:50px;\">Materi</th>
					<th style=\"width:50px;\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[idKategori]))
			$filter.=" and idKategori='".$par[idKategori]."'";
			
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(judulMateri) like '%".strtolower($par[filter])."%'		
		)";
		
		$arrDetail = arrayQuery("select idMateri, count(*) from plt_materi_detail group by 1");
		
		$sql="select * from plt_materi where idMateri is not null $filter order by idMateri";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusMateri = $r[statusMateri] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";
			
			$detailMateri = $arrDetail["$r[idMateri]"] > 0 ? $arrDetail["$r[idMateri]"] : "<img src=\"styles/images/f.png\" title='Detail Materi'>";
			
			$text.="<tr>
					<td>$no.</td>			
					<td>$r[judulMateri]</td>
					<td align=\"right\">".getAngka($r[pertemuanMateri])."</td>
					<td align=\"center\"><a href=\"?par[mode]=det&par[idMateri]=$r[idMateri]".getPar($par,"mode,idMateri")."\">$detailMateri</a></td>
					<td align=\"center\">$statusMateri</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";						
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idMateri]=$r[idMateri]".getPar($par,"mode,idMateri")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
							
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idMateri]=$r[idMateri]".getPar($par,"mode,idMateri")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";			
		return $text;
	}		
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile;
		
		$sql="select * from plt_materi where idMateri='$par[idMateri]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">
			</div>
			<form action=\"\" method=\"post\" class=\"stdform\">
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
			<legend style=\"padding:10px; margin-left:20px;\"><h4>PELATIHAN</h4></legend>						
			<table width=\"100%\">
				<tr>
				<td width=\"50%\">
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Judul Pelatihan</label>
						<span class=\"field\">$r[judulMateri]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Sub</label>
						<span class=\"field\">$r[subMateri]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Kategori</label>
						<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[idKategori]."'")."&nbsp;</span>
					</p>
				</td>
				<td width=\"50%\">
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Metode</label>
						<span class=\"field\">$r[metodeMateri]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Durasi</label>
						<span class=\"field\">$r[durasiMateri]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Pertemuan</label>
						<span class=\"field\">".getAngka($r[pertemuanMateri])."&nbsp;</span>
					</p>
				</td>
				</tr>
			</table>						
			</fieldset>	
			<br clear=\"all\">
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>MATERI</h3></div>
			</div>
			<div style=\"position:absolute; right:0; top:0; margin-top:265px; margin-right:20px;\">
			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idMateri")."';\"/> ";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=addDet".getPar($par,"mode, idDetail")."',1000,575);\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Judul</th>
					<th style=\"width:75px;\">Durasi</th>					
					<th style=\"width:50px;\">View</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$sql="select * from plt_materi_detail where idMateri='$par[idMateri]' order by idDetail";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;	
			$view = $view = empty($r[fileDetail]) ? "" : "<a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('view.php?doc=fileMateri&id=$r[idDetail]',1000,500)\"><span class=\"detail\">Detail</span></a>";		
			$text.="<tr>
					<td>$no.</td>			
					<td>$r[judulDetail]</td>
					<td align=\"right\">".getAngka($r[durasiDetail])." Menit</td>
					<td align=\"center\">$view</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";						
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editDet&par[idDetail]=$r[idDetail]".getPar($par,"mode,idDetail")."',1000,575);\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
							
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delDet&par[idDetail]=$r[idDetail]".getPar($par,"mode,idDetail")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			
			if($par[mode] == "print") pdf();
		return $text;
	}	
	
function id() {
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(kodeMenu, '\t', namaMenu) from app_menu WHERE kodeData!='PKTG' AND kodeSite='$par[modul]' AND kodeInduk='0' ORDER BY urutanMenu");
  return implode("\n", $data);
}

function id2() {
  global $s, $id, $inp, $par, $arrParameter;
  $data = arrayQuery("select concat(id_program, '\t', program) from ctg_program WHERE id_kategori='$par[kategori_level]' ORDER BY id_program");
  return implode("\n", $data);
}

	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "kta":
				$text = kota();
			break;	
			
			case "det":
				$text = detail();
			break;
			
			case "id":
            $text = id();
            break;

            case "id2":
            $text = id2();
            break;

			
			case "detFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile_detail(); else $text = lihat();
			break;
			case "delDet":
				if(isset($menuAccess[$s]["delete"])) $text = hapusDetail(); else $text = lihat();
			break;
			case "editDet":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formDetail() : ubahDetail(); else $text = lihat();
			break;
			case "addDet":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDetail() : tambahDetail(); else $text = lihat();
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
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/ticket/";
	
	function upload($idTiket){
		global $s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["fileTiket"]["tmp_name"];
		$fileUpload_name = $_FILES["fileTiket"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){						
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileTiket = "ticket-".$idTiket.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileTiket);			
		}
		if(empty($fileTiket)) $fileTiket = getField("select fileTiket from sup_tiket where idTiket='$idTiket'");
		
		return $fileTiket;
	}
	
	function hapusFile(){
		global $s,$inp,$par,$fFile,$cUsername;
		$fileTiket = getField("select fileTiket from sup_tiket where idTiket='$par[idTiket]'");
		if(file_exists($fFile.$fileTiket) and $fileTiket!="")unlink($fFile.$fileTiket);
		
		$sql="update sup_tiket set fileTiket='' where idTiket='$par[idTiket]'";
		db($sql);
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$fFile,$cUsername;
		$fileTiket = getField("select fileTiket from sup_tiket where idTiket='$par[idTiket]'");
		if(file_exists($fFile.$fileTiket) and $fileTiket!="")unlink($fFile.$fileTiket);
		
		$sql="delete from sup_tiket where idTiket='$par[idTiket]'";
		db($sql);
		$sql="delete from sup_analisa where idTiket='$par[idTiket]'";
		db($sql);
		$sql="delete from sup_status where idTiket='$par[idTiket]'";
		db($sql);
		$sql="delete from sup_diskusi where idTiket='$par[idTiket]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function update(){
		global $s,$inp,$par,$arrParameter,$cUsername;	
		if(!getField("select createBy from sup_analisa where idTiket='$par[idTiket]'")) $createBy = ", createBy='$cUsername', createTime='".date('Y-m-d H:i:s')."'";
					
		$sql="update sup_analisa set idJenis='$inp[idJenis]', tanggalAnalisa='".setTanggal($inp[tanggalAnalisa])."', keteranganAnalisa='$inp[keteranganAnalisa]', rencanaAnalisa='$inp[rencanaAnalisa]', biayaAnalisa='$inp[biayaAnalisa]', nilaiAnalisa='".setAngka($inp[nilaiAnalisa])."' ".$createBy.", updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idTiket='$par[idTiket]'";
		db($sql);						
		
		#ANALISA
		$prosesTiket = 1;
		if(getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'") < $prosesTiket){
			$sql="update sup_tiket set prosesTiket=prosesTiket+1 where idTiket='$par[idTiket]'";
			db($sql);
		}
		
		if($inp[rencanaAnalisa] == "f"){
			$idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData desc limit 1");
			$sql="update sup_tiket set idStatus='$idStatus', selesaiTiket='".date('Y-m-d')."' where idTiket='$par[idTiket]'";
			db($sql);
		}else{
			$idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D05."' and urutanData='2'");
			$sql="update sup_tiket set idStatus='$idStatus', selesaiTiket='0000-00-00' where idTiket='$par[idTiket]'";
			if(getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'") == $prosesTiket) db($sql);
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField(array("keteranganTiket"));		
		$fileTiket=upload($par[idTiket]);
		
		$sql="update sup_tiket set idModul='$inp[idModul]', idTipe='$inp[idTipe]', idPrioritas='$inp[idPrioritas]', tanggalTiket='".setTanggal($inp[tanggalTiket])."', namaTiket='$inp[namaTiket]', keteranganTiket='$inp[keteranganTiket]', catatanTiket='$inp[catatanTiket]', fileTiket='$fileTiket', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idTiket='$par[idTiket]'";
		db($sql);
			
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$arrParameter,$cUsername;
		repField(array("keteranganTiket"));
		$idTiket = getField("select idTiket from sup_tiket order by idTiket desc limit 1")+1;
		$fileTiket=upload($idTiket);
		$idStatus = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D05."' and urutanData='1'");
		
		$createBy = $inp[skipTiket] == "t" ? $cUsername : "";
		$createTime = $inp[skipTiket] == "t" ? date('Y-m-d H:i:s') : "";
		$rencanaAnalisa = $inp[skipTiket] == "t" ? "t" : "";
		$prosesTiket = $inp[skipTiket] == "t" ? 3 : 0;
		$rencanaAnalisa = $inp[skipTiket] == "t" ? "t" : "";
		$tanggalAnalisa = $inp[skipTiket] == "t" ? setTanggal($inp[tanggalTiket]) : "";
		$diperiksaStatus = $inp[skipTiket] == "t" ? "t" : "";
		$diperiksaTanggal = $inp[skipTiket] == "t" ? setTanggal($inp[tanggalTiket]) : "";
		$disetujuiStatus = $inp[skipTiket] == "t" ? "t" : "";		
		$disetujuiTanggal = $inp[skipTiket] == "t" ? setTanggal($inp[tanggalTiket]) : "";
		
		$sql="insert into sup_tiket (idTiket, idModul, idTipe, idPrioritas, idStatus, tanggalTiket, namaTiket, keteranganTiket, catatanTiket, fileTiket, prosesTiket, picTiket, skipTiket, createBy, createTime) values ('$idTiket', '$inp[idModul]', '$inp[idTipe]', '$inp[idPrioritas]', '$idStatus', '".setTanggal($inp[tanggalTiket])."', '$inp[namaTiket]', '$inp[keteranganTiket]', '$inp[catatanTiket]', '$fileTiket', '$prosesTiket', '$inp[picTiket]', '$inp[skipTiket]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);						
		
		$sql="insert into sup_analisa (idTiket, idAnalisa, rencanaAnalisa, tanggalAnalisa, createBy, createTime) values ('$idTiket', '$idTiket', '$rencanaAnalisa', '$tanggalAnalisa', '$createBy', '$createTime')";
		db($sql);
		
		$sql="insert into sup_status (idTiket, idStatus, diperiksaStatus, diperiksaTanggal, disetujuiStatus, disetujuiTanggal) values ('$idTiket', '$idTiket', '$diperiksaStatus', '$diperiksaTanggal', '$disetujuiStatus', '$disetujuiTanggal')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idTiket")."';</script>";
	}
	
	function analisa(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;				
		$arrJenis = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".D03."' order by urutanData");		
		
		$sql="select t1.*, t2.namaUser from sup_analisa t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
						
		if(empty($r[idJenis])) $r[idJenis]=getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D03."' order by urutanData limit 1");
		if(empty($r[namaUser])) $r[namaUser]=getField("select namaUser from app_user where username='".$cUsername."'");		
		if(empty($r[tanggalAnalisa]) || $r[tanggalAnalisa]=="0000-00-00") $r[tanggalAnalisa]=date('Y-m-d');
		
		
		$nilaiAnalisa = $r[biayaAnalisa] == "t" ? "block" : "none";
		$ya =  $r[biayaAnalisa] == "t" ? "checked=\"checked\"" : "";		
		$tidak = empty($ya) ? "checked=\"checked\"" : "";
		
		$selesai =  $r[rencanaAnalisa] == "f" ? "checked=\"checked\"" : "";		
		$lanjut = empty($selesai) ? "checked=\"checked\"" : "";			
				
		setValidation("is_null","tanggalAnalisa","anda harus mengisi tanggal");				
		setValidation("is_null","inp[keteranganAnalisa]","anda harus mengisi keterangan");		
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords("analisa"))."						
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div class=\"widgetbox\" style=\"margin-top:20px;\">
				<div id=\"general\" style=\"margin-top:20px;\">										
					<p>
						<label class=\"l-input-small\">Analis</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaUser]\" name=\"inp[namaUser]\"  value=\"$r[namaUser]\" class=\"mediuminput\" style=\"width:350px;\"  readonly=\"readonly\"/>
						</div>
					</p>	
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalAnalisa\" name=\"inp[tanggalAnalisa]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalAnalisa])."\" class=\"vsmallinput hasDatePicker\"/>	
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Jenis</label>
						<div class=\"fradio\">";
					if(is_array($arrJenis)){				
						reset($arrJenis);
						while(list($idJenis, $namaJenis)=each($arrJenis)){
							$checkJenis = $r[idJenis] == $idJenis ? "checked=\"checked\"" : "";
							$text.="<input type=\"radio\" id=\"idJenis_".$idJenis."\" name=\"inp[idJenis]\" value=\"".$idJenis."\" ".$checkJenis." /> <span class=\"sradio\">".$namaJenis."</span>";
						}
					}
					$text.="</div>
					</p>			
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganAnalisa]\" name=\"inp[keteranganAnalisa]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:75px; width:360px;\">$r[keteranganAnalisa]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Biaya</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"tidak\" name=\"inp[biayaAnalisa]\" value=\"f\" onclick=\"setNilai();\" ".$tidak." /> <span class=\"sradio\" >Tidak</span>
							<input type=\"radio\" id=\"ya\" name=\"inp[biayaAnalisa]\" value=\"t\" onclick=\"setNilai();\" ".$ya." /> <span class=\"sradio\" >Ya</span>
						</div>
					</p>
					<div id=\"nilaiAnalisa\" style=\"display:$nilaiAnalisa;\">
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiAnalisa]\" name=\"inp[nilaiAnalisa]\"  value=\"".getAngka($r[nilaiAnalisa])."\" class=\"mediuminput\" style=\"width:125px; text-align:right;\" onkeyup=\"cekAngka(this);\"/>
						</div>
					</div>
					<p>
						<label class=\"l-input-small\">Recana</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"lanjut\" name=\"inp[rencanaAnalisa]\" value=\"t\" ".$lanjut." /> <span class=\"sradio\">Lanjut</span>
							<input type=\"radio\" id=\"selesai\" name=\"inp[rencanaAnalisa]\" value=\"f\" ".$selesai." /> <span class=\"sradio\">Selesai</span>
						</div>
					</p>
				</div>				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\"  onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\"/>
				</p>";
		
		$sql="select t1.*, t2.namaUser from sup_tiket t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$text.="<div class=\"title\" style=\"margin-top:20px; margin-bottom:0px;\"><h3>Permasalahan</h3></div>
				<p>
					<label class=\"l-input-small\">Judul</label>
					<span class=\"field\">$r[namaTiket]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">User</label>
					<span class=\"field\">$r[namaUser]&nbsp;</span>
				</p>	
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<span class=\"field\">".getTanggal($r[tanggalTiket],"t")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Modul</label>
					<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idModul]'")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Uraian Masalah</label>
					<span class=\"field\">
						<table>
						<tr>
						<td>$r[keteranganTiket]</td>
						</tr>
						</table>
					&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Detail</label>
					<span class=\"field\">";
				$text.=empty($r[fileTiket]) ? "":
							"<a href=\"download.php?d=tiket&f=$r[idTiket]\"><img src=\"".getIcon($r[fileTiket])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px;\"></a>							
							<br clear=\"all\">";
			$text.="&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Jenis</label>
					<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idTipe]'")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Prioritas</label>
					<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idPrioritas]'")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Catatan</label>
					<span class=\"field\">".nl2br($r[catatanTiket])."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<span class=\"field\">$r[picTiket]&nbsp;</span>
				</p>
				
				</div>
			</form>";
		return $text;
	}
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;		
		include "plugins/mce.jsp";
				
		$arrTipe = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".D04."' order by urutanData");
		$arrPrioritas = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".D02."' order by urutanData");
		
		$sql="select t1.*, t2.namaUser from sup_tiket t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
						
		if(empty($r[idTipe])) $r[idTipe]=getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D04."' order by urutanData limit 1");
		if(empty($r[idPrioritas])) $r[idPrioritas]=getField("select kodeData from mst_data where statusData='t' and kodeCategory='".D02."' order by urutanData limit 1");
		if(empty($r[namaUser])) $r[namaUser]=getField("select namaUser from app_user where username='".$cUsername."'");		
		if(empty($r[tanggalTiket])) $r[tanggalTiket]=date('Y-m-d');
		
		$disabled = $par[mode] == "edit" ? "disabled=\"disabled\"" : "";
		$skipTiket = $r[skipTiket] == "t" ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[namaTiket]","anda harus mengisi judul");		
		setValidation("is_null","tanggalTiket","anda harus mengisi tanggal");		
		setValidation("is_null","inp[idModul]","anda harus mengisi modul");		
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div class=\"widgetbox\" style=\"margin-top:20px;\">
				<div id=\"general\" style=\"margin-top:20px;\">
					<p>
						<label class=\"l-input-small\">Judul</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaTiket]\" name=\"inp[namaTiket]\"  value=\"$r[namaTiket]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">User</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaUser]\" name=\"inp[namaUser]\"  value=\"$r[namaUser]\" class=\"mediuminput\" style=\"width:350px;\"  readonly=\"readonly\"/>
						</div>
					</p>	
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalTiket\" name=\"inp[tanggalTiket]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalTiket])."\" class=\"vsmallinput hasDatePicker\"/>	
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Modul</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".D06."' order by urutanData","kodeData","namaData","inp[idModul]"," ",$r[idModul],"", "160px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Uraian Masalah</label>
						<div class=\"field\">
							<textarea id=\"mce1\" name=\"inp[keteranganTiket]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:250px; width:90%;\">$r[keteranganTiket]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Detail</label>
						<div class=\"field\">";
							$text.=empty($r[fileTiket])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileTiket\" name=\"fileTiket\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=tiket&f=$r[idTiket]\"><img src=\"".getIcon($r[fileTiket])."\" align=\"left\" style=\"padding-right:5px; padding-top:8px;\"></a>
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Jenis</label>
						<div class=\"fradio\">";
					if(is_array($arrTipe)){						
						reset($arrTipe);
						while(list($idTipe, $namaTipe)=each($arrTipe)){
							$checkTipe = $r[idTipe] == $idTipe ? "checked=\"checked\"" : "";
							$text.="<input type=\"radio\" id=\"idTipe_".$idTipe."\" name=\"inp[idTipe]\" value=\"".$idTipe."\" ".$checkTipe." /> <span class=\"sradio\">".$namaTipe."</span>";
						}
					}
					$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Prioritas</label>
						<div class=\"fradio\">";
					if(is_array($arrPrioritas)){						
						reset($arrPrioritas);
						while(list($idPrioritas, $namaPrioritas)=each($arrPrioritas)){
							$checkPrioritas = $r[idPrioritas] == $idPrioritas ? "checked=\"checked\"" : "";
							$text.="<input type=\"radio\" id=\"idPrioritas_".$idPrioritas."\" name=\"inp[idPrioritas]\" value=\"".$idPrioritas."\" ".$checkPrioritas." /> <span class=\"sradio\">".$namaPrioritas."</span>";
						}
					}
					$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Catatan</label>
						<div class=\"field\">
							<textarea id=\"inp[catatanTiket]\" name=\"inp[catatanTiket]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:75px; width:360px;\">$r[catatanTiket]</textarea>
						</div>
					</p>	
					<p>
						<label class=\"l-input-small\">PIC</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[picTiket]\" name=\"inp[picTiket]\"  value=\"$r[picTiket]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">By Pass</label>
						<div class=\"field\">
							<input type=\"checkbox\" id=\"inp[skipTiket]\" name=\"inp[skipTiket]\" value=\"t\" $skipTiket $disabled />  Approve All
						</div>
					</p>
				</div>				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\"  onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\"/>
				</p>
				</div>
			</form>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
		
		if(empty($par[mulaiTiket])) $par[mulaiTiket] = date('01/m/Y');
		if(empty($par[selesaiTiket])) $par[selesaiTiket] = date('d/m/Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td><input type=\"text\" id=\"mulaiTiket\" name=\"par[mulaiTiket]\" size=\"10\" maxlength=\"10\" value=\"".$par[mulaiTiket]."\" class=\"vsmallinput hasDatePicker\"/></td>
				<td>s.d</td>
				<td><input type=\"text\" id=\"selesaiTiket\" name=\"par[selesaiTiket]\" size=\"10\" maxlength=\"10\" value=\"".$par[selesaiTiket]."\" class=\"vsmallinput hasDatePicker\"/></td>				
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idTiket")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"75\">Nomor</th>
					<th>Judul</th>			
					<th width=\"150\">User</th>
					<th width=\"75\">Prioritas</th>
					<th width=\"50\">Analisa</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
				
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."'";
				
		$arrIcon = array(
			"<img src=\"styles/images/f.png\" title=\"Belum Selesai\">",
			"<img src=\"styles/images/f.png\" title=\"Belum Selesai\">",
			"<img src=\"styles/images/p.png\" title=\"Masih Diproses\">",
			"<img src=\"styles/images/o.png\" title=\"Pending\">",
			"<img src=\"styles/images/t.png\" title=\"Selesai\">",
		);
		$arrStatus = arrayQuery("select kodeData, urutanData from mst_data where statusData='t' and kodeCategory='".D05."' order by urutanData");
		
		$sql="select * from sup_tiket t1 join app_user t2 join mst_data t3 join sup_analisa t4 join sup_status t5 on (t1.createBy=t2.username and t1.idPrioritas=t3.kodeData and t1.idTiket=t4.idTiket and t1.idTiket=t5.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$analisaTiket = (empty($r[tanggalAnalisa]) || $r[tanggalAnalisa]=="0000-00-00") ? "<img src=\"styles/images/f.png\">" : "<img src=\"styles/images/t.png\">";
			$urutanStatus = $arrStatus["$r[idStatus]"];
			
			$tesStatus = "<img src=\"styles/images/f.png\" title=\"Belum Tes\">";
			if($r[tesStatus] == "t") $tesStatus = "<img src=\"styles/images/p.png\" title=\"Proses Testing\">";
			if($r[tesStatus] == "f") $tesStatus = "<img src=\"styles/images/t.png\" title=\"Selesai\">";
			
			$text.="<tr>
					<td>$no.</td>
					<td align=\"center\">".getTanggal($r[tanggalTiket])."</td>
					<td align=\"center\"><a href=\"?par[mode]=det&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Detail Tiket\" class=\"detil\">".str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)."</a></td>
					<td>$r[namaTiket]</td>
					<td>$r[namaUser]</td>
					<td>$r[namaData]</td>
					<td align=\"center\">";				
			$text.= isset($menuAccess[$s]["edit"]) ? "<a href=\"?par[mode]=upd&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Analisa Data\" >".$analisaTiket."</a>" : $analisaTiket;
			$text.="</td>
					<td align=\"center\">".$tesStatus."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idTiket]=$r[idTiket]".getPar($par,"mode,kodeCustomer")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;
		$prosesTiket = getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'");
		
		if($prosesTiket > 0){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:block;\"";			
			
			$cAnalisa = "class=\"current\"";									
		}else{
			$dPermasalahan = "style=\"display:block;\"";
			$dAnalisa = "style=\"display:none;\"";			
			
			$cPermasalahan = "class=\"current\"";			
			$sAnalisa = "style=\"display:none;\"";			
		}
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords("detail"))."
					<ul class=\"hornav\">						
						<li ".$cAnalisa." ".$sAnalisa."><a href=\"#analisa\">Analisa</a></li>
						<li ".$cPermasalahan."><a href=\"#permasalahan\">Permasalahan</a></li>
					</ul>
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\" >";				
				
		#TAB ANALISA
		$sql="select t1.*, t2.namaUser from sup_analisa t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$nilaiAnalisa = $r[biayaAnalisa] == "t" ? "block" : "none";
		$biayaAnalisa = $r[biayaAnalisa] == "t" ? "Ya" : "Tidak";
		$rencanaAnalisa = $r[rencanaAnalisa] == "t" ? "Lanjut" : "Selesai";
		
		$text.="<div id=\"analisa\" class=\"subcontent\" ".$dAnalisa.">
					<p>
						<label class=\"l-input-small\">Analis</label>
						<span class=\"field\">".$r[namaUser]."&nbsp;</span>
					</p>	
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal($r[tanggalAnalisa],"t")."&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Jenis</label>
						<span class=\"field\">";
		$namaJenis = getField("select namaData from mst_data where kodeData='$r[idJenis]'");
		$text.= $namaJenis ? $namaJenis : "By Pass";
		$text.="&nbsp;</span>
					</p>			
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[keteranganAnalisa])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Biaya</label>
						<span class=\"field\">".$biayaAnalisa."&nbsp;</span>
					</p>
					<div id=\"nilaiAnalisa\" style=\"display:$nilaiAnalisa;\">
						<label class=\"l-input-small\">Nilai</label>
						<span class=\"field\">".getAngka($r[nilaiAnalisa])."&nbsp;</span>
					</div>
					<p>
						<label class=\"l-input-small\">Recana</label>
						<span class=\"field\">".$rencanaAnalisa."&nbsp;</span>
					</p>
				</div>";
				
		#TAB PERMASALAHAN
		$sql="select t1.*, t2.namaUser from sup_tiket t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$text.="<div id=\"permasalahan\" class=\"subcontent\" ".$dPermasalahan.">
					<p>
						<label class=\"l-input-small\">Judul</label>
						<span class=\"field\">$r[namaTiket]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">User</label>
						<span class=\"field\">$r[namaUser]&nbsp;</span>
					</p>	
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal($r[tanggalTiket],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Modul</label>
						<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idModul]'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Uraian Masalah</label>
						<span class=\"field\">
							<table>
							<tr>
							<td>$r[keteranganTiket]</td>
							</tr>
							</table>
						&nbsp;</span>
					</p>";
				$text.=empty($r[fileTiket]) ? "":
						"<p>
						<label class=\"l-input-small\">Detail</label>
						<span class=\"field\"><a href=\"download.php?d=tiket&f=$r[idTiket]\"><img src=\"".getIcon($r[fileTiket])."\" align=\"left\" style=\"padding-right:5px; padding-top:8px;\"></a>&nbsp;</span>
						</p>";
				$text.="<p>
						<label class=\"l-input-small\">Jenis</label>
						<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idTipe]'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Prioritas</label>
						<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idPrioritas]'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Catatan</label>
						<span class=\"field\">".nl2br($r[catatanTiket])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">PIC</label>
						<span class=\"field\">$r[picTiket]&nbsp;</span>
					</p>
				</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\"  onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\"/>
				</p>
			</form>";
		return $text;
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){	
			case "delFile":
				if(isset($menuAccess[$s]["delete"])) $text = hapusFile(); else $text = lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "upd":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? analisa() : update(); else $text = lihat();
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
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/ticket/";
	
	function ubah(){
		global $s,$inp,$par,$arrParameter,$cUsername;	
		if(!getField("select diperiksaBy from sup_status where idTiket='$par[idTiket]'")) $diperiksaBy = ", diperiksaBy='$cUsername', diperiksaTime='".date('Y-m-d H:i:s')."'";
					
		$sql="update sup_status set diperiksaTanggal='".setTanggal($inp[diperiksaTanggal])."', diperiksaStatus='$inp[diperiksaStatus]', diperiksaKeterangan='$inp[diperiksaKeterangan]' ".$diperiksaBy.", updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idTiket='$par[idTiket]'";
		db($sql);
		
		#DIPERIKSA
		$prosesTiket = 2;
		if(getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'") < $prosesTiket){
			$sql="update sup_tiket set prosesTiket=prosesTiket+1 where idTiket='$par[idTiket]'";
			db($sql);
		}
		
		if($inp[diperiksaStatus] == "f"){
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
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;		
		$sql="select t1.*, t2.namaUser from sup_status t1 left join app_user t2 on (t1.diperiksaBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
								
		if(empty($r[namaUser])) $r[namaUser]=getField("select namaUser from app_user where username='".$cUsername."'");		
		if(empty($r[diperiksaTanggal]) || $r[diperiksaTanggal]=="0000-00-00") $r[diperiksaTanggal]=date('Y-m-d');
		
		$tidak =  $r[diperiksaStatus] == "f" ? "checked=\"checked\"" : "";		
		$setuju = empty($tidak) ? "checked=\"checked\"" : "";			
				
		setValidation("is_null","diperiksaTanggal","anda harus mengisi tanggal");				
		setValidation("is_null","inp[diperiksaKeterangan]","anda harus mengisi keterangan");		
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
					<ul class=\"hornav\">
						<li class=\"current\"><a href=\"#diperiksa\">Diperiksa</a></li>
						<li><a href=\"#analisa\">Analisa</a></li>
						<li><a href=\"#permasalahan\">Permasalahan</a></li>
					</ul>
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">";
				
		#TAB DIPERIKSA
		$text.="<div id=\"diperiksa\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"diperiksaTanggal\" name=\"inp[diperiksaTanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[diperiksaTanggal])."\" class=\"vsmallinput hasDatePicker\"/>	
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nama Pemeriksa</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaUser]\" name=\"inp[namaUser]\"  value=\"$r[namaUser]\" class=\"mediuminput\" style=\"width:350px;\"  readonly=\"readonly\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"setuju\" name=\"inp[diperiksaStatus]\" value=\"t\" ".$setuju." /> <span class=\"sradio\">Setuju</span>
							<input type=\"radio\" id=\"tidak\" name=\"inp[diperiksaStatus]\" value=\"f\" ".$tidak." /> <span class=\"sradio\">Tidak Setuju</span>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[diperiksaKeterangan]\" name=\"inp[diperiksaKeterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:75px; width:360px;\">$r[diperiksaKeterangan]</textarea>
						</div>
					</p>
				</div>";
				
		#TAB ANALISA
		$sql="select t1.*, t2.namaUser from sup_analisa t1 left join app_user t2 on (t1.createBy=t2.username) where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$nilaiAnalisa = $r[biayaAnalisa] == "t" ? "block" : "none";
		$biayaAnalisa = $r[biayaAnalisa] == "t" ? "Ya" : "Tidak";
		$rencanaAnalisa = $r[rencanaAnalisa] == "t" ? "Lanjut" : "Selesai";
		
		$text.="<div id=\"analisa\" class=\"subcontent\" style=\"display:none\">
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
		$text.="<div id=\"permasalahan\" class=\"subcontent\" style=\"display:none\">
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
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\"  onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\"/>
				</p>
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
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\" width=\"75\">Tanggal</th>
					<th rowspan=\"2\" width=\"75\">Nomor</th>
					<th rowspan=\"2\">Judul</th>			
					<th rowspan=\"2\" width=\"150\">User</th>
					<th rowspan=\"2\" width=\"75\">Prioritas</th>
					<th colspan=\"2\">Tanggal</th>
					<th rowspan=\"2\" width=\"50\">Status</th>
				</tr>
				<tr>
					<th width=\"75\">Analisa</th>
					<th width=\"75\">Diperiksa</th>
				</tr>
			</thead>
			<tbody>";
		
				
		$filter = "where t1.tanggalTiket between '".setTanggal($par[mulaiTiket])."' and '".setTanggal($par[selesaiTiket])."' and t4.rencanaAnalisa='t'";
						
		$sql="select * from sup_tiket t1 join app_user t2 join mst_data t3 join sup_analisa t4 join sup_status t5 on (t1.createBy=t2.username and t1.idPrioritas=t3.kodeData and t1.idTiket=t4.idTiket and t1.idTiket=t5.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$diperiksaTanggal = (empty($r[diperiksaTanggal]) || $r[diperiksaTanggal]=="0000-00-00") ? "<img src=\"styles/images/f.png\">" : getTanggal($r[diperiksaTanggal]);
			
			$diperiksaStatus = "<img src=\"styles/images/p.png\" title=\"Belum Diperiksa\">";
			if($r[diperiksaStatus] == "t") $diperiksaStatus = "<img src=\"styles/images/t.png\" title=\"Setuju\">";
			if($r[diperiksaStatus] == "f") $diperiksaStatus = "<img src=\"styles/images/f.png\" title=\"Tidak Setuju\">";
			
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
					<td align=\"center\">".getTanggal($r[tanggalAnalisa])."</td>
					<td align=\"center\">";				
			$text.= isset($menuAccess[$s]["edit"]) ? "<a href=\"?par[mode]=edit&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Periksa Tiket\" >".$diperiksaTanggal."</a>" : $diperiksaTanggal;
			$text.="</td>
					<td align=\"center\">".$tesStatus."</td>
					</tr>";			
		}
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;
		$prosesTiket = getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'");
		
		$sql="select * from sup_status where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
		
		$diperiksaStatus = $r[diperiksaStatus] == "f" ? "Tidak Setuju" : "Setuju";		
		
		if($prosesTiket == 1){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:block;\"";
			$dDiperiksa = "style=\"display:none;\"";			
			
			$cAnalisa = "class=\"current\"";						
			$sDiperiksa = "style=\"display:none;\"";			
		}else if($prosesTiket > 1){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:block;\"";
			
			$cDiperiksa = "class=\"current\"";			
		}else{
			$dPermasalahan = "style=\"display:block;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:none;\"";			
			
			$cPermasalahan = "class=\"current\"";			
			$sAnalisa = "style=\"display:none;\"";
			$sDiperiksa = "style=\"display:none;\"";			
		}
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords("detail"))."
					<ul class=\"hornav\">						
						<li ".$cDiperiksa." ".$sDiperiksa."><a href=\"#diperiksa\">Diperiksa</a></li>
						<li ".$cAnalisa." ".$sAnalisa."><a href=\"#analisa\">Analisa</a></li>
						<li ".$cPermasalahan."><a href=\"#permasalahan\">Permasalahan</a></li>
					</ul>
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\" >";				
		#TAB DIPERIKSA
		$text.="<div id=\"diperiksa\" class=\"subcontent\" ".$dDiperiksa.">
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal($r[diperiksaTanggal],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nama Pemeriksa</label>
						<span class=\"field\">";
		$namaUser = getField("select namaUser from app_user where username='".$r[diperiksaBy]."'");
		$text.= $namaUser ? $namaUser : "By Pass";
		$text.="&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$diperiksaStatus."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[diperiksaKeterangan])."&nbsp;</span>
					</p>
				</div>";
				
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
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
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
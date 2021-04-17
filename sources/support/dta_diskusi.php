<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/ticket/";
	
	function hapus(){
		global $s,$inp,$par,$arrParameter,$cUsername;				
		$sql="delete from sup_diskusi where idDiskusi='$par[idDiskusi]'";
		db($sql);
		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idDiskusi")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$arrParameter,$cUsername;
		repField(array("keteranganDiskusi"));				
		
		$sql="update sup_diskusi set keteranganDiskusi='$inp[keteranganDiskusi]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDiskusi='$par[idDiskusi]'";
		if(!empty($inp[keteranganDiskusi])) db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$arrParameter,$cUsername;
		repField(array("keteranganDiskusi"));		
		$idDiskusi = getField("select idDiskusi from sup_diskusi order by idDiskusi desc limit 1")+1;
		
		$sql="insert into sup_diskusi (idDiskusi, idTiket, keteranganDiskusi, createBy, createTime) values ('$idDiskusi', '$par[idTiket]', '$inp[keteranganDiskusi]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		if(!empty($inp[keteranganDiskusi])) db($sql);
		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idDiskusi")."';</script>";
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
		
		if(empty($par[mulaiTiket])) $par[mulaiTiket] = date('01/m/Y');
		if(empty($par[selesaiTiket])) $par[selesaiTiket] = date('d/m/Y');
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
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
					<th width=\"50\">Status</th>
					<th width=\"50\">Diskusi</th>
				</tr>
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
		$arrDiskusi = arrayQuery("select t2.idTiket, count(t2.idDiskusi) from sup_tiket t1 join sup_diskusi t2 on (t1.idTiket=t2.idTiket) $filter group by 1");
		
		$sql="select * from sup_tiket t1 join app_user t2 join mst_data t3 join sup_analisa t4 on (t1.createBy=t2.username and t1.idPrioritas=t3.kodeData and t1.idTiket=t4.idTiket) $filter order by t1.tanggalTiket";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;						
			$urutanStatus = $arrStatus["$r[idStatus]"];
			
			$text.="<tr>
					<td>$no.</td>
					<td align=\"center\">".getTanggal($r[tanggalTiket])."</td>
					<td align=\"center\"><a href=\"?par[mode]=det&par[idTiket]=$r[idTiket]".getPar($par,"mode,idTiket")."\" title=\"Detail Tiket\" class=\"detil\">".str_pad($r[idTiket], 3, "0", STR_PAD_LEFT)."</a></td>
					<td>$r[namaTiket]</td>
					<td>$r[namaUser]</td>
					<td>$r[namaData]</td>
					<td align=\"center\">".$arrIcon[$urutanStatus]."</td>
					<td align=\"center\">".getAngka($arrDiskusi["$r[idTiket]"])."</td>
					</tr>";			
		}
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function form(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		include "plugins/mce.jsp";
		
		$sql="select * from sup_diskusi where idDiskusi='$par[idDiskusi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);									
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" >	
				<div id=\"general\" class=\"subcontent\">										
					<textarea id=\"mce1\" name=\"inp[keteranganDiskusi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:250px; width:100%;\">$r[keteranganDiskusi]</textarea>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile,$cUsername;
		include "plugins/mce.jsp";
		
		$prosesTiket = getField("select prosesTiket from sup_tiket where idTiket='$par[idTiket]'");
		
		$sql="select * from sup_status where idTiket='$par[idTiket]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
		
		$diperiksaStatus = $r[diperiksaStatus] == "f" ? "Tidak Setuju" : "Setuju";
		$disetujuiStatus = $r[diperiksaStatus] == "f" ? "Selesai" : "Dikerjakan";
		$dikerjakanStatus = $r[dikerjakanStatus] == "f" ? "Selesai" : "Proses";
		$tesStatus = $r[tesStatus] == "f" ? "Selesai" : "Proses";
		
		if($prosesTiket == 1){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:block;\"";
			$dDiperiksa = "style=\"display:none;\"";
			$dDisetujui = "style=\"display:none;\"";
			$dDikerjakan = "style=\"display:none;\"";
			$dTesting = "style=\"display:none;\"";
			
			$cAnalisa = "class=\"current\"";						
			$sDiperiksa = "style=\"display:none;\"";
			$sDisetujui = "style=\"display:none;\"";
			$sDikerjakan = "style=\"display:none;\"";
			$sTesting = "style=\"display:none;\"";
		}else if($prosesTiket == 2){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:block;\"";
			$dDisetujui = "style=\"display:none;\"";
			$dDikerjakan = "style=\"display:none;\"";
			$dTesting = "style=\"display:none;\"";
			
			$cDiperiksa = "class=\"current\"";					
			$sDisetujui = "style=\"display:none;\"";
			$sDikerjakan = "style=\"display:none;\"";
			$sTesting = "style=\"display:none;\"";
		}else if($prosesTiket == 3){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:none;\"";
			$dDisetujui = "style=\"display:block;\"";
			$dDikerjakan = "style=\"display:none;\"";
			$dTesting = "style=\"display:none;\"";
			
			$cDisetujui = "class=\"current\"";						
			$sDikerjakan = "style=\"display:none;\"";
			$sTesting = "style=\"display:none;\"";
		}else if($prosesTiket == 4){
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:none;\"";
			$dDisetujui = "style=\"display:none;\"";
			$dDikerjakan = "style=\"display:block;\"";
			$dTesting = "style=\"display:none;\"";
			
			$cDikerjakan = "class=\"current\"";									
			$sTesting = "style=\"display:none;\"";
		}else if($prosesTiket == 5){						
			$dPermasalahan = "style=\"display:none;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:none;\"";
			$dDisetujui = "style=\"display:none;\"";
			$dDikerjakan = "style=\"display:none;\"";
			$dTesting = "style=\"display:block;\"";
			
			$cTesting = "class=\"current\"";
		}else{
			$dPermasalahan = "style=\"display:block;\"";
			$dAnalisa = "style=\"display:none;\"";
			$dDiperiksa = "style=\"display:none;\"";
			$dDisetujui = "style=\"display:none;\"";
			$dDikerjakan = "style=\"display:none;\"";
			$dTesting = "style=\"display:none;\"";
			
			$cPermasalahan = "class=\"current\"";			
			$sAnalisa = "style=\"display:none;\"";
			$sDiperiksa = "style=\"display:none;\"";
			$sDisetujui = "style=\"display:none;\"";
			$sDikerjakan = "style=\"display:none;\"";
			$sTesting = "style=\"display:none;\"";
		}
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords("detail"))."
					<ul class=\"hornav\">
						<li ".$cTesting." ".$sTesting."><a href=\"#testing\">Testing</a></li>
						<li ".$cDikerjakan." ".$sDikerjakan."><a href=\"#dikerjakan\">Dikerjakan</a></li>
						<li ".$cDisetujui." ".$sDisetujui."><a href=\"#disetujui\">Disetujui</a></li>
						<li ".$cDiperiksa." ".$sDiperiksa."><a href=\"#diperiksa\">Diperiksa</a></li>
						<li ".$cAnalisa." ".$sAnalisa."><a href=\"#analisa\">Analisa</a></li>
						<li ".$cPermasalahan."><a href=\"#permasalahan\">Permasalahan</a></li>
					</ul>
				</div>
				<div class=\"contentwrapper\">
				<form id=\"detail\" name=\"detail\" class=\"stdform\" >";
		
		#TAB TESTING
		$text.="<div id=\"testing\" class=\"subcontent\" ".$dTesting.">
					<p>
						<label class=\"l-input-small\">Tanggal Mulai</label>
						<span class=\"field\">".getTanggal($r[tesMulai],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Perkiraan Selesai</label>
						<span class=\"field\">".getTanggal($r[tesSelesai],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nama</label>
						<span class=\"field\">".getField("select namaUser from app_user where username='".$r[tesBy]."'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$tesStatus."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[tesKeterangan])."&nbsp;</span>
					</p>
				</div>";
		
		
		#TAB DIKERJAKAN
		$text.="<div id=\"dikerjakan\" class=\"subcontent\" ".$dDikerjakan.">
					<p>
						<label class=\"l-input-small\">Tanggal Mulai</label>
						<span class=\"field\">".getTanggal($r[dikerjakanMulai],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Perkiraan Selesai</label>
						<span class=\"field\">".getTanggal($r[dikerjakanSelesai],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nama</label>
						<span class=\"field\">".getField("select namaUser from app_user where username='".$r[dikerjakanBy]."'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$dikerjakanStatus."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[dikerjakanKeterangan])."&nbsp;</span>
					</p>
				</div>";
		
		#TAB DISETUJUI
		$text.="<div id=\"disetujui\" class=\"subcontent\" ".$dDisetujui.">
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal($r[disetujuiTanggal],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nama Approval</label>
						<span class=\"field\">";
		$namaUser = getField("select namaUser from app_user where username='".$r[disetujuiBy]."'");
		$text.= $namaUser ? $namaUser : "By Pass";
		$text.="&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$disetujuiStatus."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[disetujuiKeterangan])."&nbsp;</span>
					</p>
				</div>";
		
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
			</form>	
			<div class=\"widgetbox\">
			<div class=\"title\" style=\"margin-top:20px; margin-bottom:10px;\"><h3>Diskusi</h3></div>";
		$sql="select t1.*, t2.namaUser from sup_diskusi t1 join app_user t2 on (t1.createBy=t2.username) where t1.idTiket='$par[idTiket]' order by t1.createTime desc";
		$res=db($sql);
		
		if(mysql_num_rows($res)){
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:100%; padding:5px;\" id=\"dyntable\">
				<thead style=\"display:none;\">
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th>&nbsp;</th>";
				$text.="</tr>
				</thead>
				<tbody>";
					
			while($r=mysql_fetch_array($res)){
				$text.="<tr>
						<td style=\"vertical-align:top; padding-bottom:10px; width:150px; border-bottom:solid 1px #CCC;\" align=\"center\">
							".getDiskusi($r[createTime])."
						</td>					
						<td style=\"vertical-align:top; padding-bottom:10px; border-bottom:solid 1px #CCC;\">
							$r[keteranganDiskusi]
							<span style=\"font-size:11px;\"><strong>Pengirim :</strong> $r[namaUser]<span>
						</td>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
					$text.="<td style=\"vertical-align:top; padding-bottom:10px; width:50px; border-bottom:solid 1px #CCC;\" align=\"center\">";				
					if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=edit&par[idDiskusi]=$r[idDiskusi]".getPar($par,"mode,idDiskusi")."',850,425);\" title=\"Edit Data\" class=\"edit\"><span><img src=\"styles/images/icons/edit.png\" border=\"0\"></span></a> ";				
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idDiskusi]=$r[idDiskusi]".getPar($par,"mode,kodeCustomer")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" ><span><img src=\"styles/images/icons/delete.png\" border=\"0\"></span></a>";
					$text.="</td>";
				}
				$text.="</tr>";
			}
			$text.="</tbody>
				</table>
				<br clear=\"all\">";
		}
		$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\">	
				<textarea id=\"mce1\" name=\"inp[keteranganDiskusi]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:250px; width:100%;\">$r[keteranganDiskusi]</textarea>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Kirim\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\"  onclick=\"window.location='?".getPar($par,"mode,idTiket")."';\"/>
				</p>
			</form></div>";
		return $text;
	}
	
	function getDiskusi($createTime){
		list($tanggalDiskusi, $waktuDiskusi) = explode(" ", $createTime);
		list($tahun, $bulan, $tanggal) = explode("-", $tanggalDiskusi);
		
		$text.="<table style=\"margin:10px;\">
				<tr>
				<td align=\"center\"><h1>".$tanggal."</h1>".getBulan($bulan)." ".$tahun."</td>
				</tr>
				<tr>
				<td align=\"center\" style=\"font-size:10px; font-weight:bold; color:#fe7700;\">".$waktuDiskusi."</td>
				</tr>
				</table>";
		
		return $text;
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){		
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;			
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "det":				
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? detail() : tambah(); else $text = detail();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
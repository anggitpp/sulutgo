<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
			
	function absen(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_hadir where idHadir='$par[idData]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
		
		if(empty($r[nomorHadir])) $r[nomorHadir] = gNomor();
		if(empty($r[tanggalHadir])) $r[tanggalHadir] = date('Y-m-d');		

		$hariHadir =  $r[hariHadir] == "t" ? "Ya" : "Tidak";
		
		list($mulaiHadir_tanggal, $mulaiHadir_waktu) = explode(" ", $r[mulaiHadir]);
		list($selesaiHadir_tanggal, $selesaiHadir_waktu) = explode(" ", $r[selesaiHadir]);
		
		if($mulaiHadir_tanggal != $selesaiHadir_tanggal){
			$jamMulai = "none";
			$jamSelesai = "none";
			$allDay = "none";
		}else{
			if($r[hariHadir] == "t"){
				$jamMulai = "none";
				$jamSelesai = "none";
			}else{				
				$jamMulai = "block";
				$jamSelesai = "block";			
			}
			
			$allDay = "block";	
		}
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">".$r[nomorHadir]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalHadir],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN KETIDAKHADIRAN</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
						<p>
							<label class=\"l-input-small\">Tanggal Mulai</label>
							<span>
								<div style=\"float:left; width:125px;\">".getTanggal($mulaiHadir_tanggal,"t")."</div>
								<span id=\"jamMulai\" style=\"display:".$jamMulai."\">
									<label class=\"l-input-small\">Jam Mulai</label>
									".substr($mulaiHadir_waktu,0,5)."
								</span>&nbsp;
							</span>
						</p>
						</div>
						<div style=\"padding-bottom:5px; border-bottom:solid 1px #eee;\">
						<p>
							<label class=\"l-input-small\">Tanggal Selesai</label>
							<span>
								<div style=\"float:left; width:125px;\">".getTanggal($selesaiHadir_tanggal,"t")."</div>
								<span id=\"jamMulai\" style=\"display:".$jamSelesai."\">
									<label class=\"l-input-small\">Jam Selesai</label>
									".substr($selesaiHadir_waktu,0,5)."
								</span>&nbsp;
							</span>
						</p>
						</div>
						<div id=\"allDay\" style=\"display:".$allDay."\">
						<p>
							<label class=\"l-input-small\">All Day</label>
							<span class=\"field\">".$hariHadir."&nbsp;</div>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganHadir])."&nbsp;</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						";
					
					$sql_="select
						id as idPengganti,
						reg_no as nikPengganti,
						name as namaPengganti
					from emp where id='".$r[idPengganti]."'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);					
					$text.="<p>
							<label class=\"l-input-small\">Pengganti</label>
							<span class=\"field\">".$r_[nikPengganti]." - ".$r_[namaPengganti]."&nbsp;</span>
						</p>";
						
						
					$sql_="select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='".$r[idAtasan]."'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Kategori Izin</label>
							<span class=\"field\">
								".getField("select namaData from mst_data where kodeData='$r[idKategori]'")."&nbsp;
							</span>
						</p>
						<div id=\"tipeIzin\" style=\"display:$tipeIzin\">
						<p>
							<label class=\"l-input-small\">Tipe Izin</label>
							<span class=\"field\">
								".getField("select namaData from mst_data where kodeData='$r[idTipe]'")."&nbsp;
							</span>
						</p>
						</div>
						<p>
							<label class=\"l-input-small\">Dokumen</label>
							<span class=\"field\">";
							$text.=empty($r[fileHadir])? "":
								"<a href=\"download.php?d=hadir&f=$r[idHadir]\"><img src=\"".getIcon($fFile."/".$r[fileHadir])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
							$text.="&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanHadir = "Belum Diproses";
			$persetujuanHadir = $r[persetujuanHadir] == "t" ? "Disetujui" : $persetujuanHadir;
			$persetujuanHadir = $r[persetujuanHadir] == "f" ? "Ditolak" : $persetujuanHadir;		
			$persetujuanHadir = $r[persetujuanHadir] == "r" ? "Diperbaiki" : $persetujuanHadir;		
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanHadir."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanHadir])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmHadir = "Belum Diproses";
			$sdmHadir = $r[sdmHadir] == "t" ? "Disetujui" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "f" ? "Ditolak" : $sdmHadir;
			$sdmHadir = $r[sdmHadir] == "r" ? "Diperbaiki" : $sdmHadir;
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmHadir."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteHadir])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"history.back();\" style=\"float:right;\"/>
				</p>
			</form>";
		return $text;
	}
	
	function lembur(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from att_lembur where idLembur='$par[idData]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		if(empty($r[nomorLembur])) $r[nomorLembur] = gNomor();
		if(empty($r[tanggalLembur])) $r[tanggalLembur] = date('Y-m-d');
		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">".$r[nomorLembur]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>
					</td>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalLembur],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN LEMBUR</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"55%\" style=\"vertical-align:top\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($mulaiLembur_tanggal,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<span class=\"field\">".substr($mulaiLembur,0,5)." <strong>s.d</strong> ".substr($selesaiLembur,0,5)."&nbsp;</span>
						</p>";
					
					$sql_="select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='".$r[idAtasan]."'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					$persetujuanLembur = $r[persetujuanLembur] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
					$persetujuanLembur = $r[persetujuanLembur] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanLembur;
					$persetujuanLembur = $r[persetujuanLembur] == "r"? "<img src=\"styles/images/o.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Diperbaiki\">" : $persetujuanLembur;
					
					list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
					$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
					$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;
					
					$text.="<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
						</p>						
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[keteranganLembur])."&nbsp;</span>
						</p>
					</td>
					<td width=\"45%\" style=\"vertical-align:top\">
						<p>
							<label class=\"l-input-small\">Shift</label>
							<span class=\"field\">".getField("select namaShift from dta_shift where idShift='".$r[idShift]."'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Divisi</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[idDivisi]."'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[idGedung]."'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Group</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[idGroup]."'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Pos</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[idPos]."'")."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";
			
			$persetujuanLembur = "Belum Diproses";
			$persetujuanLembur = $r[persetujuanLembur] == "t" ? "Disetujui" : $persetujuanLembur;
			$persetujuanLembur = $r[persetujuanLembur] == "f" ? "Ditolak" : $persetujuanLembur;		
			$persetujuanLembur = $r[persetujuanLembur] == "r" ? "Diperbaiki" : $persetujuanLembur;		
			list($approveDate) = explode(" ", $r[approveTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($approveDate,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[approveBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$persetujuanLembur."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[catatanLembur])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";					
					
			$sdmLembur = "Belum Diproses";
			$sdmLembur = $r[sdmLembur] == "t" ? "Disetujui" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "f" ? "Ditolak" : $sdmLembur;
			$sdmLembur = $r[sdmLembur] == "r" ? "Diperbaiki" : $sdmLembur;
			list($sdmDate) = explode(" ", $r[sdmTime]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($sdmDate,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[sdmBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmLembur."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteLembur])."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Overtime</label>
							<span class=\"field\">".getAngka($r[overtimeLembur])."&nbsp;Jam</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			$text.="</div>
				<p>									
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"history.back();\" style=\"float:right;\"/>
				</p>
			</form>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;						
		$cutil = new Common();
		if(empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');							
		$arrShift = arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");
		$arrShift[0] = "OFF";
			
		$text.="<script>
				function getDivisi(){
					if(document.getElementById('pSearch').value == '889'){";
						if (is_array($arrShift)) {
							reset($arrShift);
							while (list($idShift, $namaShift) = each($arrShift)) {
								$text.= in_array($idShift, array(3)) ?
								"document.getElementById('tab-".$idShift."').className = 'current';":
								"document.getElementById('tab-".$idShift."').className = '';";
								
								$text.= in_array($idShift, array(0, 3)) ?
								"document.getElementById('tab-".$idShift."').style.display = 'block';":
								"document.getElementById('tab-".$idShift."').style.display = 'none';";
								
								$text.= in_array($idShift, array(3)) ?
								"document.getElementById('".$idShift."').style.display = 'block';":
								"document.getElementById('".$idShift."').style.display = 'none';";
							}
						}
				$text.="}else{";
						if (is_array($arrShift)) {
							reset($arrShift);
							while (list($idShift, $namaShift) = each($arrShift)) {
								$text.= in_array($idShift, array(1)) ?
								"document.getElementById('tab-".$idShift."').className = 'current';":
								"document.getElementById('tab-".$idShift."').className = '';";
								
								$text.= "document.getElementById('tab-".$idShift."').style.display = 'block';";
								
								$text.= in_array($idShift, array(1)) ?
								"document.getElementById('".$idShift."').style.display = 'block';":
								"document.getElementById('".$idShift."').style.display = 'none';";
							}
						}
				$text.="}
				}
			</script>
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">	
			".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[statusPegawai]","All",$par[statusPegawai],"onchange=\"document.getElementById('form').submit();\"", "110px")."		
			<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
			<input type=\"text\" id=\"tanggalAbsen\" name=\"par[tanggalAbsen]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAbsen]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />
			<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			</form>
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; top:0; right:0; margin-top:95px; margin-right:30px;\">
				<input id=\"bView\" type=\"button\" value=\"+ View\" class=\"btn btn_search btn-small\" onclick=\"
					document.getElementById('bView').style.display = 'none';
					document.getElementById('bHide').style.display = 'block';
					document.getElementById('dFilter').style.visibility = 'visible';
					document.getElementById('fSet').style.height = '250px';
				\" />
				<input id=\"bHide\" type=\"button\" value=\"- Hide\" class=\"btn btn_search btn-small\" style=\"display:none\" onclick=\"
					document.getElementById('bView').style.display = 'block';
					document.getElementById('bHide').style.display = 'none';
					document.getElementById('dFilter').style.visibility = 'collapse';
					document.getElementById('fSet').style.height = '90px';
				\" />
			</div>
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px; height:90px;\">						
			<legend style=\"padding:10px; margin-left:20px;\"><h4>FILTER PENCARIAN</h4></legend>						
			<p>
				<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">NAMA</label>
				<div class=\"field\" style=\"margin-left:150px;\">
					<input type=\"text\" id=\"sSearch\" name=\"sSearch\" value=\"\" style=\"width:290px;\"/>
				</div>
			</p>
			<div id=\"dFilter\" style=\"visibility:collapse;\">
				<p>
					<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[38]) . "</label>
					<div class=\"field\" style=\"margin-left:150px;\">
						".comboData("select kodeData, namaData from mst_data where kodeCategory='X04' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"pSearch", "ALL", "", "", "300px;")."
					</div>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
					<div class=\"field\" style=\"margin-left:150px;\">";
						$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
	                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
	                       where t2.kodeCategory='X04' AND t1.statusData = 't' order by t1.urutanData";
	                       $text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "bSearch", $_GET['bSearch'], "", "class='chosen-select' style=\"width: 300px\"");
					$text .= "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
					<div class=\"field\" style=\"margin-left:150px;\">";
						$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
	                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
	                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
	                       where t3.kodeCategory='X04' AND t1.statusData = 't' order by t1.urutanData";
	                       $text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "tSearch", $_GET['tSearch'], "", "class='chosen-select' style=\"width: 300px\"");
					$text .= "
					</div>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
					<div class=\"field\" style=\"margin-left:150px;\">";
						$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
	                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
	                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
	                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
	                       where t4.kodeCategory='X04' AND t1.statusData = 't' order by t1.urutanData";
	                       $text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "mSearch", $_GET['mSearch'], "", "class='chosen-select' style=\"width: 300px\"");
					$text .= "
					</div>
				</p>
			</div>
			</fieldset>			
			</form>
			<br clear=\"all\" />
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>VIEW DATA</h3></div>
			</div>	
			<ul class=\"hornav\">";			
			
			$no=1;
			if (is_array($arrShift)) {
				reset($arrShift);
				while (list($idShift, $namaShift) = each($arrShift)) {
					$current = $no == 1 ? "class=\"current\"" : "";
					$text.="<li id=\"tab-".$idShift."\" ".$current."><a href=\"#".$idShift."\">$namaShift</a></li>";
					$no++;
				}
			}
			$text.="</ul>";
			
			$no=1;
			if (is_array($arrShift)) {
				reset($arrShift);
				while (list($idShift, $namaShift) = each($arrShift)) {
					$display = $no == 1 ? "" : "style=\"display:none\"";

					$cols = $idShift > 0 ? 6 : 4;
					$text.= table($cols, array(), "lst", "true", "", "dataList".$idShift, "&par[idShift]=".$idShift);
					$text.="<div id=\"".$idShift."\" class=\"subcontent\" ".$display.">
						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList".$idShift."\">
						<thead>
							<tr>
								<th style=\"min-width:20px; max-width:20px;\">No.</th>					
								<th style=\"width:350px;\">Nama</th>					
								<th style=\"min-width:70px; max-width:70px;\">Tanggal</th>";
						if($idShift > 0)
						$text.="<th style=\"min-width:70px;  max-width:70px;\">Datang</th>
								<th style=\"min-width:70px; max-width:70px;\">Pulang</th>";
						$text.="<th style=\"min-width:75px;\">Keterangan</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					</div>";
					$no++;
				}
			}				
			$text.="</div>
			</div>
			<script type=\"text/javascript\">
				jQuery(document).ready(function(){
					jQuery(\"#bSearch\").chained(\"#pSearch\");
				    jQuery(\"#bSearch\").trigger(\"chosen:updated\");

				    jQuery(\"#pSearch\").bind(\"change\", function () {
				      jQuery(\"#bSearch\").trigger(\"chosen:updated\");
				    });

				    jQuery(\"#tSearch\").chained(\"#bSearch\");
				    jQuery(\"#tSearch\").trigger(\"chosen:updated\");

				    jQuery(\"#bSearch\").bind(\"change\", function () {
				      jQuery(\"#tSearch\").trigger(\"chosen:updated\");
				    });

				    jQuery(\"#mSearch\").chained(\"#tSearch\");
				    jQuery(\"#mSearch\").trigger(\"chosen:updated\");

				    jQuery(\"#tSearch\").bind(\"change\", function () {
				      jQuery(\"#mSearch\").trigger(\"chosen:updated\");
				    });
				});
			</script>";
		return $text;
	}		
	
	function lData(){
		global $s,$par,$menuAccess,$arrParameter;	
		if(empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$sWhere= " where t1.status='".$status."' and t2.tanggalJadwal='".setTanggal($par[tanggalAbsen])."' and t2.idShift='".$par[idShift]."'";
		if (!empty($_GET['sSearch']))
			$sWhere.= " and (				
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'
			)";
		
		if (!empty($_GET['pSearch'])) $sWhere.= " and t1.dir_id='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $sWhere.= " and t1.div_id='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $sWhere.= " and t1.dept_id='".$_GET['tSearch']."'";
		if (!empty($_GET['mSearch'])) $sWhere.= " and t1.unit_id='".$_GET['mSearch']."'";
		if(!empty($par[statusPegawai]))
			$sWhere.= " and t1.cat='".$par[statusPegawai]."'";
		
		
		$lWhere= " where l1.status='".$status."' and l2.idShift='".$par[idShift]."' and l2.persetujuanLembur!='f' and l2.sdmLembur!='f' and '".setTanggal($par[tanggalAbsen])."' between date(l2.mulaiLembur) and date(l2.selesaiLembur)";
		if (!empty($_GET['sSearch']))
			$lWhere.= " and (				
				lower(l1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'
			)";
		
		if (!empty($_GET['pSearch'])) $lWhere.= " and l2.idDivisi='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $lWhere.= " and l2.idGedung='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $lWhere.= " and l2.idGroup='".$_GET['tSearch']."'";
		if (!empty($_GET['mSearch'])) $lWhere.= " and l2.idPos='".$_GET['mSearch']."'";
		
		$arrOrder = array(	
			"name",
			"name",
			"tanggalData",
			"time(mulaiData)",
			"time(selesaiData)",			
			"keteranganData",	
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];		
		
		$sql="select * from(
			select y.id,y.reg_no, y.name, y.idLembur as idData, date(y.mulaiLembur) as tanggalData, y.mulaiLembur as mulaiData, y.selesaiLembur as selesaiData, y.keteranganLembur as keteranganData, 'lembur' as tipeData from (
				select * from dta_pegawai l1 join att_lembur l2 on (l1.id = l2.idPegawai) $lWhere
			) as y
			union
			select x.id, x.reg_no, x.name, x.idAbsen as idData, x.tanggalJadwal as tanggalData, x.mulaiAbsen as mulaiData, x.selesaiAbsen as selesaiData, x.keteranganAbsen as keteranganData, 'absen' as tipeData from (
				select n1.*, n2.idAbsen, n2.mulaiAbsen, n2.selesaiAbsen, n2.keteranganAbsen from (
					select * from dta_pegawai t1 join dta_jadwal t2 on (t1.id = t2.idPegawai) $sWhere
				) as n1 left join mnt_absen n2 on (n1.idPegawai=n2.idPegawai and n1.tanggalJadwal=date(n2.mulaiAbsen))
			) as x			
		) as z group by z.id order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from(			
			select y.id, y.reg_no, y.name, date(y.mulaiLembur) as tanggalData, y.mulaiLembur as mulaiData, y.selesaiLembur as selesaiData, y.keteranganLembur as keteranganData from (
				select * from dta_pegawai l1 join att_lembur l2 on (l1.id = l2.idPegawai) $lWhere
			) as y
			union
			select x.id, x.reg_no, x.name, x.tanggalJadwal as tanggalData, x.mulaiAbsen as mulaiData, x.selesaiAbsen as selesaiData, x.keteranganAbsen as keteranganData from (
				select n1.*, n2.mulaiAbsen, n2.selesaiAbsen, n2.keteranganAbsen from (
					select * from dta_pegawai t1 join dta_jadwal t2 on (t1.id = t2.idPegawai) $sWhere
				) as n1 left join mnt_absen n2 on (n1.idPegawai=n2.idPegawai and n1.tanggalJadwal=date(n2.mulaiAbsen))
			) as x
		) as z group by z.id"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			list($r[masukTanggal], $r[masukData]) = explode(" ", $r[mulaiData]);
			list($r[pulangTanggal], $r[pulangData]) = explode(" ", $r[selesaiData]);
			
			$data=$par[idShift] > 0?
			array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[name]."</div>",
				"<div align=\"center\">".getTanggal($r[tanggalData])."</div>",
				"<div align=\"center\">".substr($r[masukData],0,5)."</div>",
				"<div align=\"center\">".substr($r[pulangData],0,5)."</div>",				
				"<div align=\"left\"><a href=\"?par[mode]=$r[tipeData]&par[idData]=$r[idData]&par[idPegawai]=$r[id]&par[tanggalAbsen]=".getTanggal($r[tanggalData])."&par[keteranganAbsen]=$r[keteranganData]".getPar($par,"mode,tanggalAbsen,keteranganAbsen")."\" title=\"Detail Data\" class=\"detil\">".$r[keteranganData]."</a></div>",
			):
			array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[name]."</div>",
				"<div align=\"center\">".getTanggal($r[tanggalData])."</div>",				
				"<div align=\"left\"><a href=\"?par[mode]=$r[tipeData]&par[idData]=$r[idData]&par[idPegawai]=$r[id]&par[tanggalAbsen]=".getTanggal($r[tanggalData])."&par[keteranganAbsen]=$r[keteranganData]".getPar($par,"mode,tanggalAbsen,keteranganAbsen")."\" title=\"Detail Data\" class=\"detil\">".$r[keteranganData]."</a></div>",
			);		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){						
			case "absen":
				$text = absen();
			break;
			case "lembur":
				$text = lembur();
			break;
			case "lst":
				$text=lData();
			break;			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
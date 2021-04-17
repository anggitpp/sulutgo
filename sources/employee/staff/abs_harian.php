<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/sakit/";

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
	$par[idPegawai] = $cID;
	if(empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');		

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				<input type=\"text\" id=\"tanggalAbsen\" name=\"par[tanggalAbsen]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAbsen]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>
						<td>".comboArray("par[search]", array("All", "Nama", "NIK"), $par[search])."</td>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>			
			<br clear=\"all\" />			
		</form>
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:150px;\">Nama</th>
					<th rowspan=\"2\" width=\"100\">NIK</th>
					<th colspan=\"2\" style=\"width:80px;\">Jadwal</th>					
					<th colspan=\"2\" style=\"width:80px;\">Aktual</th>
					<th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
					<th rowspan=\"2\" width=\"85\">Keterangan</th>
					<th rowspan=\"2\" width=\"50\">Detail</th>
				</tr>
				<tr>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
				</tr>
			</thead>
			<tbody>";


				$arrNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
				$arrShift=arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
				$arrJadwal=arrayQuery("select idPegawai, concat(mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where tanggalJadwal='".setTanggal($par[tanggalAbsen])."'");

				$filter = "where '".setTanggal($par[tanggalAbsen])."' between date(t1.mulaiAbsen) and date(t1.selesaiAbsen)";
				if(!isset($menuAccess[$s]["apprlv2"]))
				    $filter.= "and (t2.leader_id='".$par[idPegawai]."' or t2.administration_id='".$par[idPegawai]."')";
				
				if($par[search] == "Nama")
					$filter.= " and lower(t2.name) like '%".strtolower($par[filter])."%'";
				else if($par[search] == "NIK")
					$filter.= " and lower(t2.reg_no) like '%".strtolower($par[filter])."%'";
				else
					$filter.= " and (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				)";

				$filter .= " AND t2.location IN ($areaCheck)";
				$sql="select * from dta_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t2.name";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
					explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;

					list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
					list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

					if(isset($arrJadwal["$r[idPegawai]"]))
						list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"]);

					$arr["$r[idPegawai]"]=$r;
				}

				if(is_array($arr)){				
					reset($arr);		
					while(list($idPegawai, $r)=each($arr)){
						$no++;			

						if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
						if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

						$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>
						<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
						<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
						<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
						<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
						<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
						<td>$r[keteranganAbsen]</td>
						<td align=\"center\"><a href=\"?par[mode]=det&par[idPegawai]=$r[idPegawai]&par[keteranganAbsen]=$r[keteranganAbsen]".getPar($par,"mode,idPegawai,keteranganAbsen")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>
					</tr>";			
				}
			}

			$text.="</tbody>
		</table>
	</div>";
	return $text;
}		

function detail(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
	$_SESSION["curr_emp_id"] = $par[idPegawai];
	echo "<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
	".getBread(ucwords($par[mode]." data"))."								
</div>
<div style=\"padding:10px;\">";

	require_once "tmpl/__emp_header__.php";

	$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and '".setTanggal($par[tanggalAbsen])."' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$dtaNormal=getField("select concat(namaShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift))='normal'");
	$dtaShift=getField("select concat(t2.namaShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]'");
	$dtaJadwal=getField("select concat(t2.namaShift, ',\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]' and tanggalJadwal='".setTanggal($par[tanggalAbsen])."'");

	list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t", $dtaShift);
	if(!empty($dtaJadwal)) list($r[namaShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);

	list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
	list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

	if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
	if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";

	$text.="</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" class=\"stdform\">	
			<div id=\"general\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>DATA ABSENSI</h3></div>
				</div>				
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<span class=\"field\">".getTanggal(setTanggal($par[tanggalAbsen]), "t")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Jadwal Kerja</label>
					<span class=\"field\">$r[namaShift] ".substr($r[mulaiShift],0,5)." - ".substr($r[selesaiShift],0,5)."&nbsp;</span>
				</p>";

				list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
				$nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn." / ".$pulangAbsen_sn;

				$arrMode = array(
					"Izin Cuti" => "detCuti",
					"Izin Dinas" => "detDinas",
					"Izin Sementara" => "detSementara",
					"Izin Pelatihan" => "detPelatihan",
					"Izin Sakit" => "detSakit",
					);

				if(empty($r[keteranganAbsen]))
					$text.="<p>
				<label class=\"l-input-small\">Aktual</label>
				<span class=\"field\">".substr($r[masukAbsen],0,5)." - ".substr($r[pulangAbsen],0,5)."&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">SN Mesin</label>
				<span class=\"field\">".$nomorAbsen."&nbsp;</span>
			</p>";
			else
				$text.="<p>
			<label class=\"l-input-small\">Keterangan</label>
			<span class=\"field\">$r[keteranganAbsen]&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor</label>
			<span class=\"field\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=".$arrMode["$r[keteranganAbsen]"]."&par[id]=$r[idAbsen]".getPar($par,"mode,idMesin")."',1000,550);\">$r[nomorAbsen]</a>&nbsp;</span>
		</p>";
		$text.="</div>
		<p>					
			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\" />
		</p>
	</form>";
	return $text;
}

function detailSementara(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from att_izin where idIzin='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	if(empty($r[nomorIzin])) $r[nomorIzin] = gNomor();
	if(empty($r[tanggalIzin])) $r[tanggalIzin] = date('Y-m-d');
	list($mulaiIzin_tanggal, $mulaiIzin) = explode(" ", $r[mulaiIzin]);
	list($selesaiIzin_tanggal, $selesaiIzin) = explode(" ", $r[selesaiIzin]);

	$sql_="select
	id as idPegawai,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[idPegawai]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Izin Sementara</h1>
		".getBread()."								
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorIzin]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">NIK</label>
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
								<span class=\"field\">".getTanggal($r[tanggalIzin],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Divisi</label>
								<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SEMENTARA</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Tanggal</label>
								<span class=\"field\">".getTanggal($mulaiIzin_tanggal,"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Waktu</label>
								<span class=\"field\">".substr($mulaiIzin,0,5)." <strong>s.d</strong> ".substr($selesaiIzin,0,5)."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[keteranganIzin])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">";

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

						$persetujuanIzin = $r[persetujuanIzin] == "t"? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
						$persetujuanIzin = $r[persetujuanIzin] == "f"? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanIzin;

						list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
						$approveTime = getTanggal($approveTanggal)." ".substr($approveWaktu,0,5);
						$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;

						$text.="<p>
						<label class=\"l-input-small\">Atasan</label>
						<span class=\"field\">".$r_[nikAtasan]." - ".$r_[namaAtasan]."&nbsp;</span>
					</p>						
				</td>
			</tr>
		</table>
		<div style=\"float:right; margin-top:-30px;\">
			<table width=\"100%\">
				<tr>
					<td>Tahun ini sudah pernah melakukan izin : <strong>".getField("select count(*) from att_izin where idPegawai='$r[idPegawai]' and persetujuanIzin='t'")." Kali</strong></td>
					<td>
						<table>
							<tr>
								<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
								<td>".$persetujuanIzin." ".$approveTime."</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>";

		$persetujuanIzin = "Belum Diproses";
		$persetujuanIzin = $r[persetujuanIzin] == "t" ? "Disetujui" : $persetujuanIzin;
		$persetujuanIzin = $r[persetujuanIzin] == "f" ? "Ditolak" : $persetujuanIzin;		

		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
	</div>					
	<table width=\"100%\">
		<tr>
			<td width=\"45%\">
				<p>
					<label class=\"l-input-small\">Status</label>
					<span class=\"field\">".$persetujuanIzin."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\">".nl2br($r[catatanIzin])."&nbsp;</span>
				</p>
			</td>
			<td width=\"55%\">&nbsp;</td>
		</tr>
	</table>					
</div>
</form>
</div>";
return $text;
}

function detailCuti(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from att_cuti where idCuti='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	$sql_="select
	id as idPegawai,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[idPegawai]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Izin Cuti</h1>
		".getBread()."
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorCuti]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">NIK</label>
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
								<span class=\"field\">".getTanggal($r[tanggalCuti],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Divisi</label>
								<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Mulai</label>
								<span class=\"field\">".getTanggal($r[mulaiCuti],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Selesai</label>
								<span class=\"field\">".getTanggal($r[selesaiCuti],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[keteranganCuti])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">";

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
				</td>
			</tr>
		</table>
		<div style=\"float:right; margin-right:20px; margin-top:-40px;\">
			Jatah Cuti  Tahun ini : <strong>12 Hari</strong>, Jatah <strong>".getAngka($r[jatahCuti])." Hari</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Diambil <strong>".getAngka($r[jumlahCuti])." Hari</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Sisa <strong>".getAngka($r[sisaCuti])." Hari</strong>
		</div>";

		$persetujuanCuti = "Belum Diproses";
		$persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
		$persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;	

		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
	</div>			
	<table width=\"100%\">
		<tr>
			<td width=\"45%\">
				<p>
					<label class=\"l-input-small\">Status</label>
					<span class=\"field\">".$persetujuanCuti."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\">".nl2br($r[catatanCuti])."&nbsp;</span>
				</p>
			</td>
			<td width=\"55%\">&nbsp;</td>
		</tr>
	</table>";

	$sdmCuti = "Belum Diproses";
	$sdmCuti = $r[sdmCuti] == "t" ? "Disetujui" : $sdmCuti;
	$sdmCuti = $r[sdmCuti] == "f" ? "Ditolak" : $sdmCuti;		

	$text.="<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
</div>			
<table width=\"100%\">
	<tr>
		<td width=\"45%\">
			<p>
				<label class=\"l-input-small\">Status</label>
				<span class=\"field\">".$sdmCuti."&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<span class=\"field\">".nl2br($r[noteCuti])."&nbsp;</span>
			</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
	</tr>
</table>";

$text.="</div>
</form>
</div>";
return $text;
}	

function detailDinas(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from att_dinas where idDinas='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	$sql_="select
	id as idPegawai,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[idPegawai]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Izin Dinas</h1>
		".getBread()."								
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorDinas]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">NIK</label>
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
								<span class=\"field\">".getTanggal($r[tanggalDinas],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Divisi</label>
								<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN DINAS</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Mulai</label>
								<span class=\"field\">".getTanggal($r[mulaiDinas],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Selesai</label>
								<span class=\"field\">".getTanggal($r[selesaiDinas],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[keteranganDinas])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">";

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
				</td>
			</tr>
		</table>";

		$persetujuanDinas = "Belum Diproses";
		$persetujuanDinas = $r[persetujuanDinas] == "t" ? "Disetujui" : $persetujuanDinas;
		$persetujuanDinas = $r[persetujuanDinas] == "f" ? "Ditolak" : $persetujuanDinas;	
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
	</div>			
	<table width=\"100%\">
		<tr>
			<td width=\"45%\">
				<p>
					<label class=\"l-input-small\">Status</label>
					<span class=\"field\">".$persetujuanDinas."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\">".nl2br($r[catatanDinas])."&nbsp;</span>
				</p>
			</td>
			<td width=\"55%\">&nbsp;</td>
		</tr>
	</table>";


	$sdmDinas = "Belum Diproses";
	$sdmDinas = $r[sdmDinas] == "t" ? "Disetujui" : $sdmDinas;
	$sdmDinas = $r[sdmDinas] == "f" ? "Ditolak" : $sdmDinas;
	$text.="<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
</div>			
<table width=\"100%\">
	<tr>
		<td width=\"45%\">
			<p>
				<label class=\"l-input-small\">Status</label>
				<span class=\"field\">".$sdmDinas."&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<span class=\"field\">".nl2br($r[noteDinas])."&nbsp;</span>
			</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
	</tr>
</table>";

$text.="</div>
</form>
</div>";
return $text;
}

function detailPelatihan(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from att_pelatihan where idPelatihan='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);							

	$sql_="select
	id as idPegawai,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[idPegawai]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Izin Pelatihan</h1>
		".getBread()."								
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorPelatihan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">NIK</label>
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
								<span class=\"field\">".getTanggal($r[tanggalPelatihan],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Divisi</label>
								<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN DINAS</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Mulai</label>
								<span class=\"field\">".getTanggal($r[mulaiPelatihan],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Selesai</label>
								<span class=\"field\">".getTanggal($r[selesaiPelatihan],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[keteranganPelatihan])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">";

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
				</td>
			</tr>
		</table>";

		$persetujuanPelatihan = "Belum Diproses";
		$persetujuanPelatihan = $r[persetujuanPelatihan] == "t" ? "Disetujui" : $persetujuanPelatihan;
		$persetujuanPelatihan = $r[persetujuanPelatihan] == "f" ? "Ditolak" : $persetujuanPelatihan;	
		$text.="<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
	</div>			
	<table width=\"100%\">
		<tr>
			<td width=\"45%\">
				<p>
					<label class=\"l-input-small\">Status</label>
					<span class=\"field\">".$persetujuanPelatihan."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\">".nl2br($r[catatanPelatihan])."&nbsp;</span>
				</p>
			</td>
			<td width=\"55%\">&nbsp;</td>
		</tr>
	</table>";


	$sdmPelatihan = "Belum Diproses";
	$sdmPelatihan = $r[sdmPelatihan] == "t" ? "Disetujui" : $sdmPelatihan;
	$sdmPelatihan = $r[sdmPelatihan] == "f" ? "Ditolak" : $sdmPelatihan;
	$text.="<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
</div>			
<table width=\"100%\">
	<tr>
		<td width=\"45%\">
			<p>
				<label class=\"l-input-small\">Status</label>
				<span class=\"field\">".$sdmPelatihan."&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<span class=\"field\">".nl2br($r[notePelatihan])."&nbsp;</span>
			</p>
		</td>
		<td width=\"55%\">&nbsp;</td>
	</tr>
</table>";

$text.="</div>
</form>
</div>";
return $text;
}

function detailSakit(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

	$sql="select * from att_sakit where idSakit='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);				

	$sql_="select
	id as idPegawai,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[idPegawai]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Izin Sakit</h1>
		".getBread()."
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" class=\"stdform\">	
			<div id=\"general\" style=\"margin-top:20px;\">
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Nomor</label>
								<span class=\"field\">".$r[nomorSakit]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">NIK</label>
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
								<span class=\"field\">".getTanggal($r[tanggalSakit],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Jabatan</label>
								<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Divisi</label>
								<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SAKIT</h3></div>
				</div>
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Mulai</label>
								<span class=\"field\">".getTanggal($r[mulaiSakit],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Selesai</label>
								<span class=\"field\">".getTanggal($r[selesaiSakit],"t")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[keteranganSakit])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">
							<p>
								<label class=\"l-input-small\">Perawatan</label>
								<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[idPerawatan]'")."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Surat Dokter</label>
								<span class=\"field\">";
									$text.=empty($r[fileSakit])? "":
									"<a href=\"download.php?d=sakit&f=$r[idSakit]\"><img src=\"".getIcon($fFile."/".$r[fileSakit])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
									$text.="&nbsp;</span>
								</p>
							</td>
						</tr>
					</table>";

					$persetujuanSakit = "Belum Diproses";
					$persetujuanSakit = $r[persetujuanSakit] == "t" ? "Disetujui" : $persetujuanSakit;
					$persetujuanSakit = $r[persetujuanSakit] == "f" ? "Ditolak" : $persetujuanSakit;		

					$text.="<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
				</div>			
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Status</label>
								<span class=\"field\">".$persetujuanSakit."&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<span class=\"field\">".nl2br($r[catatanSakit])."&nbsp;</span>
							</p>
						</td>
						<td width=\"55%\">&nbsp;</td>
					</tr>
				</table>";

				$sdmSakit = "Belum Diproses";
				$sdmSakit = $r[sdmSakit] == "t" ? "Disetujui" : $sdmSakit;
				$sdmSakit = $r[sdmSakit] == "f" ? "Ditolak" : $sdmSakit;
				$text.="<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
			</div>			
			<table width=\"100%\">
				<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmSakit."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[noteSakit])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
				</tr>
			</table>";

			$text.="</div>
		</form>
	</div>";
	return $text;
}

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){	
		case "detCuti":
		$text = detailCuti();
		break;
		case "detDinas":
		$text = detailDinas();
		break;
		case "detSementara":
		$text = detailSementara();
		break;
		case "detPelatihan":
		$text = detailPelatihan();
		break;
		case "detSakit":
		$text = detailSakit();
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
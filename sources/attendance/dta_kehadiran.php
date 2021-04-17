<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();		
		
		$idAbsen = getField("select idAbsen from att_absen where idPegawai='$par[idPegawai]' and mulaiAbsen='".setTanggal($par[mulaiAbsen])."'");
		
		if($idAbsen > 0){
			$sql="update att_absen set mulaiAbsen_masuk='".setTanggal($inp[mulaiAbsen_masuk])."', masukAbsen='".$inp[masukAbsen]."', mulaiAbsen_pulang='".setTanggal($inp[mulaiAbsen_pulang])."', pulangAbsen='".$inp[pulangAbsen]."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idAbsen='$idAbsen'";			
		}else{
			$idAbsen = getField("select idAbsen from att_absen order by idAbsen desc limit 1") + 1;
			$sql="insert into att_absen (idAbsen, idPegawai, mulaiAbsen, mulaiAbsen_masuk, mulaiAbsen_pulang, masukAbsen, pulangAbsen, createBy, createTime) values ('$idAbsen', '$par[idPegawai]', '".setTanggal($par[mulaiAbsen])."', '".setTanggal($inp[mulaiAbsen_masuk])."', '".setTanggal($inp[mulaiAbsen_pulang])."', '".$inp[masukAbsen]."', '".$inp[pulangAbsen]."', '$cUsername', '".date("Y-m-d H:i:s")."')";
		}
		db($sql);		
		db("update att_absen set durasiAbsen=TIMEDIFF(concat(mulaiAbsen_pulang,' ',pulangAbsen), concat(mulaiAbsen_masuk,' ',masukAbsen)) where idAbsen='$idAbsen'");
		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,mulaiAbsen")."';</script>";
	}
	
	function data(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
				
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and '".setTanggal($par[mulaiAbsen])."' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$dtaNormal=getField("select concat(idShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('ho', 'shift 1')");
		$dtaShift=getField("select concat(t2.idShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$par[idPegawai]'");
		$dtaJadwal=getField("select concat(idShift, ',\t', mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where idPegawai='$par[idPegawai]' and tanggalJadwal='".setTanggal($par[mulaiAbsen])."'");
		
		list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t", $dtaShift);
		if(!empty($dtaJadwal)) list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);
		
		list($r[mulaiAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
		list($r[mulaiAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);
		
		if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
		if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
		
		$namaShift = $r[idShift] == 0 ? "OFF" : getField("select namaShift from dta_shift where idShift='$r[idShift]'");
		
		$text.="</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" class=\"stdform\">	
				<div id=\"general\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>DATA ABSENSI</h3></div>
					</div>				
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal(setTanggal($par[mulaiAbsen]), "t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Jadwal Kerja</label>
						<span class=\"field\">".substr($r[mulaiShift],0,5)." s.d ".substr($r[selesaiShift],0,5)."&nbsp;, $namaShift</span>
					</p>";
			
			list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
			$nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn." / ".$pulangAbsen_sn;						
			
			if(empty($r[keteranganAbsen]))
			$text.="<p>
						<label class=\"l-input-small\">Aktual</label>
						<span class=\"field\">".substr($r[masukAbsen],0,5)." s.d ".substr($r[pulangAbsen],0,5)."&nbsp;</span>
					</p>";
			else
			$text.="<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">$r[keteranganAbsen]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nomor</label>
						<span class=\"field\">$r[nomorAbsen]&nbsp;</span>
					</p>";
			$text.="</div>
				<div style=\"position:absolute; top:10px; right:20px;\">
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode,mulaiAbsen,keteranganAbsen")."';\" style=\"float:right;\" />
				</div>
				</form>";
		return $text;
	}
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
				
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and '".setTanggal($par[mulaiAbsen])."' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$dtaNormal=getField("select concat(idShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('ho', 'shift 1')");
		$dtaShift=getField("select concat(t2.idShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$par[idPegawai]'");
		$dtaJadwal=getField("select concat(idShift, ',\t', mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where idPegawai='$par[idPegawai]' and tanggalJadwal='".setTanggal($par[mulaiAbsen])."'");
		
		list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t", $dtaShift);
		if(!empty($dtaJadwal)) list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);
		
		list($r[mulaiAbsen_masuk], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
		list($r[mulaiAbsen_pulang], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);
		
		if($r[mulaiAbsen_masuk] == "0000-00-00" || empty($r[mulaiAbsen_masuk])) $r[mulaiAbsen_masuk] = setTanggal($par[mulaiAbsen]);
		if($r[mulaiAbsen_pulang] == "0000-00-00" || empty($r[mulaiAbsen_pulang])) $r[mulaiAbsen_pulang] = setTanggal($par[mulaiAbsen]);
		
		if(empty($r[masukAbsen])) $r[masukAbsen] = "00:00:00";
		if(empty($r[pulangAbsen])) $r[pulangAbsen] = "00:00:00";
		
		$namaShift = $r[idShift] == 0 ? "OFF" : getField("select namaShift from dta_shift where idShift='$r[idShift]'");
		
		setValidation("is_null","inp[keteranganShift]","anda harus mengisi keterangan");		
		$text = getValidation();
		
		$text.="</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" >	
				<div id=\"general\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>DATA ABSENSI</h3></div>
					</div>				
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<span class=\"field\">".getTanggal(setTanggal($par[mulaiAbsen]), "t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Jadwal Kerja</label>
						<span class=\"field\">".substr($r[mulaiShift],0,5)." s.d ".substr($r[selesaiShift],0,5)."&nbsp;, $namaShift</span>
					</p>";
			
			list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
			$nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn." / ".$pulangAbsen_sn;						
			
			if(empty($r[keteranganAbsen]))
			$text.="<p>
						<label class=\"l-input-small\">Absen Masuk</label>
						<div class=\"field\">
							<input type=\"text\" id=\"mulaiAbsen_masuk\" name=\"inp[mulaiAbsen_masuk]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiAbsen_masuk])."\" class=\"vsmallinput hasDatePicker\"/> 
							<input type=\"text\" id=\"masukAbsen\" name=\"inp[masukAbsen]\" size=\"10\" maxlength=\"5\" value=\"".substr($r[masukAbsen],0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Absen Pulang</label>
						<div class=\"field\">			
							<input type=\"text\" id=\"mulaiAbsen_pulang\" name=\"inp[mulaiAbsen_pulang]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiAbsen_pulang])."\" class=\"vsmallinput hasDatePicker\"/> 
							<input type=\"text\" id=\"pulangAbsen\" name=\"inp[pulangAbsen]\" size=\"10\" maxlength=\"5\" value=\"".substr($r[pulangAbsen],0,5)."\" class=\"vsmallinput hasTimePicker\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</div>
					</p>";
			else
			$text.="<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">$r[keteranganAbsen]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Nomor</label>
						<span class=\"field\">$r[nomorAbsen]&nbsp;</span>
					</p>";
			$text.="</div>
				<div style=\"position:absolute; top:10px; right:20px;\">
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode,mulaiAbsen,keteranganAbsen")."';\" style=\"float:right;\" />
				</div>
				</form>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));						
		$day = $day < 10 ? "0".$day : $day;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		if(empty($par[tanggalAwal])) $par[tanggalAwal] = date('01/'.$par[bulanAbsen].'/'.$par[tahunAbsen]);
		if(empty($par[tanggalAkhir])) $par[tanggalAkhir] = date($day.'/'.$par[bulanAbsen].'/'.$par[tahunAbsen]);	
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>								
				<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\" style=\"padding:0 20px;\">
					<div style=\"float:left;\">
						Pegawai : ".comboData("select id, upper(name) as name from emp where status='$status' order by name","id","name","par[idPegawai]","",$par[idPegawai],"onchange=\"document.getElementById('form').submit();\"", "300px;", "chosen-select")."
					</div>
					<div style=\"float:right;\">
						Periode : 
						<input type=\"text\" id=\"tanggalAwal\" name=\"par[tanggalAwal]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAwal]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" /> <strong>s.d</strong> <input type=\"text\" id=\"tanggalAkhir\" name=\"par[tanggalAkhir]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalAkhir]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />						
						<input type=\"hidden\" id=\"par[idLokasi]\" name=\"par[idLokasi]\" value=\"".$par[idLokasi]."\" >
						<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"".$par[mode]."\" >
						<input type=\"hidden\" id=\"par[bulanAbsen]\" name=\"par[bulanAbsen]\" value=\"".$par[bulanAbsen]."\" >
						<input type=\"hidden\" id=\"par[tahunAbsen]\" name=\"par[tahunAbsen]\" value=\"".$par[tahunAbsen]."\" >
					</div>
					<br clear=\"all\">
				</form>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
						
		$text.="</div>
			<div class=\"contentwrapper\">				
			<div id=\"general\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\" style=\"margin-top:30px;\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:150px;\">Tanggal</th>					
					<th colspan=\"3\" style=\"width:80px;\">Jadwal</th>					
					<th colspan=\"2\" style=\"width:80px;\">Aktual</th>
					<th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
					<th rowspan=\"2\" style=\"width:80px;\">Keterangan</th>					
				</tr>
				<tr>
					<th style=\"width:40px;\">Shift</th>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
					<th style=\"width:40px;\">Masuk</th>
					<th style=\"width:40px;\">Pulang</th>
				</tr>
			</thead>
			<tbody>";
		
		$arrKode=arrayQuery("select idShift, kodeShift from dta_shift order by idShift");
		$arrNormal=getField("select concat(idShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho', 'shift 1')");
		$arrShift=arrayQuery("select t1.idPegawai, concat(t2.idShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where idPegawai='$par[idPegawai]'");
		$arrJadwal=arrayQuery("select tanggalJadwal, concat(idShift, '\t', mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where idPegawai='$par[idPegawai]' and tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");
		
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			list($mulaiAbsen, $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
			list($selesaiAbsen, $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);			
			while($mulaiAbsen <= $selesaiAbsen){				
				list($Y,$m,$d) = explode("-", $mulaiAbsen);
				
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
				explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;
				
				if(isset($arrJadwal[$mulaiAbsen]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$mulaiAbsen]);
								
				$arr[$mulaiAbsen]=$r;
				
				$mulaiAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}
			
		
		$mulaiAbsen = setTanggal($par[tanggalAwal]);
		$tanggalAkhir = setTanggal($par[tanggalAkhir]);
		while($mulaiAbsen <= $tanggalAkhir){
			$no++;						
			$r=$arr[$mulaiAbsen];					
			
			$week = date("w", strtotime($mulaiAbsen));
			$color = (in_array($week, array(0,6)) || getField("select idLibur from dta_libur where '".$par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)."' between mulaiLibur and selesaiLibur and statusLibur='t'")) ? "style=\"background:#f2dbdb;\"" : "";
			
			if(empty($r[mulaiShift]) && empty($r[selesaiShift]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$mulaiAbsen]);	
			
			if(empty($r[mulaiShift]) && empty($r[selesaiShift]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);
			
			
			if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
			if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";					
			
			$kodeShift = $r[idShift] == 0 ? "OFF" : $arrKode["".$r[idShift].""];
			$r[keteranganAbsen] = strtolower($r[keteranganAbsen]) == "izin tidak masuk kerja" ? $r[keteranganAbsen]." : ".getField("select t2.namaData from att_hadir t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idHadir='".$r[idAbsen]."'") : $r[keteranganAbsen];
			$text.="<tr ".$color.">
					<td>$no.</td>						
					<td><a href=\"?par[mode]=dta&par[mulaiAbsen]=".getTanggal($mulaiAbsen)."&par[keteranganAbsen]=$r[keteranganAbsen]".getPar($par,"mode,mulaiAbsen,keteranganAbsen")."\" title=\"Detail Data\" class=\"detil\">".getTanggal($mulaiAbsen, "t")."</a></td>
					<td align=\"center\">".$kodeShift."</td>
					<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
					<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
					<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
					<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
					<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
					<td>$r[keteranganAbsen]</td>						
					</tr>";	
			
			list($Y,$m,$d) = explode("-",$mulaiAbsen);
			$mulaiAbsen = date("Y-m-d", dateAdd("d",1,mktime(0,0,0,$m,$d,$Y)));
		}
		
		$text.="</tbody>
			</table>	
			<div style=\"position:absolute; top:10px; right:20px;\">
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\" />
			</div>
			<p>					
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\" />
			</p>
		</div>
		</div>";
		
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]." - ".getField("select reg_no from emp where id='".$par[idPegawai]."'"))).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}
		
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$areaCheck;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');										
		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));						
		$cutil = new Common();
		$cols = 5+$day;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		for($i=4; $i<=5+$day; $i++){
			$arrNot[] = $i;
		}
		$text = table($cols, $arrNot, "lst", "true", "h");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
			".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[statusPegawai]","All",$par[statusPegawai],"onchange=\"document.getElementById('form').submit();\"", "110px")."
			<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
			".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."
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
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:350px;\">Nama</th>
					<th style=\"min-width:100px;\">NPP</th>";					
				for($i=1; $i<=$day; $i++)
					$text.="<th style=\"min-width:40px;\">".$i."</th>";
				$text.="<th style=\"min-width:40px;\">Total</th>
					<th style=\"min-width:50px;\">Detail</th>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
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
		global $s,$par,$menuAccess,$arrParameter,$areaCheck;		
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));
		
		$sql="select * from dta_absen where month(mulaiAbsen)='".$par[bulanAbsen]."' and year(mulaiAbsen)='".$par[tahunAbsen]."'";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($tahunAbsen, $bulanAbsen, $mulaiAbsen) = explode("-", $r[mulaiAbsen]);
			$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
			$arrAbsen["$r[idPegawai]"][intval($mulaiAbsen)] = str_replace("-","",$r[durasiAbsen]);
		}
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$sWhere = "where t1.id is not null and t1.status='".$status."' and t2.location in ($areaCheck)";
		
		if(!empty($par[idLokasi]))
			$sWhere.= " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[statusPegawai]))
			$sWhere.= " and t1.cat='".$par[statusPegawai]."'";
		
		if(!empty($_GET['sSearch']))
			$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'";
		
		if (!empty($_GET['pSearch'])) $sWhere.= " and t2.dir_id='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $sWhere.= " and t2.div_id='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $sWhere.= " and t2.dept_id='".$_GET['tSearch']."'";
		if (!empty($_GET['mSearch'])) $sWhere.= " and t2.unit_id='".$_GET['mSearch']."'";
					
		$arrOrder = array(	
			"t1.name",
			"t1.name",
			"t1.reg_no",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select t1.* from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			if(empty($r[namaData])) $r[namaData] = "ALL";
			$statusShift=$r[statusShift] == "t" ?
					"<img src=\"styles/images/t.png\" title=\"Active\">":
					"<img src=\"styles/images/f.png\" title=\"Not Active\">";			
			
			$controlAbsen="<a href=\"?par[mode]=det&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";			
			
			$data=array();
			$data[]="<div align=\"center\">".$no.".</div>";
			$data[]="<div align=\"left\">".strtoupper($r[name])."</div>";
			$data[]="<div align=\"left\">".$r[reg_no]."</div>";
			for($i=1; $i<=$day; $i++){
				$week = date("w", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
				$color = (in_array($week, array(0,6)) || getField("select idLibur from dta_libur where '".$par[tahunAbsen]."-".$par[bulanAbsen]."-".str_pad($i, 2, "0", STR_PAD_LEFT)."' between mulaiLibur and selesaiLibur and statusLibur='t'")) ? "style=\"background:#f2dbdb;\"" : "";											
				$data[]="<div align=\"center\" ".$color.">".substr($arrAbsen["$r[id]"][$i],0,5)."&nbsp;</div>";
			}
			$data[]="<div align=\"center\">".substr(sumTime($arrAbsen["$r[id]"]),0,5)."</div>";
			$data[]="<div align=\"center\">".$controlAbsen."</div>";
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function xls(){		
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$fFile;
		require_once 'plugins/PHPExcel.php';
		
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');
		$day = date("t", strtotime($par[tahunAbsen]."-".$par[bulanAbsen]."-01"));						
		$day = $day < 10 ? "0".$day : $day;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		if(empty($par[tanggalAwal])) $par[tanggalAwal] = date('01/'.$par[bulanAbsen].'/'.$par[tahunAbsen]);
		if(empty($par[tanggalAkhir])) $par[tanggalAkhir] = date($day.'/'.$par[bulanAbsen].'/'.$par[tahunAbsen]);	
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
						
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
		->setLastModifiedBy($cNama)
		->setTitle($arrTitle[$s]);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(40);
		
		$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('9')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:I2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:I3');
		$objPHPExcel->getActiveSheet()->mergeCells('A7:I7');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DURASI KEHADIRAN');		
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal(setTanggal($par[tanggalAwal]),"t").' s.d '.getTanggal(setTanggal($par[tanggalAkhir]),"t"));		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:I4');
		$objPHPExcel->getActiveSheet()->mergeCells('C5:I5');
		$objPHPExcel->getActiveSheet()->mergeCells('C6:I6');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('A6', 'JABATAN');
	
		$sql="select * from dta_pegawai where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$objPHPExcel->getActiveSheet()->setCellValue('C4', ': '.$r[name]);
		$objPHPExcel->getActiveSheet()->setCellValue('C5', ': '.$r[reg_no]);
		$objPHPExcel->getActiveSheet()->setCellValue('C6', ': '.$r[pos_name]);
	
		$objPHPExcel->getActiveSheet()->getStyle('A8:I9')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A8:I9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A8:I9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A8:I9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A8:I8')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A9:I9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A8:A9');
		$objPHPExcel->getActiveSheet()->mergeCells('B8:B9');		
		$objPHPExcel->getActiveSheet()->mergeCells('C8:E8');
		$objPHPExcel->getActiveSheet()->mergeCells('F8:G8');
		$objPHPExcel->getActiveSheet()->mergeCells('H8:H9');
		$objPHPExcel->getActiveSheet()->mergeCells('I8:I9');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A8', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B8', 'TANGGAL');		
		$objPHPExcel->getActiveSheet()->setCellValue('C8', 'JADWAL');
		$objPHPExcel->getActiveSheet()->setCellValue('F8', 'AKTUAL');				
		$objPHPExcel->getActiveSheet()->setCellValue('H8', 'DURASI');
		$objPHPExcel->getActiveSheet()->setCellValue('I8', 'KETERANGAN');
		
		$objPHPExcel->getActiveSheet()->setCellValue('C9', 'SHIFT');
		$objPHPExcel->getActiveSheet()->setCellValue('D9', 'MASUK');
		$objPHPExcel->getActiveSheet()->setCellValue('E9', 'PULANG');
		$objPHPExcel->getActiveSheet()->setCellValue('F9', 'MASUK');
		$objPHPExcel->getActiveSheet()->setCellValue('G9', 'PULANG');								
		
		$rows = 10;
		$arrKode=arrayQuery("select idShift, kodeShift from dta_shift order by idShift");
		$arrNormal=getField("select concat(idShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho', 'shift 1')");
		$arrShift=arrayQuery("select t1.idPegawai, concat(t2.idShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where idPegawai='$par[idPegawai]'");
		$arrJadwal=arrayQuery("select tanggalJadwal, concat(idShift, '\t', mulaiJadwal, '\t', selesaiJadwal) from dta_jadwal where idPegawai='$par[idPegawai]' and tanggalJadwal between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'");
		
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and date(mulaiAbsen) between '".setTanggal($par[tanggalAwal])."' and '".setTanggal($par[tanggalAkhir])."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			list($mulaiAbsen, $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
			list($selesaiAbsen, $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);			
			while($mulaiAbsen <= $selesaiAbsen){				
				list($Y,$m,$d) = explode("-", $mulaiAbsen);
				
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
				explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;
				
				if(isset($arrJadwal[$mulaiAbsen]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$mulaiAbsen]);
								
				$arr[$mulaiAbsen]=$r;
				
				$mulaiAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}
			
		
		$mulaiAbsen = setTanggal($par[tanggalAwal]);
		$tanggalAkhir = setTanggal($par[tanggalAkhir]);
		while($mulaiAbsen <= $tanggalAkhir){
			$no++;						
			$r=$arr[$mulaiAbsen];					
						
			if(empty($r[mulaiShift]) && empty($r[selesaiShift]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$mulaiAbsen]);	
			
			if(empty($r[mulaiShift]) && empty($r[selesaiShift]))
				list($r[idShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);
			
			
			if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
			if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";					
			
			$kodeShift = $r[idShift] == 0 ? "OFF" : $arrKode["".$r[idShift].""];
			$r[keteranganAbsen] = strtolower($r[keteranganAbsen]) == "izin tidak masuk kerja" ? $r[keteranganAbsen]." : ".getField("select t2.namaData from att_hadir t1 join mst_data t2 on (t1.idTipe=t2.kodeData) where t1.idHadir='".$r[idAbsen]."'") : $r[keteranganAbsen];
			
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':I'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, getTanggal($mulaiAbsen, "t"));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $kodeShift);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, str_replace("FALSE", "",substr($r[mulaiShift],0,5)));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, str_replace("FALSE", "",substr($r[selesaiShift],0,5)));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, str_replace("FALSE", "",substr($r[masukAbsen],0,5)));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, str_replace("FALSE", "",substr($r[pulangAbsen],0,5)));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, str_replace("FALSE", "",substr(str_replace("-","",$r[durasiAbsen]),0,5)));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $r[keteranganAbsen]);
			
						
			list($Y,$m,$d) = explode("-",$mulaiAbsen);
			$mulaiAbsen = date("Y-m-d", dateAdd("d",1,mktime(0,0,0,$m,$d,$Y)));
			$rows++;							
		}
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A8:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A8:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B8:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C8:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D8:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E8:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F8:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G8:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H8:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('I8:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:I'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A10:I'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s]." - ".getField("select reg_no from emp where id='".$par[idPegawai]."'"))).".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;
			
			case "dta":				
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = data();				
			break;
			case "det":
				$text = detail();
			break;
			case "xls":
				$text = detail();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
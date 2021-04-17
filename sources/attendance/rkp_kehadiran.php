<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	
	function div() {
		global $s, $id, $inp, $par, $arrParameter,$db;
		$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where kodeInduk='$par[kodeP]' order by namaData");
		// var_dump($data); 
		// die;
		return implode("\n", $data);
	}
	function dep() {
		global $s, $id, $inp, $par, $arrParameter,$db;
		$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where kodeInduk='$par[kodeDiv]' order by namaData");
		// var_dump($data); 
		// die;
		return implode("\n", $data);
	}
	function group() {
		global $s, $id, $inp, $par, $arrParameter,$db;
		$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where kodeInduk='$par[kodeDep]' order by namaData");
		// var_dump($data); 
		// die;
		return implode("\n", $data);
	}
	function line() {
		global $s, $id, $inp, $par, $arrParameter,$db;
		$data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where kodeInduk='$par[kodeGroup]' order by namaData");
		// var_dump($data); 
		// die;
		return implode("\n", $data);
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
		
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and '".setTanggal($par[tanggalAbsen])."' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$dtaNormal=getField("select concat(namaShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho', 'shift 1')");
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
		<span class=\"field\">$r[nomorAbsen]&nbsp;</span>
		</p>";
		$text.="</div>
		<p>
		<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode,tanggalAbsen,keteranganAbsen")."';\" style=\"float:right;\" />
		</p>
		</form>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[tanggalMulai])) $par[tanggalMulai] = date('01/m/Y');
		if(empty($par[tanggalSelesai])) $par[tanggalSelesai] = date('d/m/Y');
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."						
		</div>
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute; right:0; top:0; margin-top:130px; margin-right:20px;\">
			Periode : 
			<input type=\"text\" id=\"tanggalMulai\" name=\"par[tanggalMulai]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalMulai]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" /> <strong>s.d</strong> <input type=\"text\" id=\"tanggalSelesai\" name=\"par[tanggalSelesai]\" size=\"10\" maxlength=\"10\" value=\"".$par[tanggalSelesai]."\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />					
			<input type=\"hidden\" id=\"par[idPegawai]\" name=\"par[idPegawai]\" value=\"".$par[idPegawai]."\" >
			<input type=\"hidden\" id=\"par[idLokasi]\" name=\"par[idLokasi]\" value=\"".$par[idLokasi]."\" >
			<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"".$par[mode]."\" >
			<input type=\"hidden\" id=\"par[bulanAbsen]\" name=\"par[bulanAbsen]\" value=\"".$par[bulanAbsen]."\" >
			<input type=\"hidden\" id=\"par[tahunAbsen]\" name=\"par[tahunAbsen]\" value=\"".$par[tahunAbsen]."\" >
		</div>			
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
		<th colspan=\"2\" style=\"width:80px;\">Jadwal</th>			
		<th colspan=\"2\" style=\"width:80px;\">Aktual</th>
		<th rowspan=\"2\" style=\"width:40px;\">Durasi</th>
		<th rowspan=\"2\" style=\"width:80px;\">Keterangan</th>			
		</tr>
		<tr>
		<th style=\"width:40px;\">Masuk</th>
		<th style=\"width:40px;\">Pulang</th>
		<th style=\"width:40px;\">Masuk</th>
		<th style=\"width:40px;\">Pulang</th>
		</tr>
		</thead>
		<tbody>";
		
		$arrNormal=getField("select concat(mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho', 'shift 1')");
		$arrShift=arrayQuery("select t1.idPegawai, concat(t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		
		$sql="select * from dta_absen where idPegawai='$par[idPegawai]' and (date(mulaiAbsen) between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."' or date(selesaiAbsen) between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."')";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			list($r[mulaiShift], $r[selesaiShift]) = is_array($arrShift["$r[idPegawai]"]) ?
			explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal) ;
						
			list($tanggalMulai) = explode(" ", $r[mulaiAbsen]);
			list($tanggalSelesai) = explode(" ", $r[selesaiAbsen]);			
			while($tanggalMulai <= $tanggalSelesai){				
				list($Y,$m,$d) = explode("-", $tanggalMulai);									
				if($tanggalMulai >= setTanggal($par[tanggalMulai]) && $tanggalMulai <= setTanggal($par[tanggalSelesai])){
					$r[tanggalAbsen] = $tanggalMulai;
					list($tanggalAbsen, $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
					list($tanggalAbsen, $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);
					
					$arrData[$tanggalMulai] = $r;
				}					
				$tanggalMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
			
		}	
		
		$sql="select * from dta_jadwal where tanggalJadwal between '".setTanggal($par[tanggalMulai])."' and '".setTanggal($par[tanggalSelesai])."' and idPegawai='$par[idPegawai]' and idShift>0 group by 1,2";
		$res=db($sql);		
		$arrAlpha=array();
		while($r=mysql_fetch_array($res)){	
			$arrJadwal["$r[tanggalJadwal]"] = $r[mulaiJadwal]."\t".$r[selesaiJadwal];
			if(!isset($arrData["$r[tanggalJadwal]"])) $arrAlpha["$r[tanggalJadwal]"]= $r[tanggalJadwal];
		}
		
		$tanggalAbsen = setTanggal($par[tanggalMulai]);
		while($tanggalAbsen <= setTanggal($par[tanggalSelesai])){				
			list($Y,$m,$d) = explode("-", $tanggalAbsen);									
			$no++;
			$r=$arrData[$tanggalAbsen];
			
			list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal[$tanggalAbsen]);
			if(isset($arrAlpha[$tanggalAbsen])) $r[keteranganAbsen] = "Tidak Masuk";
				
			$week = date("w", strtotime($tanggalAbsen));
			$color = (in_array($week, array(0,6)) || getField("select idLibur from dta_libur where '".$tanggalAbsen."' between mulaiLibur and selesaiLibur and statusLibur='t'")) ? "style=\"background:#f2dbdb;\"" : "";
			
			if(empty($r[mulaiShift]) && empty($r[selesaiShift]))
			list($r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);
		
			if($r[mulaiShift] == "00:00:00") $r[mulaiShift] = "";
			if($r[selesaiShift] == "00:00:00") $r[selesaiShift] = "";		
			
			if($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
			if($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";			
			
			$text.="<tr ".$color.">
			<td>$no.</td>				
			<td><a href=\"?par[mode]=dta&par[tanggalAbsen]=".getTanggal($tanggalAbsen)."&par[keteranganAbsen]=$r[keteranganAbsen]".getPar($par,"mode,tanggalAbsen,keteranganAbsen")."\" title=\"Detail Data\" class=\"detil\">".getTanggal($tanggalAbsen, "t")."</a></td>
			<td align=\"center\">".substr($r[mulaiShift],0,5)."</td>
			<td align=\"center\">".substr($r[selesaiShift],0,5)."</td>
			<td align=\"center\">".substr($r[masukAbsen],0,5)."</td>
			<td align=\"center\">".substr($r[pulangAbsen],0,5)."</td>
			<td align=\"center\">".substr(str_replace("-","",$r[durasiAbsen]),0,5)."</td>
			<td>$r[keteranganAbsen]</td>				
			</tr>";	
			
			$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
		}
	
		$text.="</tbody>
		</table>	
		<p>			
		<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai,tanggalMulai,tanggalSelesai")."';\" style=\"float:right;\" />
		</p>
		</div>
		</div>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');						
		$par[tanggalAbsen] = 15;		
		
		$bulanAbsen = $par[bulanAbsen] == 1 ? 12 : str_pad($par[bulanAbsen] - 1, 2, "0", STR_PAD_LEFT);
		$tahunAbsen = $par[bulanAbsen] == 1 ? $par[tahunAbsen] - 1 : $par[tahunAbsen];
		$tanggalAbsen = 16;				
		
		$tanggalMulai = $tahunAbsen."-".$bulanAbsen."-".$tanggalAbsen;
		$tanggalSelesai = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$par[tanggalAbsen];		
		$day = selisihHari($tanggalMulai,$tanggalSelesai)+1;		
		
		while($tanggalMulai <= $tanggalSelesai){				
			list($Y,$m,$d) = explode("-", $tanggalMulai);								
				$arrPeriode[$Y.$m]++;
			$tanggalMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
		}	
		
		$cols = 13+$day;
		//$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		for($i=4; $i<=9+$day; $i++){
			$arrNot[] = $i;
		}
		$text = table($cols, $arrNot, "lst", "true", "h");
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		
		
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute; right:0; top:0; margin-right:20px;\">
		Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
		<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
		".comboMonth("par[bulanAbsen]", $par[bulanAbsen], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunAbsen]", $par[tahunAbsen], "", "onchange=\"document.getElementById('form').submit();\"")."
		<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
		<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
		</div>
		</form>
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
		<br clear=\"all\"/>
		<div style=\"position:absolute; z-index:9999; top:0; right:0; margin-top:120px; margin-right:30px;\">
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
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">NAMA</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		<input type=\"text\" id=\"sSearch\" name=\"sSearch\" value=\"\" style=\"width:290px;\"/>
		</div>
		</p>
		<div id=\"dFilter\" style=\"visibility:collapse;\">
		<p>
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">PERUSAHAAN</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		".comboData("select kodeData, namaData from mst_data where kodeCategory='X04' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"pSearch", "ALL", "", "onchange=\"getDiv('".getPar($par, "mode")."');\"", "300px;")."
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">DIVISI</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		".comboData("select kodeData, namaData from mst_data where kodeCategory='X05' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"bSearch", "ALL", "", "onchange=\"getDep('".getPar($par, "mode")."');\"", "300px;")."
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">DEPARTEMEN</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		".comboData("select kodeData, namaData from mst_data where kodeCategory='X06' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"tSearch", "ALL", "", "onchange=\"getGroup('".getPar($par, "mode")."');\"", "300px;")."
		</div>
		</p>
		
		<p>
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">GROUP</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		".comboData("select kodeData, namaData from mst_data where kodeCategory='X07' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"mSearch", "ALL", "", "onchange=\"getLine('".getPar($par, "mode")."');\"", "300px;")."
		</div>
		</p>
		<p>
		<label class=\"l-input-small\" style=\"width:100px; text-align:left; padding-left:10px;\">LINE</label>
		<div class=\"field\" style=\"margin-left:100px;\">
		".comboData("select kodeData, namaData from mst_data where kodeCategory='X08' and statusData='t' order by urutanData", "kodeData" , "namaData" ,"zSearch", "ALL", "", "", "300px;")."
		</div>
		</p>
		
		</div>
		</fieldset>	
		</form>
		<br clear=\"all\" />
		<strong>Periode</strong> : ".$tanggalAbsen." ".getBulan($bulanAbsen)." ".$tahunAbsen." s.d ".$par[tanggalAbsen]." ".getBulan($par[bulanAbsen])." ".$par[tahunAbsen]."
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
		<thead>
		<tr>
		<th rowspan=\"2\" width=\"20\">No.</th>			
		<th rowspan=\"2\" style=\"min-width:350px;\">Nama</th>
		<th rowspan=\"2\" style=\"min-width:100px;\">NPP</th>
		<th rowspan=\"2\" style=\"min-width:350px;\">Departemen</th>";
	if(is_array($arrPeriode)){
		while(list($Ym, $cols) = each($arrPeriode)){
			$tahunPeriode = substr($Ym,0,4);
			$bulanPeriode = substr($Ym,4,2);
			$text.="<th colspan=\"".$cols."\">".getBulan($bulanPeriode)." ".$tahunPeriode."</th>";
		}
	}
		
	$text.="<th rowspan=\"2\" style=\"min-width:40px;\">Total</th>
		<th rowspan=\"2\" style=\"min-width:40px;\">HK</th>
		<th rowspan=\"2\" style=\"min-width:40px;\">S</th>
		<th rowspan=\"2\" style=\"min-width:40px;\">I</th>
		<th rowspan=\"2\" style=\"min-width:40px;\">C</th>
		<th rowspan=\"2\" style=\"min-width:40px;\">A</th>
		<th colspan=\"2\">Keterlambatan</th>
		<th rowspan=\"2\" style=\"min-width:50px;\">Detail</th>
		</tr>
		<tr>";					
		
		$tanggalMulai = $tahunAbsen."-".$bulanAbsen."-".$tanggalAbsen;
		$tanggalSelesai = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$par[tanggalAbsen];			
		while($tanggalMulai <= $tanggalSelesai){				
			list($Y,$m,$d) = explode("-", $tanggalMulai);								
				$text.="<th style=\"min-width:40px;\">".$d."</th>";
			$tanggalMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
		}		
		$text.="
		<th style=\"min-width:100px;\">Menit</th>
		<th style=\"min-width:100px;\">Hari</th>
		</tr>
		</thead>
		<tbody></tbody>
		</table>
		</div>";
					
		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=LAPORAN DURASI KEHADIRAN.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
		
		return $text;
	}
	
	function lData(){
		global $s,$par,$menuAccess,$arrParameter;
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');						
		$par[tanggalAbsen] = 15;		
		
		$bulanAbsen = $par[bulanAbsen] == 1 ? 12 : str_pad($par[bulanAbsen] - 1, 2, "0", STR_PAD_LEFT);
		$tahunAbsen = $par[bulanAbsen] == 1 ? $par[tahunAbsen] - 1 : $par[tahunAbsen];
		$tanggalAbsen = 16;				
		
		$tanggalMulai = $tahunAbsen."-".$bulanAbsen."-".$tanggalAbsen;
		$tanggalSelesai = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$par[tanggalAbsen];		
		$day = selisihHari($tanggalMulai,$tanggalSelesai)+1;
		
		$arrDept = arrayQuery("select kodeData, namaData from mst_data where left(kodeCategory,1)='X'");
		
		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho')");
		$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		
		$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".$tanggalMulai."' and '".$tanggalSelesai."' and lower(trim(t2.namaShift)) not in ('off', 'cuti')");
		
		$sql="select * from dta_libur where (mulaiLibur between '".$tanggalMulai."' and '".$tanggalSelesai."') or (selesaiLibur between '".$tanggalMulai."' and '".$tanggalSelesai."')";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$liburMulai = $r[mulaiLibur];
			$liburSelesai = $r[selesaiLibur];			
			while($liburMulai <= $liburSelesai){				
				list($Y,$m,$d) = explode("-", $liburMulai);									
				if($liburMulai >= $tanggalMulai && $liburMulai <= $tanggalSelesai){
					$arrLibur["".$liburMulai.""] = $liburMulai;
				}					
				$liburMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}				
		}
		
		$sql="select * from dta_absen where (date(mulaiAbsen) between '".$tanggalMulai."' and '".$tanggalSelesai."') or ('".$tanggalMulai."' between date(mulaiAbsen) and date(selesaiAbsen)) or ('".$tanggalSelesai."' between date(mulaiAbsen) and date(selesaiAbsen))";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			list($absenMulai, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);
			list($absenSelesai) = explode(" ", $r[selesaiAbsen]);			
			
			$mulaiJadwal = isset($arrJadwal[$absenMulai]["$r[idPegawai]"]) ? $arrJadwal[$absenMulai]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];
			
			if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
			list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
			list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);
			
			if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){		
				$d1 = $absenMulai." ".$mulaiJadwal;
				$d2 = $r[mulaiAbsen];
				$durasiMenit = selisihMenit($d1, $d2);
												
				$arrTerlambat["$r[idPegawai]"][$absenMulai]=$r[idPegawai];
				$arrMenit["$r[idPegawai]"]+=$durasiMenit;
			}
			
			while($absenMulai <= $absenSelesai){				
				list($Y,$m,$d) = explode("-", $absenMulai);									
				if($absenMulai >= $tanggalMulai && $absenMulai <= $tanggalSelesai){
					$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
					$arrData["".$r[idPegawai].""]["".$absenMulai.""] = $r[durasiAbsen];
					
					if(in_array(trim(strtolower($r[keteranganAbsen])), array("izin cuti"))){
						$arrCuti["$r[idPegawai]"]++;
						$arrNote["$r[idPegawai]"][$absenMulai] = "C";
					}else if(in_array(trim(strtolower($r[keteranganAbsen])), array("izin sakit"))){
						$arrSakit["$r[idPegawai]"]++;
						$arrNote["$r[idPegawai]"][$absenMulai] = "S";
					}else if(in_array(trim(strtolower($r[keteranganAbsen])), array("", "koreksi absen"))){
						$arrAbsen["$r[idPegawai]"][$absenMulai] = str_replace("-","",$r[durasiAbsen]);				
					}else{
						$arrIzin["$r[idPegawai]"]++;
						if(substr($r[nomorAbsen], 4,2) == "IB"){

						$arrNote["$r[idPegawai]"][$absenMulai] = "IB";
						}else{
								
						$arrNote["$r[idPegawai]"][$absenMulai] = "I";
						}
						
					}
				}					
				$absenMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}				
		
		$sql="select * from dta_jadwal where tanggalJadwal between '".$tanggalMulai."' and '".$tanggalSelesai."' and idShift>0 group by 1,2";
		$res=db($sql);		
		$arrAlpha=array();
		while($r=mysql_fetch_array($res)){	
			$arrJadwal["$r[idPegawai]"]++; 
			$arrAlpha["$r[idPegawai]"]+= isset($arrData["$r[idPegawai]"]["$r[tanggalJadwal]"]) ? 0 : 1;
			if(!isset($arrData["$r[idPegawai]"]["$r[tanggalJadwal]"])) $arrAbsen["$r[idPegawai]"]["$r[tanggalJadwal]"] = "A";
		}
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
		$sWhere = "where t1.id is not null and t1.status='".$status."'";
		
		if(!empty($par[idLokasi]))
		$sWhere.= " and t2.location='".$par[idLokasi]."'";
		
		if(!empty($_GET['sSearch']))
		$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'";
		
		if (!empty($_GET['pSearch'])) $sWhere.= " and t2.rank='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $sWhere.= " and t2.div_id='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $sWhere.= " and t2.dept_id='".$_GET['tSearch']."'";
		if (!empty($_GET['zSearch'])) $sWhere.= " and t2.line_id='".$_GET['zSearch']."'";
		if (!empty($_GET['mSearch'])) $sWhere.= " and t2.unit_id='".$_GET['mSearch']."'";
		
		
		$arrOrder = array(
		"t1.name",
		"t1.name",
		"t1.reg_no",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
				
		$sql="select t1.*, t2.dept_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $sWhere order by $orderBy $sLimit";
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
			
			$controlAbsen="<a href=\"?par[mode]=det&par[idPegawai]=$r[id]&par[tanggalMulai]=".getTanggal($tanggalMulai)."&par[tanggalSelesai]=".getTanggal($tanggalSelesai)."".getPar($par,"mode,idPegawai,tanggalMulai,tanggalSelesai")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";	
			
			$data=array();
			$data[]="<div align=\"center\">".$no.".</div>";
			$data[]="<div align=\"left\">".strtoupper($r[name])."</div>";
			$data[]="<div align=\"left\">".$r[reg_no]."</div>";
			$data[]="<div align=\"left\">".strtoupper($arrDept["".$r[dept_id].""])."</div>";
						
			$tanggalAbsen = $tanggalMulai;			
			while($tanggalAbsen <= $tanggalSelesai){				
				list($Y,$m,$d) = explode("-", $tanggalAbsen);													
				$week = date("w", strtotime($tanggalAbsen));
				$color = (in_array($week, array(0,6)) || isset($arrLibur[$tanggalAbsen])) ? "style=\"background:#f2dbdb;\"" : "";
				$detAbsen = !isset($arrNote["$r[id]"][$tanggalAbsen]) ? substr($arrAbsen["$r[id]"][$tanggalAbsen],0,5) : $arrNote["$r[id]"][$tanggalAbsen];
				$data[]="<div align=\"center\" ".$color.">".$detAbsen."&nbsp;</div>";
				
				$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
			
			$data[]="<div align=\"center\">".substr(sumTime($arrAbsen["$r[id]"]),0,5)."</div>";
			$data[]="<div align=\"center\">".getAngka($arrJadwal["$r[id]"])."</div>";
			$data[]="<div align=\"center\">".getAngka($arrSakit["$r[id]"])."</div>";
			$data[]="<div align=\"center\">".getAngka($arrIzin["$r[id]"])."</div>";
			$data[]="<div align=\"center\">".getAngka($arrCuti["$r[id]"])."</div>";
			$data[]="<div align=\"center\">".getAngka($arrAlpha["$r[id]"])."</div>";
			$data[]=$arrMenit["$r[id]"] > 0 ? "<div align=\"right\">".getAngka($arrMenit["$r[id]"])." Menit</div>" : "<div align=\"right\"></div>";
			$data[]=count($arrTerlambat["$r[id]"]) > 0 ? "<div align=\"right\">".getAngka(count($arrTerlambat["$r[id]"]))." Hari</div>" : "<div align=\"right\"></div>";
			$data[]="<div align=\"center\">".$controlAbsen."</div>";
			
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
		
		if(empty($par[bulanAbsen])) $par[bulanAbsen] = date('m');
		if(empty($par[tahunAbsen])) $par[tahunAbsen] = date('Y');						
		$par[tanggalAbsen] = 15;	
		
		$bulanAbsen = $par[bulanAbsen] == 1 ? 12 : str_pad($par[bulanAbsen] - 1, 2, "0", STR_PAD_LEFT);
		$tahunAbsen = $par[bulanAbsen] == 1 ? $par[tahunAbsen] - 1 : $par[tahunAbsen];
		$tanggalAbsen = 16;		
		
		$tanggalMulai = $tahunAbsen."-".$bulanAbsen."-".$tanggalAbsen;
		$tanggalSelesai = $par[tahunAbsen]."-".$par[bulanAbsen]."-".$par[tanggalAbsen];		
		$day = selisihHari($tanggalMulai,$tanggalSelesai)+1;						
		
		$arrNormal=getField("select mulaiShift from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho')");
		$arrShift=arrayQuery("select t1.idPegawai, t2.mulaiShift from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		
		$arrJadwal=arrayQuery("select t1.tanggalJadwal, t1.idPegawai, t1.mulaiJadwal from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.tanggalJadwal between '".$tanggalMulai."' and '".$tanggalSelesai."' and lower(trim(t2.namaShift)) not in ('off', 'cuti')");
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		
		$cols=5;
		$tanggalAbsen = $tanggalMulai;
		while($tanggalAbsen <= $tanggalSelesai){				
			list($Y,$m,$d) = explode("-", $tanggalAbsen);								
			
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
			
			$arrPeriode[$Y.$m]++;	
			$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));			
			$cols++;
		}
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(10);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);		
		
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN DURASI KEHADIRAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Periode : '.getTanggal($tanggalMulai,"t")." s.d ".getTanggal($tanggalSelesai,"t"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols).'5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'DEPARTEMEN');
		
		$cols=5;
		if(is_array($arrPeriode)){
				while(list($Ym, $cnt) = each($arrPeriode)){
				$tahunPeriode = substr($Ym,0,4);
				$bulanPeriode = substr($Ym,4,2);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', getBulan($bulanPeriode)." ".$tahunPeriode);
				$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols+$cnt-1).'4');
				$cols+=$cnt;								
			}
		}
		
		$cols=5;
		$tanggalAbsen = $tanggalMulai;
		while($tanggalAbsen <= $tanggalSelesai){				
			list($Y,$m,$d) = explode("-", $tanggalAbsen);							
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', $d);
			$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));			
			$cols++;
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "TOTAL");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "HK");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "S");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "I");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "C");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "A");					
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', "KETERLAMBATAN");			
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols + 1).'4');
		
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', "MENIT");	
		$cols++;
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', "HARI");
				
		$arrDept = arrayQuery("select kodeData, namaData from mst_data where left(kodeCategory,1)='X'");

		$sql="select * from dta_libur where (mulaiLibur between '".$tanggalMulai."' and '".$tanggalSelesai."') or (selesaiLibur between '".$tanggalMulai."' and '".$tanggalSelesai."')";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$liburMulai = $r[mulaiLibur];
			$liburSelesai = $r[selesaiLibur];			
			while($liburMulai <= $liburSelesai){				
				list($Y,$m,$d) = explode("-", $liburMulai);									
				if($liburMulai >= $tanggalMulai && $liburMulai <= $tanggalSelesai){
					$arrLibur["".$liburMulai.""] = $liburMulai;
				}					
				$liburMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}				
		}
		
		$sql="select * from dta_absen where (date(mulaiAbsen) between '".$tanggalMulai."' and '".$tanggalSelesai."') or ('".$tanggalMulai."' between date(mulaiAbsen) and date(selesaiAbsen)) or ('".$tanggalSelesai."' between date(mulaiAbsen) and date(selesaiAbsen))";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			list($absenMulai, $mulaiAbsen) = explode(" ", $r[mulaiAbsen]);
			list($absenSelesai) = explode(" ", $r[selesaiAbsen]);			
			
			$mulaiJadwal = isset($arrJadwal[$absenMulai]["$r[idPegawai]"]) ? $arrJadwal[$absenMulai]["$r[idPegawai]"] : $arrShift["$r[idPegawai]"];
			
			if($mulaiJadwal == "00:00:00" || empty($mulaiJadwal)) $mulaiJadwal = $arrNormal;
			list($jamJadwal, $menitJadwal) = explode(":", $mulaiJadwal);
			list($jamAbsen, $menitAbsen) = explode(":", $mulaiAbsen);
			
			if($jamAbsen.$menitAbsen > $jamJadwal.$menitJadwal && !empty($mulaiAbsen) && $mulaiAbsen != "00:00:00"){		
				$d1 = $absenMulai." ".$mulaiJadwal;
				$d2 = $r[mulaiAbsen];
				$durasiMenit = selisihMenit($d1, $d2);
												
				$arrTerlambat["$r[idPegawai]"][$absenMulai]=$r[idPegawai];
				$arrMenit["$r[idPegawai]"]+=$durasiMenit;
			}
			
			while($absenMulai <= $absenSelesai){				
				list($Y,$m,$d) = explode("-", $absenMulai);									
				if($absenMulai >= $tanggalMulai && $absenMulai <= $tanggalSelesai){
					$r[durasiAbsen] = substr($r[durasiAbsen],0,5) == "00:00" ? "" : substr($r[durasiAbsen],0,5);
					$arrData["".$r[idPegawai].""]["".$absenMulai.""] = $r[durasiAbsen];
					
					if(in_array(trim(strtolower($r[keteranganAbsen])), array("izin cuti"))){
						$arrCuti["$r[idPegawai]"]++;
						$arrNote["$r[idPegawai]"][$absenMulai] = "C";
					}else if(in_array(trim(strtolower($r[keteranganAbsen])), array("izin sakit"))){
						$arrSakit["$r[idPegawai]"]++;
						$arrNote["$r[idPegawai]"][$absenMulai] = "S";
					}else if(in_array(trim(strtolower($r[keteranganAbsen])), array("", "koreksi absen"))){
						$arrAbsen["$r[idPegawai]"][$absenMulai] = str_replace("-","",$r[durasiAbsen]);				
					}else{
						$arrIzin["$r[idPegawai]"]++;
						if(substr($r[nomorAbsen], 4,2) == "IB"){

						$arrNote["$r[idPegawai]"][$absenMulai] = "IB";
						}else{
								
						$arrNote["$r[idPegawai]"][$absenMulai] = "I";
						}
						
					}
				}					
				$absenMulai=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			}
		}				
		
		$sql="select * from dta_jadwal where tanggalJadwal between '".$tanggalMulai."' and '".$tanggalSelesai."' and idShift>0 group by 1,2";
		$res=db($sql);		
		$arrAlpha=array();
		while($r=mysql_fetch_array($res)){	
			$arrJadwal["$r[idPegawai]"]++; 
			$arrAlpha["$r[idPegawai]"]+= isset($arrData["$r[idPegawai]"]["$r[tanggalJadwal]"]) ? 0 : 1;
			if(!isset($arrData["$r[idPegawai]"]["$r[tanggalJadwal]"])) $arrAbsen["$r[idPegawai]"]["$r[tanggalJadwal]"] = "A";
		}
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");	
		$sWhere = "where t1.id is not null and t1.status='".$status."'";
		
		if(!empty($par[idLokasi]))
		$sWhere.= " and t2.location='".$par[idLokasi]."'";
		
		if(!empty($_GET['sSearch']))
		$sWhere.= " and lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['sSearch']))."%'";
		
		if (!empty($_GET['pSearch'])) $sWhere.= " and t2.rank='".$_GET['pSearch']."'";
		if (!empty($_GET['bSearch'])) $sWhere.= " and t2.div_id='".$_GET['bSearch']."'";
		if (!empty($_GET['tSearch'])) $sWhere.= " and t2.dept_id='".$_GET['tSearch']."'";
		if (!empty($_GET['zSearch'])) $sWhere.= " and t2.line_id='".$_GET['zSearch']."'";
		if (!empty($_GET['mSearch'])) $sWhere.= " and t2.unit_id='".$_GET['mSearch']."'";
		
		
		$no=1;
		$rows=6;
		$sql="select t1.*, t2.dept_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $sWhere order by t1.name";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($arrDept["".$r[dept_id].""]));
			
			$cols=5;
			$tanggalAbsen = $tanggalMulai;						
			while($tanggalAbsen <= $tanggalSelesai){				
				list($Y,$m,$d) = explode("-", $tanggalAbsen);													
				$week = date("w", strtotime($tanggalAbsen));				
				
				if((in_array($week, array(0,6)) || isset($arrLibur[$tanggalAbsen])))
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFADDDB');
				$detAbsen = !isset($arrNote["$r[id]"][$tanggalAbsen]) ? substr($arrAbsen["$r[id]"][$tanggalAbsen],0,5) : $arrNote["$r[id]"][$tanggalAbsen];
				
				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $detAbsen);				
				
				$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
				$cols++;
			}
			
			$dtaAbsen = sumTime($arrAbsen["$r[id]"]);
			$sumAbsen = !empty($dtaAbsen) ? substr($dtaAbsen,0,5) : "";
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $sumAbsen);
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrJadwal["$r[id]"]));
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrSakit["$r[id]"]));
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrIzin["$r[id]"]));
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrCuti["$r[id]"]));
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getAngka($arrAlpha["$r[id]"]));			
			$cols++;
			
			$menit=$arrMenit["$r[id]"] > 0 ? getAngka($arrMenit["$r[id]"])." Menit" : "";
			$hari=count($arrTerlambat["$r[id]"]) > 0 ? getAngka(count($arrTerlambat["$r[id]"]))." Hari" : "";
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $menit);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $hari);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':C'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':'.numToAlpha($cols-2).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$rows++;
			$no++;
		}
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$cols=5;
		$tanggalAbsen = $tanggalMulai;			
		while($tanggalAbsen <= $tanggalSelesai){				
			list($Y,$m,$d) = explode("-", $tanggalAbsen);													
						
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$tanggalAbsen=date("Y-m-d",dateAdd("d", 1, mktime(0,0,0,$m,$d,$Y)));
			$cols++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;		
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
				
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 5);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("LAPORAN DURASI KEHADIRAN");
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile."LAPORAN DURASI KEHADIRAN.xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){	
			case "lst":
			$text=lData();
			break;
			
			case "dta":
			$text = data();
			break;
			case "det":
			$text = detail();
			break;
			case "div":
			$text = div();
			break;
			case "group":
			$text = group();
			break;
			case "line":
			$text = line();
			break;
			case "dep":
			$text = dep();
			break;
			default:
			$text = lihat();
			break;
		}
		return $text;
	}
?>
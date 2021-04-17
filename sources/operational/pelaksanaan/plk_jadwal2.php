<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	$fFile = "files/rencana/";
	
	function lihat(){
		global $db,$s,$inp,$par,$_submit,$arrTitle,$fFile,$arrParameter,$menuAccess;
		if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
		$arr = explode("/",$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
		$url="http://";
		for($i=0; $i<(count($arr)-1); $i++){
			$url.=$arr[$i]."/";
		}
		$url.="calendar/pelatihan/plk_jadwal.php?".getPar($par,"mode")."";	
		$arrData = json_decode(file_get_contents($url), true);
				
		$tanggalPelatihan = $par[tahunPelatihan] == date('Y') ? date('d') : 1;
		if(empty($par[bulanPelatihan]))
			$bulanPelatihan = $par[tahunPelatihan] == date('Y') ? date('m') - 1 : 0;
		else
			$bulanPelatihan = $par[bulanPelatihan] - 1;
		$tahunPelatihan = empty($par[tahunPelatihan]) ? date('Y') : $par[tahunPelatihan];		
		
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
			</form>
			<br clear=\"all\" />
			<a href=\"?par[mode]=det".getPar($par,"mode,idPelatihan,bulanPelatihan")."\" class=\"btn btn1 btn_grid\" style=\"position:absolute; left:0; top:0; margin-left:20px; margin-top:75px;\"><span>PER TAHUN</span></a>
			<div class=\"one_half last dashboard_left\" style=\"margin-top:20px;\">
				<script type=\"text/javascript\" src=\"scripts/calendar.js\"></script>
				<script type=\"text/javascript\">
					jQuery(function () {						
						jQuery('#calendar').fullCalendar({	
							year: ".$tahunPelatihan.",
							month: ".$bulanPelatihan.",
							date: ".$tanggalPelatihan.",
						
							header: {
								left: 'month,agendaWeek,agendaDay',
								center: 'title',
								right: 'prev, next'
							},
							buttonText: {
								prev: '&laquo;',
								next: '&raquo;',
								prevYear: '&nbsp;&lt;&lt;&nbsp;',
								nextYear: '&nbsp;&gt;&gt;&nbsp;',
								today: 'today',
								month: 'month',
								week: 'week',
								day: 'day'
							},
							events: {
								url: '$url',
								cache: true
							},
							
							eventMouseover: function(calEvent, jsEvent) {								
								arr = calEvent.tmp.split(\"\\n\");
								
								var tooltip = '<div class=\"tooltipevent\" style=\"background:'+ calEvent.color +'; color:#fff; padding:10px 20px; position:absolute;z-index:10000; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;\">';
								
								tooltip = tooltip + '<strong>' + arr[0] + '</strong><br>';
								tooltip = tooltip + arr[1] + '<br>';
								tooltip = tooltip + '</div>';
								
								jQuery(\"body\").append(tooltip);
								jQuery(this).mouseover(function(e) {
									jQuery(this).css('z-index', 10000);
									jQuery('.tooltipevent').fadeIn('500');
									jQuery('.tooltipevent').fadeTo('10', 1.9);
								}).mousemove(function(e) {
									jQuery('.tooltipevent').css('top', e.pageY + 10);
									jQuery('.tooltipevent').css('left', e.pageX + 20);
								});
							},

							eventMouseout: function(calEvent, jsEvent) {
								jQuery(this).css('z-index', 8);
								jQuery('.tooltipevent').remove();
							},
							
							eventClick: function (calEvent, jsEvent, view) {									
								gPar = document.getElementById('getPar').value;
								window.location.href='?par[idPelatihan]=' + calEvent.id + gPar;
							},														
						});
												
					});																	
				</script>				
				<div id=\"calendar\"></div>
				<input type=\"hidden\" id=\"getPar\" value=\"".getPar($par,"mode,idPelatihan")."\"/>
			</div>";
	
	
	$filter = "where idPelatihan is not null and statusPelatihan='t'";
	if(!empty($par[tahunPelatihan]))
		$filter.= " and ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan)";
	if(empty($par[idPelatihan]))
		$par[idPelatihan] = getField("select idPelatihan from plt_pelatihan $filter order by idPelatihan");
	
	$sql="select * from plt_pelatihan where idPelatihan='".$par[idPelatihan]."'";
	$res=db($sql);
	$r=mysql_fetch_array($res);
		
	if(!empty($r[idPelatihan])) $periodePelatihan = getTanggal($r[mulaiPelatihan], "t")." s.d ".getTanggal($r[selesaiPelatihan], "t");
	if(!empty($r[idPelatihan])) $pesertaPelatihan = getAngka($r[pesertaPelatihan])." Orang";
	if(!empty($r[idPelatihan])) $pelaksanaanPelatihan =  $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";	
	if(!empty($r[filePelatihan])) $filePelatihan = "<a href=\"download.php?d=rencana&f=$r[idPelatihan]\"><img src=\"".getIcon($fFile."/".$r[filePelatihan])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
	
	$text.="<div class=\"one_half last dashboard_right\" style=\"margin-left:20px;\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">				
				<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>VIEW DETAIL</h4></legend>
					<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Pelatihan</label>
						<span class=\"field\" style=\"margin-left:100px;\">$r[judulPelatihan]&nbsp;</span>
					</p>
					<table style=\"width:100%; margin-top:-5px; margin-bottom:-5px;\" cellpadding=\"0\" cellspacing=\"0\">
					<tr>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:100px;\">Sub</label>
							<span class=\"field\" style=\"margin-left:100px;\">$r[subPelatihan]&nbsp;</span>
						</p>
					</td>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:50px;\">Kode</label>
							<span class=\"field\" style=\"margin-left:50px;\">$r[kodePelatihan]&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>					
					<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Jumlah Peserta</label>
						<span class=\"field\" style=\"margin-left:100px;\">".$pesertaPelatihan."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Tanggal</label>
						<span class=\"field\" style=\"margin-left:100px;\">".$periodePelatihan."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Pelaksanaan</label>
						<span class=\"field\" style=\"margin-left:100px;\">".$pelaksanaanPelatihan."&nbsp;</span>
					</p>";
			if($r[pelaksanaanPelatihan] == "e")
			$text.="<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Vendor</label>
						<span class=\"field\" style=\"margin-left:100px;\">".getField("select namaVendor from dta_vendor where kodeVendor='".$r[idVendor]."'")."&nbsp;</span>
					</p>";
			$text.="<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Lokasi</label>
						<span class=\"field\" style=\"margin-left:100px;\">$r[lokasiPelatihan]&nbsp;</span>
					</p>
";					
			if($r[pelaksanaanPelatihan] == "e")
				$text.="<p>
						<label class=\"l-input-small\" style=\"width:100px;\">Trainer</label>
						<span class=\"field\" style=\"margin-left:100px;\">".getField("select namaTrainer from dta_trainer where idTrainer='".$r[idTrainer]."'")."&nbsp;</span>
					</p>";	
			else
				$text.="<p>
						<label class=\"l-input-small\" style=\"width:100px;\">PIC</label>
						<span class=\"field\" style=\"margin-left:100px;\">".getField("select name from emp where id='".$r[idPegawai]."'")."&nbsp;</span>
					</p>";
			$text."<p>
						<label class=\"l-input-small\" style=\"width:100px;\">File</label>
						<span class=\"field\" style=\"margin-left:100px;\">".$filePelatihan."&nbsp;</span>
					</p>
				</fieldset>
				</form>
			</div>";
			
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$arrColor,$menuAccess;
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
			".setPar($par, "filter,idKategori,tahunPelatihan")."
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
			<div id=\"pos_l\" style=\"float:right\">
				<a href=\"?".getPar($par,"mode,filter,idKategori,idPelatihan,bulanPelatihan")."\" class=\"btn btn1 btn_grid2\" ><span>PER BULAN</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Pelatihan</th>";
			for($i=1; $i<=12; $i++)
				$text.="<th style=\"width:30px;\">".getBulan($i, "t")."</th>";			
			$text.="</tr>
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
			or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
		)";
				
		$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from plt_pelatihan t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
			list($tahunMulai, $bulanMulai) = explode("-", $r[mulaiPelatihan]);
			list($tahunSelesai, $bulanSelesai) = explode("-", $r[selesaiPelatihan]);
			$text.="<tr>
					<td>$no.</td>			
					<td>$r[judulPelatihan]</td>";
			for($i=1; $i<=12; $i++){
				$periodePelatihan = $par[tahunPelatihan].str_pad($i, 2, "0", STR_PAD_LEFT);
				$color = $tahunMulai.$bulanMulai <= $periodePelatihan && $tahunSelesai.$bulanSelesai >= $periodePelatihan ? "background:".$arrColor[$no-1]."" : "";
				$text.="<td style=\"cursor:pointer; ".$color."\" onclick=\"window.location='?par[idPelatihan]=$r[idPelatihan]&par[bulanPelatihan]=".$i.getPar($par,"mode,filter,idKategori,idPelatihan,bulanPelatihan")."';\">&nbsp;</td>";
			}
			$text.="</tr>";							
		}	
		
		$text.="</tbody>
			</table>
			</div>";			
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
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
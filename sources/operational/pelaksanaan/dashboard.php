<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fPokok = "files/pokok/";
	
	function lihat(){
		global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;							
		if(empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
				
		$arrKategori=arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by urutanData");
		$cntPelatihan = arrayQuery("select idKategori, count(idPelatihan) from plt_pelatihan where ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan) and statusPelatihan='t' group by 1");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
			Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"")."
			</div>				
			</form>	

			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Pelatihan</h3></div>
			</div>			
			<div id=\"divPelatihan\" align=\"center\"></div>
			<script type=\"text/javascript\">
			var pelatihanChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";
			
			if(is_array($arrKategori)){
				reset($arrKategori);
				while (list($idKategori, $namaKategori) = each($arrKategori)){
					$text.="<set label=\"".$namaKategori."\" value=\"".$cntPelatihan[$idKategori]."\"/> ";
				}
			}
			
			$text.="</chart>';
			var chart = new FusionCharts(\"Column2D\", \"chartPelatihan\", \"100%\", 250);
				chart.setXMLData( pelatihanChart );
				chart.render(\"divPelatihan\");
			</script>
			
			
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>Pelaksanaan Pelatihan</h3></div>
			</div>
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
		
		$periodeTanggal = date('Ym');		
		$sql="select t1.* from plt_pelatihan t1 $filter order by t1.idPelatihan";
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
				
				$background = "#fff";
				if($periodeTanggal < $periodePelatihan) $background = "#1f11be";
				if($periodeTanggal == $periodePelatihan) $background = "#c1b00e";
				if($periodeTanggal > $periodePelatihan) $background = "#1fc22e";
				
				$color = $tahunMulai.$bulanMulai <= $periodePelatihan && $tahunSelesai.$bulanSelesai >= $periodePelatihan ? "background:".$background."" : "";
				$text.="<td style=\"cursor:pointer; ".$color."\" >&nbsp;</td>";
			}
			$text.="</tr>";							
		}	
		
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
		$text.="</tbody>
			</table>
			
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:100%; margin-top:30px;\">			
			<tr>
			<td style=\"width:50%; vertical-align:top; padding-right:30px;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jadwal Pelatihan</h3></div>
				</div>	
				<script src=\"scripts/calendar.js\"></script>
				<script>
					jQuery(function() {

						jQuery('#calendar').fullCalendar({	
							year: " . date('Y') . ",
							month: " . (date('m') - 1) . ",
							date:  " . date('d') . ",
							header: {
								left: 'month',
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
								url: 'ajax.php?". getPar($par, "mode") . "&par[mode]=datas',
								cache: true
							},
							eventClick: function(data) {
								openBox('popup.php?" . getPar($par, "mode, id") . "&par[mode]=detail&par[id]=' + data.id, '800', '550')
							}														
						})

					})																
				</script>
				<div id=\"calendar\"></div>
			</td>
			<td style=\"width:50%; vertical-align:top;\">
				<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Realiasai Biaya</h3></div>
				</div>	
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th>Pelatihan</th>											
						<th width=\"75\">Biaya</th>
						<th width=\"75\">Realisasi</th>
						<th width=\"30\">Persen</th>
					</tr>
				</thead>
				<tbody>";
			
			$filter = "where idPelatihan is not null and statusPelatihan='t'";
			if(!empty($par[tahunPelatihan]))
				$filter.= " and ".$par[tahunPelatihan]." between year(mulaiPelatihan) and year(selesaiPelatihan)";				
			$sql="select * from plt_pelatihan $filter order by idPelatihan";
			$res=db($sql);
			$no=1;
			while($r=mysql_fetch_array($res)){					
				
				list($nilaiRab, $nilaiRealisasi) = explode("\t", getField("select concat(sum(nilaiRab), '\t', sum(realisasiRab)) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'"));
				
				$text.="<tr>						
						<td>$r[judulPelatihan]</td>						
						<td align=\"right\">".getAngka($nilaiRab)."</td>
						<td align=\"right\">".getAngka($nilaiRealisasi)."</td>
						<td align=\"right\">".getAngka($nilaiRealisasi/$nilaiRab*100)."%</td>
						</tr>";
				
				$totalRab+=$nilaiRab;			
				$totalRealisasi+=$nilaiRealisasi;	
				$no++;				
			}
			
			$text.="</tbody>
				<tfoot>
					<tr>												
						<td><strong>TOTAL<strong></td>
						<td style=\"text-align:right\">".getAngka($totalRab)."</td>
						<td style=\"text-align:right\">".getAngka($nilaiRealisasi)."</td>
						<td>&nbsp;</td>
						</tr>
				</tfoot>
				</table>
			</td>
			</tr>
			</table>";
					
		return $text;
	}	

function getContent($par){
global $db,$s,$_submit,$menuAccess;		
switch($par[mode]){				
	
	case "datas":
		datas();
	break;

	default:
		$text = lihat();
	break;
}
return $text;
}

function datas() {

	global $par, $start, $end;

	$start 	= date('Y-m-d', $start);
	$end 	= date('Y-m-d', $end);

	$datas 	= [];
	$res 	= db("SELECT * FROM `plt_pelatihan` WHERE `mulaiPelatihan` BETWEEN '$start' AND '$end'");

	while ($row = mysql_fetch_assoc($res)) {
		
		$datas[] = [
			'id'	=> $row['idPelatihan'],
			'title'	=> "$row[judulPelatihan]",
			'start' => "$row[mulaiPelatihan]",
			'end'	=> date('Y-m-d', strtotime($row['selesaiPelatihan']) + 86400)
		];

	}

	echo json_encode($datas);
}
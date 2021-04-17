<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
	
	function lihat(){
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
			</form>
			<br clear=\"all\" />
			<div id=\"divPenyerapan\" align=\"center\"></div>
			<script type=\"text/javascript\">
			var penyerapanChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" showValues=\"0\" bgColor=\"F7F7F7, E9E9E9\">";
			
			$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
			if(!empty($par[tahunPelatihan]))
				$filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";
						
			$sql="select * from plt_pelatihan t1 join plt_pelatihan_rab t2 on (t1.idPelatihan=t2.idPelatihan) $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				list($tahunPelatihan, $bulanPelatihan) = explode("-", $r[mulaiPelatihan]);
				$arrRab[intval($bulanPelatihan)]+=$r[nilaiRab];
				$arrRealisasi[intval($bulanPelatihan)]+=$r[realisasiRab];
			}
						
			$categories="<categories>";			
			$anggaran="<dataset seriesName=\"Anggaran\">";
			$realisasi="<dataset seriesName=\"Realisasi\">";
			
			for($i=1; $i<=12; $i++){
				$categories.="<category label=\"".getBulan($i,"t")."\"/>";
				$anggaran.="<set value=\"".$arrRab[$i]."\" link=\"?par[mode]=det&par[bulanPelatihan]=".$i."".getPar($par,"mode,bulanPelatihan")."\"/>";
				$realisasi.="<set value=\"".$arrRealisasi[$i]."\" link=\"?par[mode]=det&par[bulanPelatihan]=".$i."".getPar($par,"mode,bulanPelatihan")."\"/>";
			}
			
			$categories.="</categories>";
			$anggaran.="</dataset>";
			$realisasi.="</dataset>";
			
			$text.=$categories.$anggaran.$realisasi;
			
			$text.="</chart>';
			var chart = new FusionCharts(\"MSColumn3D\", \"chartPenyerapan\", \"100%\", 250);
				chart.setXMLData( penyerapanChart );
				chart.render(\"divPenyerapan\");
			</script>
			</div>";			
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;
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
			<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : ".comboMonth("par[bulanPelatihan]", $par[bulanPelatihan], "onchange=\"document.getElementById('form').submit();\"","", "All")." ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>								
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/>
				".setPar($par, "filter,idKategori,bulanPelatihan,tahunPelatihan")."
				</td>
				</tr>
			</table>
			</div>		
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Pelatihan</th>					
					<th>Lokasi</th>
					<th>PIC</th>					
					<th width=\"125\">Biaya</th>
					<th width=\"125\">Realisasi</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
		if(!empty($par[tahunPelatihan]))
			$filter.= " and month(t1.mulaiPelatihan) = ".$par[bulanPelatihan]." and year(t1.mulaiPelatihan) = ".$par[tahunPelatihan]."";
		
		if(!empty($par[idKategori]))
			$filter.=" and t1.idKategori='".$par[idKategori]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.namaPegawai) like '%".strtolower($par[filter])."%'
			or lower(t2.namaTrainer) like '%".strtolower($par[filter])."%'
		)";
				
		$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
				select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
			) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $filter order by t1.idPelatihan";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){					
			$no++;
			list($nilaiRab, $nilaiRealisasi) = explode("\t", getField("select concat(sum(nilaiRab), '\t', sum(realisasiRab)) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'"));
			
			$text.="<tr>
					<td>$no.</td>			
					<td><a href=\"?par[mode]=dta&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>
					<td>$r[lokasiPelatihan]</td>
					<td>$r[namaPic]</td>
					<td align=\"right\">".getAngka($nilaiRab)."</td>
					<td align=\"right\">".getAngka($nilaiRealisasi)."</td>
					</tr>";
			
			$totalRab+=$nilaiRab;			
			$totalRealisasi+=$nilaiRealisasi;			
		}	
		
		$text.="</tbody>
			<tfoot>
				<tr>
					<td>&nbsp;</td>								
					<td>&nbsp;</td>
					<td>&nbsp;</td>					
					<td style=\"text-align:center\"><strong>TOTAL<strong></td>
					<td style=\"text-align:right\">".getAngka($totalRab)."</td>
					<td style=\"text-align:right\">".getAngka($nilaiRealisasi)."</td>
					</tr>
			</tfoot>
			</table>
			</div>";			
		return $text;
	}
	
	function dataa(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;						
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."#detail\" enctype=\"multipart/form-data\">
				<div style=\"top:10px; right:35px; position:absolute\">
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode, idPelatihan")."';\"/>
				</div>
				<div id=\"general\" style=\"margin-top:20px;\">					
					".dtaPelatihan("PELATIHAN")."
					<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
					<legend style=\"padding:10px; margin-left:20px;\"><h4>REALISASI BIAYA</h4></legend>
					<strong>BIAYA Rp. ".getAngka(getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'"))." &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;REALISASI Rp. ".getAngka(getField("select sum(realisasiRab) from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'"))." </strong>";
					if(isset($menuAccess[$s]["add"]))
				$text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idRab")."',725,450);\" style=\"float:right; margin-top:-5px; margin-bottom:10px;  margin-right:10px;\"><span>Tambah Data</span></a>";
				$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Uraian</th>
								<th width=\"75\">Jumlah</th>
								<th width=\"75\">Satuan</th>
								<th width=\"100\">Nilai</th>
								<th width=\"100\">Biaya</th>
								<th width=\"100\">Realisasi</th>
							</tr>
						</thead>
						<tbody>";
						
					$sql="select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' order by idRab";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){		
						$text.="<tr>
								<td>$no.</td>
								<td>$r[judulRab]</td>
								<td align=\"right\">".getAngka($r[jumlahRab])."</td>
								<td>$r[satuanRab]</td>
								<td align=\"right\">".getAngka($r[hargaRab])."</td>
								<td align=\"right\">".getAngka($r[nilaiRab])."</td>
								<td align=\"right\">".getAngka($r[realisasiRab])."</td>
								</tr>";
						$no++;
					}
					
					if($no == 1){
						$text.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								</tr>";
					}
					
					$text.="</tbody>
				</table>
				</fieldset>
				</div>				
			</form>";
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "dta":
				$text = dataa();
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
<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
	
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		
		$_SESSION["curr_emp_id"] = $par[idPegawai] = $cID;				
		
		if($par[mode] != "print"){
		echo "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread(ucwords($par[mode]." data"))."								
			</div>
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				<a href=\"ajax.php?par[mode]=print".getPar($par,"mode")."\" target=\"print\" class=\"btn btn1 btn_print\"><span>Cetak Jadwal</span></a>
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				".comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"")."
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
				<input type=\"hidden\" id=\"par[idPegawai]\" name=\"par[idPegawai]\" value=\"".$par[idPegawai]."\" >
				<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"".$par[mode]."\" >
			</div>
			</form>
			<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		}
		
		$sql="select * from dta_jadwal where idPegawai='".$par[idPegawai]."' and month(tanggalJadwal)='".$par[bulanJadwal]."' and year(tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			list($tahun, $bulan, $tanggal)=explode("-", $r[tanggalJadwal]);
			$arr["idShift"][intval($tanggal)]=$r[idShift];
			$arr["mulaiJadwal"][intval($tanggal)]=$r[mulaiJadwal];
			$arr["selesaiJadwal"][intval($tanggal)]=$r[selesaiJadwal];
			$arr["keteranganJadwal"][intval($tanggal)]=$r[keteranganJadwal];
		}				
		
		$text.="</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\">	
				<div id=\"general\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>JADWAL KERJA</h3></div>
					</div>				
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th rowspan=\"2\" width=\"20\">No.</th>
							<th rowspan=\"2\" style=\"width:150px;\">Tanggal</th>
							<th rowspan=\"2\" style=\"min-width:150px;\">Shift</th>
							<th colspan=\"2\" style=\"width:150px;\">Jadwal</th>
							<th rowspan=\"2\" style=\"min-width:150px;\">Keterangan</th>
						</tr>
						<tr>							
							<th style=\"width:75px;\">Masuk</th>
							<th style=\"width:75px;\">Keluar</th>
						</tr>
					</thead>
					<tbody>";
									
			$arrShift=arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");			
			ksort($arrShift);
			reset($arrShift);
			
			list($normalShift, $mulaiNormal, $selesaiNormal)=explode("\t", getField("select concat(namaShift,'\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift limit 1"));
			
			
			for($i=1; $i<=$day; $i++){
				
				$week = date("w", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
				$color = in_array($week, array(0,6)) ? "#f2dbdb" : "#ffffff";
								
				$arrShift["".$arr[idShift][$i].""] = empty($arr[idShift][$i]) ? $normalShift : $arrShift["".$arr[idShift][$i].""];$arr[mulaiJadwal][$i] = empty($arr[idShift][$i])  ? $mulaiNormal : $arr[mulaiJadwal][$i];
				$arr[selesaiJadwal][$i] = empty($arr[idShift][$i])  ? $selesaiNormal : $arr[selesaiJadwal][$i];				
				
				$text.="<tr style=\"background:".$color.";\">
					<td>$i.</td>
					<td>".str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal]."</td>
					<td align=\"left\">".$arrShift["".$arr[idShift][$i].""]."</td>
					<td align=\"center\">".substr($arr[mulaiJadwal][$i],0,5)."</td>
					<td align=\"center\">".substr($arr[selesaiJadwal][$i],0,5)."</td>
					<td align=\"left\">".$arr[keteranganJadwal][$i]."</td>
					</tr>";
			}
					
			$text.="</tbody>					
					</table>
				</div>
			</form><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		
		if($par[mode] == "print") pdf();
		
		return $text;
	}

	function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai=$r[name];
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(5);
		
		$pdf->Ln();		
		$pdf->SetFont('Arial','B',11);					
		$pdf->Cell(100,7,'JADWAL KERJA',0,0,'L');
		$pdf->Cell(100,7,strtoupper(getBulan($par[bulanProses]))." ".$par[tahunProses],0,0,'R');
		$pdf->Ln();		
		
		$pdf->Cell(200,1,'','B');
		$pdf->Ln(1.25);
		$pdf->Cell(200,1,'','T');
		$pdf->Ln();
		$pdf->Cell(200,1,'','T');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','','8');		
		$pdf->SetWidths(array(20, 5, 70, 10, 30, 5, 60));	
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->Row(array("NAMA\tb",":",$r[name],"","NPWP\tb",":",$r[npwp_no]), false);
		$pdf->Row(array("NPP\tb",':',$r[reg_no],'',"PANGKAT/GRADE\tb",":",getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")), false);
		$pdf->Row(array("JABATAN\tb",":",$r_[pos_name],"","LOKASI\tb",":",getField("select namaData from mst_data where kodeData='".$r_[location]."'")), false);
		
		$pdf->Ln();
		
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		$sql="select * from dta_jadwal where idPegawai='".$par[idPegawai]."' and month(tanggalJadwal)='".$par[bulanJadwal]."' and year(tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			list($tahun, $bulan, $tanggal)=explode("-", $r[tanggalJadwal]);
			$arr["idShift"][intval($tanggal)]=$r[idShift];
			$arr["mulaiJadwal"][intval($tanggal)]=$r[mulaiJadwal];
			$arr["selesaiJadwal"][intval($tanggal)]=$r[selesaiJadwal];
			$arr["keteranganJadwal"][intval($tanggal)]=$r[keteranganJadwal];
		}
				
		$pdf->SetWidths(array(10, 30, 60, 20, 20, 60));
		$pdf->SetAligns(array('C','C','C','C','C','C'));
		$pdf->Row(array("NO.\tb","TANGGAL\tb","SHIFT\tb","MASUK\tb","KELUAR\tb","KETERANGAN\tb"));
		$pdf->SetAligns(array('C','C','L','C','C','L'));
		

		$arrShift=arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");			
		ksort($arrShift);
		reset($arrShift);
		
		list($normalShift, $mulaiNormal, $selesaiNormal)=explode("\t", getField("select concat(namaShift,'\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift limit 1"));
		
		
		for($i=1; $i<=$day; $i++){			
			$week = date("w", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
			$color = in_array($week, array(0,6)) ? "#f2dbdb" : "#ffffff";
							
			$arrShift["".$arr[idShift][$i].""] = empty($arr[idShift][$i]) ? $normalShift : $arrShift["".$arr[idShift][$i].""];$arr[mulaiJadwal][$i] = empty($arr[idShift][$i])  ? $mulaiNormal : $arr[mulaiJadwal][$i];
			$arr[selesaiJadwal][$i] = empty($arr[idShift][$i])  ? $selesaiNormal : $arr[selesaiJadwal][$i];				
			
			$pdf->Row(array(
				$i.".",
				str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal],
				$arrShift["".$arr[idShift][$i].""],
				substr($arr[mulaiJadwal][$i],0,5),
				substr($arr[selesaiJadwal][$i],0,5),
				$arr[keteranganJadwal][$i]));
			
		}
		
		$pdf->AutoPrint(true);
		$pdf->Output();	
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){						
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
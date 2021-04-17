<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		if (!in_array($_GET['pSearch'], array("-- ALL")))
			$sWhere.= " and upper(trim(t2.posisiPeserta))='".$_GET['pSearch']."'";
		
		$sql="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, upper(trim(t1.judulPelatihan)) as judulPelatihan, t1.kodePelatihan, t1.mulaiPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$jamPelatihan = $menitPelatihan = 0;
			list($pertemuanPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$totalPertemuan+= $pertemuanPelatihan;
			$totalJam+= $jamPelatihan;
			$totalMenit+= $menitPelatihan;	
			$totalPeserta+= $r[jumlahPeserta];						
		}
		
		$totalJam = $totalJam + floor($totalMenit/60);
		$totalMenit = $totalMenit%60;			
		$totalPelatihan = $totalMenit > 0 ?
		getAngka($totalJam)." Jam ".getAngka($totalMenit)." Menit":
		getAngka($totalJam)." Jam";
		
		return $totalPertemuan."\t".$totalPelatihan."\t".$totalPeserta;
	}
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		if (!in_array($_GET['pSearch'], array("-- ALL")))
			$sWhere.= " and upper(trim(t2.posisiPeserta))='".$_GET['pSearch']."'";
		
		$arrOrder = array(	
			"t1.mulaiPelatihan",
			"upper(trim(t2.posisiPeserta))",
			"t1.judulPelatihan",
			"t1.kodePelatihan",
			"t1.mulaiPelatihan",
			"t1.mulaiPelatihan",
			"t1.mulaiPelatihan",
			"count(t2.idPeserta)",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		$sql="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, upper(trim(t1.judulPelatihan)) as judulPelatihan, t1.kodePelatihan, t1.mulaiPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2 order by $orderBy $sLimit";		
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from (
				select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, upper(trim(t1.judulPelatihan)) as judulPelatihan, t1.kodePelatihan, t1.mulaiPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2
			) as dta"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$jamPelatihan = $menitPelatihan = 0;
			list($pertemuanPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[namaDepartemen]."</div>",
				"<div align=\"left\">".$r[judulPelatihan]."</div>",
				"<div align=\"left\">".$r[kodePelatihan]."</div>",
				"<div align=\"center\">".getTanggal($r[mulaiPelatihan])."</div>",
				"<div align=\"right\">".$waktuPelatihan."</div>",				
				"<div align=\"right\">".getAngka($pertemuanPelatihan)." Kali</div>",									
				"<div align=\"right\">".getAngka($r[jumlahPeserta])." Orang</div>",				
			);
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
		$arrDepartemen = arrayQuery("select upper(trim(posisiPeserta)) from plt_pelatihan_peserta group by 1");
		$arrDepartemen[-1] = "-- ALL";
		
		$pSearch = empty($_GET[pSearch]) ? "-- ALL" : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? date('01/01/Y') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];
		$text = table(8, array(6,7));
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Departemen : ".comboArray("pSearch", $arrDepartemen, $pSearch,"onchange=\"gData('".getPar($par, "mode")."');\"", "310px","chosen-select")."
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Periode : </td>
				<td><input type=\"text\" id=\"mSearch\" name=\"mSearch\" size=\"10\" maxlength=\"10\" value=\"".$mSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>
				<td>s.d</td>
				<td><input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>								
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value + '&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th style=\"width:30px; vertical-align:middle;\">No.</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Departemen</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Pelatihan</th>
					<th style=\"width:100px; vertical-align:middle;\">Kode</th>
					<th style=\"width:100px; vertical-align:middle;\">Tanggal Pelaksanaan</th>
					<th style=\"width:100px; vertical-align:middle;\">Total Jam</th>
					<th style=\"width:100px; vertical-align:middle;\">Jumlah Pertemuan</th>
					<th style=\"width:100px; vertical-align:middle;\">Jumlah Peserta</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPertemuan\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPelatihan\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPeserta\">&nbsp;</td>
				</tr>
			</tfoot>
			</table>
			</div>
			<script>
				gData('".getPar($par, "mode")."');
			</script>";
			
		if($par[mode] == "xls"){
			xls();
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
			
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
		require_once 'plugins/PHPExcel.php';
			
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Laporan Pelaksanaan Pelatihan Tahunan"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Departemen : ".getField("select namaData from mst_data where kodeData='".$_GET[pSearch]."'"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'PELATIHAN');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'KODE');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TANGGAL PELAKSANAAN');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TOTAL JAM');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'JUMLAH PERTEMUAN');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'JUMLAH PESERTA');
								
		$rows = 5;
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		if (!in_array($_GET['pSearch'], array("-- ALL")))
			$sWhere.= " and upper(trim(t2.posisiPeserta))='".$_GET['pSearch']."'";
		
		$sql="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, upper(trim(t1.judulPelatihan)) as judulPelatihan, t1.kodePelatihan, t1.mulaiPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2 order by t1.mulaiPelatihan";
		$res=db($sql);
		$no=1;
		while($r=mysql_fetch_array($res)){
			
			$jamPelatihan = $menitPelatihan = 0;
			list($pertemuanPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";		
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaDepartemen]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[judulPelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[kodePelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[mulaiPelatihan]));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $waktuPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($pertemuanPelatihan)." Kali");
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($r[jumlahPeserta])." Orang");
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$totalPertemuan+= $pertemuanPelatihan;
			$totalJam+= $jamPelatihan;
			$totalMenit+= $menitPelatihan;	
			$totalPeserta+= $r[jumlahPeserta];
			
			$no++;
			$rows++;
		}
				
		$totalJam = $totalJam + floor($totalMenit/60);
		$totalMenit = $totalMenit%60;			
		$totalPelatihan = $totalMenit > 0 ?
		getAngka($totalJam)." Jam ".getAngka($totalMenit)." Menit":
		getAngka($totalJam)." Jam";
		
		$objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");		
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $totalPelatihan);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($totalPertemuan)." Kali");
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($totalPeserta)." Orang");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("Laporan");
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;
			case "get":
				$text=gData();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
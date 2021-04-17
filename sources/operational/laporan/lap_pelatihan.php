<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";

	function gData(){
		global $s,$inp,$par;
		
		$sWhere= " where t1.statusPelatihan='t'";	
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";

		$tHadir = $tAbsen = 0;
		$sql="select * from (select t4.name, t4.dept_id, t2.*, t3.statusAbsensi, t3.keteranganAbsensi from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 join dta_pegawai t4 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal and t3.idPeserta=t4.id) $sWhere) as d1 left join mst_data d2 on (d1.dept_id=d2.kodeData) order by idPelatihan";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){		
			$tHadir+= $r[statusAbsensi] == "t" ? 1 : 0;
			$tAbsen+= $r[statusAbsensi] == "t" ? 0 : 1;
		}
		
		$sql="select t1.mulaiPelatihan, t1.lokasiPelatihan, t2.namaTrainer from plt_pelatihan t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $sWhere";
		$res=db($sql);
		$r=mysql_fetch_array($res);

		return getTanggal($r[mulaiPelatihan], "t")."&nbsp;\t".$r[namaTrainer]."&nbsp;\t".$r[lokasiPelatihan]."&nbsp;\t".getAngka($tHadir)."\t".getAngka($tAbsen);
	}


	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

		$sWhere= " where t1.statusPelatihan='t'";	
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				

		$arrOrder = array(
			"d1.name",
			"d1.name",
			"d2.namaData",
			"d1.mulaiJadwal",
			"d1.statusAbsensi",
			"d1.statusAbsensi",
			"d1.keteranganAbsensi",
			);

		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		$sql="select * from (select t4.name, t4.dept_id, t2.*, t3.statusAbsensi, t3.keteranganAbsensi from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 join dta_pegawai t4 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal and t3.idPeserta=t4.id) $sWhere) as d1 left join mst_data d2 on (d1.dept_id=d2.kodeData) order by $orderBy $sLimit";
		$res=db($sql);

		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from (select t4.name, t4.dept_id, t2.*, t3.statusAbsensi, t3.keteranganAbsensi from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 join dta_pegawai t4 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal and t3.idPeserta=t4.id) $sWhere) as d1 left join mst_data d2 on (d1.dept_id=d2.kodeData)"),
			"aaData" => array(),
			);

		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$hadirAbsensi = $r[statusAbsensi] == "t" ? 1 : 0;
			$tidakAbsensi = $r[statusAbsensi] == "t" ? 0 : 1;

			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".strtoupper($r[name])."</div>",				
				"<div align=\"left\">".$r[namaData]."</div>",
				"<div align=\"center\">".substr($r[mulaiJadwal],0,5)." - ".substr($r[selesaiJadwal],0,5)."</div>",
				"<div align=\"center\">".$hadirAbsensi."</div>",
				"<div align=\"center\">".$tidakAbsensi."</div>",
				"<div align=\"left\">".$r[keteranganAbsensi]."</div>",
				);		
			
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

		$pSearch = empty($_GET[pSearch]) ? getField("select idPelatihan from plt_pelatihan where statusPelatihan='t' order by judulPelatihan limit 1") : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? date('01/m/Y') : $_GET[mSearch];		
		$text = table(7);

		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."			
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			<div id=\"general\" class=\"subcontent\">	
				<table style=\"width:100%\">
					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small\">Tanggal</label>
								<span class=\"field\" id=\"tanggalPelatihan\">&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\">Trainee</label>						
								<span class=\"field\" id=\"namaTrainer\">&nbsp;</span>
							</p>
						</td>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small\">Pelatihan</label>
								<div class=\"field\">
									".comboData("select * from plt_pelatihan where statusPelatihan='t' order by judulPelatihan","idPelatihan","judulPelatihan","pSearch","",$pSearch,"onchange=\"gData('".getPar($par,"mode")."');\"", "90%")."
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Lokasi</label>
								<span class=\"field\" id=\"lokasiPelatihan\">&nbsp;</span>
							</p>
						</td>
					</tr>
				</table>
			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th style=\"width:30px; vertical-align:middle;\">No.</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Nama</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Departemen</th>					
					<th style=\"width:100px; vertical-align:middle;\">Jam</th>
					<th style=\"width:100px; vertical-align:middle;\">Present</th>
					<th style=\"width:100px; vertical-align:middle;\">Absen</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Remarks</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>								
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tHadir\">&nbsp;</td>	
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tAbsen\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody></tbody>
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

		$sWhere= " where t1.statusPelatihan='t'";	
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";
		
		$sql="select t1.mulaiPelatihan, t1.judulPelatihan, t1.lokasiPelatihan, t2.namaTrainer from plt_pelatihan t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $sWhere";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
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
		

		// $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		

		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
		$objPHPExcel->getActiveSheet()->mergeCells('C3:G3');
		$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:G4');

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Rekapitulasi Training Per Pelatihan"));
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "TANGGAL :" .getTanggal($r[mulaiPelatihan],"t"));
		$objPHPExcel->getActiveSheet()->setCellValue('A4', "TRAINEE : ".$r[namaTrainer]);
		$objPHPExcel->getActiveSheet()->setCellValue('C3', "PELATIHAN : ".$r[judulPelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('C4', "LOKASI : ".$r[lokasiPelatihan]);

		$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		

		$objPHPExcel->getActiveSheet()->setCellValue('A6', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B6', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C6', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('D6', 'JAM');
		$objPHPExcel->getActiveSheet()->setCellValue('E6', 'PRESENT');
		$objPHPExcel->getActiveSheet()->setCellValue('F6', 'ABSEN');
		$objPHPExcel->getActiveSheet()->setCellValue('G6', 'REMARKS');


		$rows = 7;		
		$sWhere= " where t1.statusPelatihan='t'";		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idPelatihan='".$_GET['pSearch']."'";				

		$sql="select * from (select t4.name, t4.dept_id, t2.*, t3.statusAbsensi, t3.keteranganAbsensi from plt_pelatihan t1 join plt_pelatihan_jadwal t2 join plt_pelatihan_absensi t3 join dta_pegawai t4 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idJadwal=t3.idJadwal and t3.idPeserta=t4.id) $sWhere) as d1 left join mst_data d2 on (d1.dept_id=d2.kodeData) order by name";
		$res=db($sql);

		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$hadirAbsensi = $r[statusAbsensi] == "t" ? 1 : 0;
			$tidakAbsensi = $r[statusAbsensi] == "t" ? 0 : 1;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[namaData]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, substr($r[mulaiJadwal],0,5)." - ".substr($r[selesaiJadwal],0,5));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $hadirAbsensi);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $tidakAbsensi);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[keteranganAbsensi]);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$totalHadir+=$hadirAbsensi;
			$totalAbsen+=$tidakAbsensi;
			$rows++;
		}

		$objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");		
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totalHadir);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $totalAbsen);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':G'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		
		$objPHPExcel->getActiveSheet()->getStyle('A6:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A6:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B6:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C6:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D6:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E6:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F6:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G6:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
			$text = gData();
			break;

			default:
			$text = lihat();
			break;
		}
		return $text;
	}	
?>
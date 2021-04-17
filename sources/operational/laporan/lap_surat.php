<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	
	function gData(){
		global $s,$inp,$par;
		$sql="select t1.lokasiPelatihan, t2.namaTrainer from plt_pelatihan t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) where t1.idPelatihan='".$_GET[idPelatihan]."'";
		$res=db($sql);
		$data=mysql_fetch_array($res);
				
		return json_encode($data);
	}
		
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= " where t1.statusPelatihan='t'";
		
		$arrJadwal = arrayQuery("select t1.idPelatihan, concat(count(t2.idJadwal), '\t', sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1");
		$arrAbsensi = arrayQuery("select t1.idPelatihan, t2.idPeserta, count(t2.statusAbsensi) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere and t2.statusAbsensi='t' group by 1,2");		
		
		$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $sWhere order by t1.idPelatihan");		
		$cntEvaluasi = arrayQuery("select idPelatihan, count(*) from plt_pertanyaan_evaluasi where idPegawai='".$_GET['pSearch']."' group by 1");
		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t2.idPegawai='".$_GET['pSearch']."'";				
		
		$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $sWhere order by t1.idPelatihan";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrJawaban = explode("\t", $r[evaluasiJawaban]);
			$nilaiPelatihan = 0;
			if(is_array($arrJawaban)){
				reset($arrJawaban);
				while(list($i,$d)=each($arrJawaban)){
					$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
					$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
					$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
				}
			}			
			$arrNilai["$r[idPelatihan]"]+= $nilaiPelatihan;			
			$cntKategori["$r[idPelatihan]"]["$r[idKategori]"]=$r[idKategori];
		}
		
		
		$arrOrder = array(
			"t1.mulaiPelatihan",
			"t1.kodePelatihan",
			"t1.judulPelatihan",
			"t1.lokasiPelatihan",
			"t1.mulaiPelatihan",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		$sql="select * from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere"),
			"aaData" => array(),
		);						

		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;			
			
			list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal["".$r[idPelatihan].""]);
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";
			$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan > 1 ? getAngka($menitPelatihan)." Menit": $waktuPelatihan;
			$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan < 1 ? "" : $waktuPelatihan;
			
			$nilaiPelatihan = $cntEvaluasi["$r[idPelatihan]"] > 0 ? getAngka(round($arrNilai["$r[idPelatihan]"] / $cntEvaluasi["$r[idPelatihan]"])) : "-";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".$r[kodePelatihan]."</div>",				
				"<div align=\"left\">".$r[judulPelatihan]."</div>",
				"<div align=\"left\">".$r[lokasiPelatihan]."</div>",
				"<div align=\"center\">".getTanggal($r[mulaiPelatihan])."</div>",
				"<div align=\"center\">".$waktuPelatihan."</div>",
				"<div align=\"right\">".$jmlPelatihan." Kali</div>",
				"<div align=\"right\">".getAngka($arrAbsensi["".$r[idPelatihan].""]["".$r[idPeserta].""])." Kali</div>",
				"<div align=\"left\">&nbsp;</div>",
				"<div align=\"center\">".$nilaiPelatihan."</div>",
				
			);		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$pSearch = empty($_GET[pSearch]) ? getField("select idPelatihan from plt_pelatihan where statusPelatihan='t' order by judulPelatihan limit 1") : $_GET[pSearch];
		
		$sql="select * from plt_pelatihan t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) where t1.idPelatihan='".$pSearch."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		list($tahunPelatihan) = explode("-", $r[mulaiPelatihan]);
		
		$text = table(6, array(1,2,3,4,5,6));
		
		$text.="<script>
				function getPelatihan(getPar){
					idPelatihan = document.getElementById('pSearch').value;
					
					var xmlHttp = getXMLHttp();
					xmlHttp.onreadystatechange = function(){	
						if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
							response = xmlHttp.responseText.trim();			
							if(response){				
								var data = JSON.parse(response);
								document.getElementById('namaTrainer').innerHTML = data['namaTrainer'] == undefined ? '' : data['namaTrainer'];
								document.getElementById('lokasiPelatihan').innerHTML = data['lokasiPelatihan'] == undefined ? '' : data['lokasiPelatihan'];
							}
						}
					}	
					xmlHttp.open('GET', 'ajax.php?par[mode]=get&idPelatihan=' + idPelatihan + getPar, true);
						
					xmlHttp.send(null);
					return false;
				}
			</script>
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."			
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value + '&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
				
			</div>
			<div id=\"general\" class=\"subcontent\">	
				<table style=\"width:100%\">
				<tr>
				<td style=\"width:60%\">
					<p>
						<label class=\"l-input-small\">TAHUN</label>
						<span class=\"field\" id=\"tahunPelatihan\">".$tahunPelatihan."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">PELATIHAN</label>
						<div class=\"field\">
							".comboData("select * from plt_pelatihan where statusPelatihan='t' order by judulPelatihan","idPelatihan","judulPelatihan","pSearch","",$pSearch,"onchange=\"getPelatihan('".getPar($par,"mode")."');\"", "90%","chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">DEPARTEMEN</label>
						<span class=\"field\" id=\"namaDepartemen\">".getField("select namaData from mst_data where kodeData='".$r[idKategori]."'")."&nbsp;</span>
					</p>					
				</td>
				<td style=\"width:40%\">					
					&nbsp;
				</td>
				</tr>
				</table>
			</div>
			
			<div id=\"general\" class=\"subcontent\">	
				<table style=\"width:100%\">
				<tr>
				<td style=\"width:60%\">
					<p>
						<label class=\"l-input-small\">TANGGAL</label>
						<span class=\"field\" id=\"tanggalPelatihan\">".getTanggal($r[mulaiPelatihan],"t")."&nbsp;</span>
					</p>	
					<p>
						<label class=\"l-input-small\">CC</label>
						<span class=\"field\" id=\"ccPelatihan\">Yth. Manager&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">TO</label>
						<span class=\"field\" id=\"toPelatihan\">&nbsp;</span>
					</p>
				</td>
				<td style=\"width:40%\">					
					&nbsp;
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
					<th style=\"min-width:200px; vertical-align:middle;\">Nama</th>
					<th style=\"min-width:200px; vertical-align:middle;\">Posisi</th>					
					<th style=\"width:30px; vertical-align:middle;\">No.</th>
					<th style=\"min-width:200px; vertical-align:middle;\">Nama</th>
					<th style=\"min-width:200px; vertical-align:middle;\">Posisi</th>					
				</tr>
			</thead>
			<tbody></tbody>
			</table>
			<form class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"general\" class=\"subcontent\">	
				<table style=\"width:100%\">
				<tr>
				<td style=\"width:60%\">
					<p>
						<label class=\"l-input-small\">DIHARAPKAN DATANG DI</label>
						<span class=\"field\" id=\"lokasiPelatihan\">".$r[lokasiPelatihan]."&nbsp;</span>
					</p>	
					<p>
						<label class=\"l-input-small\">HARI/TANGGAL/WAKTU</label>
						<span class=\"field\" id=\"waktuPelatihan\">".getTanggal($r[mulaiPelatihan], "t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">TEMPAT</label>
						<span class=\"field\" id=\"tempatPelatihan\">&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">HEAD DEPARTEMENT</label>
						<span class=\"field\" id=\"headPelatihan\">&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">APPROVED BY</label>
						<span class=\"field\" id=\"tempatPelatihan\">&nbsp;</span>
					</p>
				</td>
				<td style=\"width:40%\">					
					<p>
						<label class=\"l-input-small\">TOPIK PELATIHAN</label>
						<span class=\"field\" id=\"judulPelatihan\">".$r[judulPelatihan]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">TIPE KEGIATAN</label>
						<span class=\"field\" id=\"tipePelatihan\">&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">FASILITATOR</label>
						<span class=\"field\" id=\"fasilitatorPelatihan\">".$r[namaTrainer]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">ACKNOWLEDGED BY</label>
						<span class=\"field\" id=\"tipePelatihan\">&nbsp;</span>
					</p>
				</td>
				</tr>
				</table>
			</div>
			</form>
			</div>";
			
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
		
		if (!empty($_GET['pSearch']))
			$sWhere.= " and t1.idKategori='".$_GET['pSearch']."'";				
		
		$sql="select * from plt_pelatihan t1 join mst_data t2 on (t1.idKategori=t2.kodeData) $sWhere order by t1.mulaiPelatihan";
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
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaData]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[judulPelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[kodePelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[mulaiPelatihan]));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $waktuPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, getAngka($pertemuanPelatihan)." Kali");
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($r[pesertaPelatihan])." Orang");
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$no++;
			$rows++;
		}
		
		$rows--;
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
				$text = gData();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
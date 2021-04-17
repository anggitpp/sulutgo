<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/export/";

function gData(){
	global $s,$inp,$par;		
	$sql="select * from dta_pegawai where id='".$_GET[idPegawai]."'";
	$res=db($sql);
	$data=mysql_fetch_array($res);

	$data[namaDivisi] = getField("select namaData from mst_data where kodeData='".$data[div_id]."'");
	$data[namaDepartemen] = getField("select namaData from mst_data where kodeData='".$data[dept_id]."'");

	if(empty($data[reg_no])) $data[reg_no] = "&nbsp;";
	if(empty($data[pos_name])) $data[pos_name] = "&nbsp;";
	if(empty($data[namaDivisi])) $data[namaDivisi] = "&nbsp;";
	if(empty($data[namaDepartemen])) $data[namaDepartemen] = "&nbsp;";

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
		$jamPelatihan = $menitPelatihan = 0;
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
	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

	$pSearch = empty($_GET[pSearch]) ? getField("select id from emp where status='".$status."' order by name limit 1") : $_GET[pSearch];		
	$text = table(10, array(6,7,8,9,10));

	$sql="select * from dta_pegawai where id='".$pSearch."'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$text.="<script>
	function getPegawai(getPar){
		idPegawai = document.getElementById('pSearch').value;

		var xmlHttp = getXMLHttp();
		xmlHttp.onreadystatechange = function(){	
			if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
				response = xmlHttp.responseText.trim();			
				if(response){				
					var data = JSON.parse(response);
					document.getElementById('nikPegawai').innerHTML = data['reg_no'] == undefined ? '' : data['reg_no'];
					document.getElementById('namaDivisi').innerHTML = data['namaDivisi'] == undefined ? '' : data['namaDivisi'];
					document.getElementById('namaJabatan').innerHTML = data['pos_name'] == undefined ? '' : data['pos_name'];
					document.getElementById('namaDepartemen').innerHTML = data['namaDepartemen'] == undefined ? '' : data['namaDepartemen'];
				}
			}
		}	
		xmlHttp.open('GET', 'ajax.php?par[mode]=get&idPegawai=' + idPegawai + getPar, true);

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
			<a href=\"#\" onclick=\"window.location='?par[mode]=xls&pSearch=' + document.getElementById('pSearch').value +'".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>

		</div>
		<div id=\"general\" class=\"subcontent\">	
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\">PEGAWAI</label>
							<div class=\"field\">
								".comboData("select id, upper(name) as name from emp where status='".$status."' order by name","id","name","pSearch","",$pSearch,"onchange=\"getPegawai('".getPar($par,"mode")."');\"", "90%","chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<span class=\"field\" id=\"nikPegawai\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">DIVISI</label>						
							<span class=\"field\" id=\"namaDivisi\">".getField("select namaData from mst_data where kodeData='".$r[div_id]."'")."&nbsp;</span>
						</p>
					</td>
					<td style=\"width:50%\">					
						<p>
							<label class=\"l-input-small\" style=\"background:#fff;\">&nbsp;</label>
							<span class=\"field\" style=\"border:solid 1px #fff;\">&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">JABATAN</label>
							<span class=\"field\" id=\"namaJabatan\">".$r[pos_name]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">DEPARTEMEN</label>						
							<span class=\"field\" id=\"namaDepartemen\">".getField("select namaData from mst_data where kodeData='".$r[dept_id]."'")."&nbsp;</span>
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
				<th style=\"max-width:200px; vertical-align:middle;\">Training ID</th>
				<th style=\"min-width:200px; vertical-align:middle;\">Nama Training</th>					
				<th style=\"width:100px; vertical-align:middle;\">Lokasi</th>
				<th style=\"width:100px; vertical-align:middle;\">Tanggal</th>
				<th style=\"width:100px; vertical-align:middle;\">Jam</th>
				<th style=\"width:100px; vertical-align:middle;\">Jml Pertemuan</th>
				<th style=\"width:100px; vertical-align:middle;\">Jml Hadir</th>
				<th style=\"width:100px; vertical-align:middle;\">Lulus</th>
				<th style=\"width:100px; vertical-align:middle;\">Nilai</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
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
			
	$sql_ = "SELECT * FROM dta_pegawai WHERE id='".$_GET[pSearch]."'";
	$res_ = db($sql_);
	$r = mysql_fetch_array($res_);
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(50);		
		
	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
	$objPHPExcel->getActiveSheet()->mergeCells('D3:E3');	
	$objPHPExcel->getActiveSheet()->mergeCells('D4:E4');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Catatan Pelatihan Individual"));
	$objPHPExcel->getActiveSheet()->setCellValue('A2', "PEGAWAI : ".strtoupper(getField("select name from dta_pegawai where id='".$_GET[pSearch]."'")));
	$objPHPExcel->getActiveSheet()->setCellValue('A3', "NPP : ".getField("select reg_no from emp where id='".$_GET[pSearch]."'"));
	$objPHPExcel->getActiveSheet()->setCellValue('A4', "DIVISI : ".getField("select namaData from mst_data where kodeData='".$r[div_id]."'"));
	$objPHPExcel->getActiveSheet()->setCellValue('D3', "JABATAN : ".getField("select pos_name from dta_pegawai where id='".$_GET[pSearch]."'"));
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "DEPARTEMEN : ".getField("select namaData from mst_data where kodeData='".$r[dept_id]."'"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		
		
	$objPHPExcel->getActiveSheet()->setCellValue('A5', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B5', 'TRAINING ID');
	$objPHPExcel->getActiveSheet()->setCellValue('C5', 'NAMA TRAINING');
	$objPHPExcel->getActiveSheet()->setCellValue('D5', 'LOKASI');
	$objPHPExcel->getActiveSheet()->setCellValue('E5', 'TANGGAL');
	$objPHPExcel->getActiveSheet()->setCellValue('F5', 'JAM');
	$objPHPExcel->getActiveSheet()->setCellValue('G5', 'JUMLAH PERTEMUAN');
	$objPHPExcel->getActiveSheet()->setCellValue('H5', 'JUMLAH HADIR');
	
	$objPHPExcel->getActiveSheet()->setCellValue('I5', 'LULUS');
	$objPHPExcel->getActiveSheet()->setCellValue('J5', 'NILAI');

		$rows = 6;
		
$sWhere= " where t1.statusPelatihan='t'";

	$arrJadwal = arrayQuery("select t1.idPelatihan, concat(count(t2.idJadwal), '\t', sum(hour(timediff(t2.selesaiJadwal, t2.mulaiJadwal))), '\t', sum(minute(timediff(t2.selesaiJadwal, t2.mulaiJadwal)))) from plt_pelatihan t1 join plt_pelatihan_jadwal t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1");
	$arrAbsensi = arrayQuery("select t1.idPelatihan, t2.idPeserta, count(t2.statusAbsensi) from plt_pelatihan t1 join plt_pelatihan_absensi t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere and t2.statusAbsensi='t' group by 1,2");		

	$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $sWhere order by t1.idPelatihan");		
	$cntEvaluasi = arrayQuery("select idPelatihan, count(*) from plt_pertanyaan_evaluasi where idPegawai='".$_GET['pSearch']."' group by 1");

	if (!empty($_GET['pSearch']))
		$sWhere.= " and t2.idPegawai='".$_GET['pSearch']."'";				

	$sql_="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $sWhere order by t1.idPelatihan";
	$res=db($sql_);

	$sql="select * from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere";
	// echo $sql;
	$res=db($sql);
	$no=1;
	while($r=mysql_fetch_array($res)){


			list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", $arrJadwal["".$r[idPelatihan].""]);
		$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
		$menitPelatihan = $menitPelatihan%60;			
		$waktuPelatihan = $menitPelatihan > 0 ?
		getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
		getAngka($jamPelatihan)." Jam";
		$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan > 1 ? getAngka($menitPelatihan)." Menit": $waktuPelatihan;
		$waktuPelatihan = $jamPelatihan < 1 && $menitPelatihan < 1 ? "" : $waktuPelatihan;

		$nilaiPelatihan = $cntEvaluasi["$r[idPelatihan]"] > 0 ? getAngka(round($arrNilai["$r[idPelatihan]"] / $cntEvaluasi["$r[idPelatihan]"])) : "-";

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[kodePelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[judulPelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[lokasiPelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[mulaiPelatihan]));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $waktuPelatihan);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $jmlPelatihan." Kali");
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($arrAbsensi["".$r[idPelatihan].""]["".$r[idPeserta].""])." Kali");
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, " ");
		
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $nilaiPelatihan);

			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$no++;
			$rows++;
		}
		
		$rows--;
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B5:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C5:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D5:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E5:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F5:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G5:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H5:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('I5:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('J5:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
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
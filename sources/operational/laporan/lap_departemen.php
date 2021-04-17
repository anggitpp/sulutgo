<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";

	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
			
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$jamPelatihan = $menitPelatihan = 0;
			list($jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r_[idPelatihan]."'"));
					
			$totalJam+= $jamPelatihan;
			$totalMenit+= $menitPelatihan;	
			$jmlPelatihan++;
			$jmlPeserta+=$r_[jumlahPeserta];						
		}
		
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' and t3.idJadwal>0 group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$cntAbsensi++;
			$jmlAbsensi+=$r_[jumlahAbsensi];
		}
		
		$jmlHadir = $cntAbsensi > 0 ? ceil($jmlAbsensi / $cntAbsensi) : "";
		$jmlAbsen = $jmlPeserta - $jmlHadir;
		$prsHadir = $jmlPeserta > 0 ? getAngka($jmlHadir / $jmlPeserta * 100)."%" : "";
		$prsAbsen = $jmlPeserta > 0 ? getAngka($jmlAbsen / $jmlPeserta * 100)."%" : "";
		if($prsHadir == "0%") $prsHadir = "";
		if($prsAbsen == "0%") $prsAbsen = "";		
		
		$totalJam = $totalJam + floor($totalMenit/60);
		$totalMenit = $totalMenit%60;			
		$waktuPelatihan = $totalMenit > 0 ?
		getAngka($totalJam)." Jam ".getAngka($totalMenit)." Menit":
		getAngka($totalJam)." Jam";
		
		$jmlAverage = $jmlPeserta > 0 ? round(($totalJam * 60 + $totalMenit) / $jmlPeserta) : 0;
		$jamAverage = floor($jmlAverage/60);
		$menitAverage = $jmlAverage%60;			
		$avgPelatihan = $menitAverage > 0 ?
		getAngka($jamAverage)." Jam ".getAngka($menitAverage)." Menit":
		getAngka($jamAverage)." Jam";
		$avgPelatihan = $jamAverage < 1 && $menitAverage > 1 ? getAngka($menitAverage)." Menit" : $avgPelatihan;
		$avgPelatihan = $jamAverage < 1 && $menitAverage < 1 ? "" : $avgPelatihan;
		
		if($jmlPelatihan > 0) return $jmlPelatihan."\t".$jmlPeserta."\t".$jmlHadir."\t".$prsHadir."\t".$jmlAbsen."\t".$prsAbsen."\t".$waktuPelatihan."\t".$avgPelatihan;
	}
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$arrParameter;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		$arrOrder = array(	
			"upper(trim(t2.posisiPeserta))",			
			"upper(trim(t2.posisiPeserta))",			
			);

		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select upper(trim(t2.posisiPeserta)) as namaDepartemen from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1 order by $orderBy $sLimit";
		$res=db($sql);

		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from (
				select upper(trim(t2.posisiPeserta)) as namaDepartemen from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1
			) as t"),
			"aaData" => array(),
			);		
		
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$jamPelatihan = $menitPelatihan = 0;
			list($jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r_[idPelatihan]."'"));
					
			$arrJam["".$r_[namaDepartemen].""]+= $jamPelatihan;
			$arrMenit["".$r_[namaDepartemen].""]+= $menitPelatihan;
			$cntPelatihan["".$r_[namaDepartemen].""]++;
			$arrPeserta["".$r_[namaDepartemen].""]+=$r_[jumlahPeserta];						
		}
		
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' and t3.idJadwal>0 group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$cntAbsensi["".$r_[namaDepartemen].""]++;
			$arrAbsensi["".$r_[namaDepartemen].""]+=$r_[jumlahAbsensi];
		}
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$jmlPelatihan = $cntPelatihan["".$r[namaDepartemen].""];
			$jmlPelatihan = $jmlPelatihan > 0 ? getAngka($jmlPelatihan)." Kali" : "";
			
			$jmlPeserta = $arrPeserta["".$r[namaDepartemen].""];
			$jmlPeserta = $jmlPeserta > 0 ? getAngka($jmlPeserta)." Orang" : "";		
						
			$jmlHadir = $cntAbsensi["".$r[namaDepartemen].""] > 0 ? ceil($arrAbsensi["".$r[namaDepartemen].""] / $cntAbsensi["".$r[namaDepartemen].""]) : "";
			$jmlAbsen = $jmlPeserta - $jmlHadir;
			$prsHadir = $jmlPeserta > 0 ? getAngka($jmlHadir / $jmlPeserta * 100)."%" : "";
			$prsAbsen = $jmlPeserta > 0 ? getAngka($jmlAbsen / $jmlPeserta * 100)."%" : "";
			if($prsHadir == "0%") $prsHadir = "";
			if($prsAbsen == "0%") $prsAbsen = "";

			$jamPelatihan = $arrJam["".$r[namaDepartemen].""] + floor($arrMenit["".$r[namaDepartemen].""]/60);
			$menitPelatihan = $arrMenit["".$r[namaDepartemen].""]%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";
					
			$jmlAverage = $jmlPeserta > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlPeserta) : 0;
			$jamAverage = floor($jmlAverage/60);
			$menitAverage = $jmlAverage%60;			
			$avgPelatihan = $menitAverage > 0 ?
			getAngka($jamAverage)." Jam ".getAngka($menitAverage)." Menit":
			getAngka($jamAverage)." Jam";
			$avgPelatihan = $jamAverage < 1 && $menitAverage > 1 ? getAngka($menitAverage)." Menit" : $avgPelatihan;
			$avgPelatihan = $jamAverage < 1 && $menitAverage < 1 ? "" : $avgPelatihan;

			$data=array(
				"<div align=\"center\">".$no.".</div>",								
				"<div align=\"left\">".$r[namaDepartemen]."</div>",
				"<div align=\"right\">".$jmlPelatihan."</div>",
				"<div align=\"right\">".$jmlPeserta."</div>",
				"<div align=\"center\">".$jmlHadir."</div>",
				"<div align=\"center\">".$prsHadir."</div>",				
				"<div align=\"center\">".$jmlAbsen."</div>",
				"<div align=\"center\">".$prsAbsen."</div>",				
				"<div align=\"right\">".$waktuPelatihan."</div>",				
				"<div align=\"right\">".$avgPelatihan."</div>",			
				);
			
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

		$pSearch = empty($_GET[pSearch]) ? "" : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? date('01/01/Y') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];
		$text = table(10, array(3,4,5,6,7,8,9,10));

		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."			
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">			
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

				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>

			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"3\" style=\"width:30px; vertical-align:middle;\">No.</th>
					<th rowspan=\"3\" style=\"min-width:200px; vertical-align:middle;\">Departemen</th>
					<th rowspan=\"3\" style=\"max-width:100px; vertical-align:middle;\">Total Training</th>
					<th colspan=\"5\" style=\"vertical-align:middle;\">Participant Of Training</th>
					<th rowspan=\"3\" style=\"max-width:100px; vertical-align:middle;\">Total Training Duration</th>
					<th rowspan=\"3\" style=\"max-width:100px; vertical-align:middle;\">Average Duration/ Participant</th>				
				</tr>
				<tr>
					<th rowspan=\"2\" style=\"max-width:100px; vertical-align:middle;\">Total Participant</th>
					<th colspan=\"2\" style=\"max-width:100px; vertical-align:middle;\">Present</th>
					<th colspan=\"2\" style=\"max-width:100px; vertical-align:middle;\">Absent</th>
				</tr>
				<tr>
					<th style=\"width:50px; vertical-align:middle;\">Jml</th>
					<th style=\"width:50px; vertical-align:middle;\">%</th>
					<th style=\"width:50px; vertical-align:middle;\">Jml</th>
					<th style=\"width:50px; vertical-align:middle;\">%</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>								
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPelatihan\">&nbsp;</td>				
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPeserta\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tHadir\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pHadir\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tAbsen\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pAbsen\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tWaktu\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"aWaktu\">&nbsp;</td>
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
		$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);	

		$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:K3');

		$objPHPExcel->getActiveSheet()->mergeCells('A4:A6');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B6');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C6');

		$objPHPExcel->getActiveSheet()->mergeCells('D4:H4');
		$objPHPExcel->getActiveSheet()->mergeCells('D5:D6');

		$objPHPExcel->getActiveSheet()->mergeCells('E5:F5');
		$objPHPExcel->getActiveSheet()->mergeCells('G5:H5');

		$objPHPExcel->getActiveSheet()->mergeCells('I4:I6');
		$objPHPExcel->getActiveSheet()->mergeCells('J4:J6');
		$objPHPExcel->getActiveSheet()->mergeCells('K4:K6');


		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Laporan Pelaksanaan Per Departemen"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));	

		$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:K6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('A4:K4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:K4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A6:K6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		

		$objPHPExcel->getActiveSheet()->getStyle('A5:A6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('B5:B6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('C5:C6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('H5:H6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('I5:I6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('J5:J6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('K5:K6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		

		$objPHPExcel->getActiveSheet()->getStyle('D5:D6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('F5:F6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


		$objPHPExcel->getActiveSheet()->getStyle('K4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('I4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
		$objPHPExcel->getActiveSheet()->getStyle('J4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'TOTAL TRAINING');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'PARTICIPANT OF TRAINING');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'TOTAL PARTICIPANT');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'PRESENT');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'ABSEN');

		$objPHPExcel->getActiveSheet()->setCellValue('E6', 'JML');
		$objPHPExcel->getActiveSheet()->setCellValue('F6', '%');
		$objPHPExcel->getActiveSheet()->setCellValue('G6', 'JML');
		$objPHPExcel->getActiveSheet()->setCellValue('H6', '%');


		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'TOTAL TRAINING DURATION');
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'AVERAGE DURATION / PARTICIPANT');
		$objPHPExcel->getActiveSheet()->setCellValue('K4', 'REMARKS');

		$rows = 7;
		
		$pSearch = empty($_GET[pSearch]) ? "" : $_GET[pSearch];
		$mSearch = empty($_GET[mSearch]) ? date('01/m/Y') : $_GET[mSearch];
		$tSearch = empty($_GET[tSearch]) ? date('d/m/Y') : $_GET[tSearch];
		$text = table(11, array(3,4,5,6,7,8,9,10,11));

		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";
		
		$sql="select upper(trim(t2.posisiPeserta)) as namaDepartemen from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1 order by 1";
		$res=db($sql);
		
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t2.idPeserta) as jumlahPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 on (t1.idPelatihan=t2.idPelatihan) $sWhere group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$jamPelatihan = $menitPelatihan = 0;
			list($jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r_[idPelatihan]."'"));
					
			$arrJam["".$r_[namaDepartemen].""]+= $jamPelatihan;
			$arrMenit["".$r_[namaDepartemen].""]+= $menitPelatihan;
			$cntPelatihan["".$r_[namaDepartemen].""]++;
			$arrPeserta["".$r_[namaDepartemen].""]+=$r_[jumlahPeserta];						
			
			$totPelatihan++;
			$totPeserta+=$r_[jumlahPeserta];						
			$totalJam+= $jamPelatihan;
			$totalMenit+= $menitPelatihan;	
		}
		
		$sql_="select upper(trim(t2.posisiPeserta)) as namaDepartemen, t1.idPelatihan, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' group by 1,2";
		$res_=db($sql_);
		while($r_=mysql_fetch_array($res_)){
			$cntAbsensi["".$r_[namaDepartemen].""]++;
			$arrAbsensi["".$r_[namaDepartemen].""]+=$r_[jumlahAbsensi];
			
			$sumAbsensi++;
			$totAbsensi+=$r_[jumlahAbsensi];
		}

		$no=1;
		while($r=mysql_fetch_array($res)){			
			$jmlPelatihan = $cntPelatihan["".$r[namaDepartemen].""];
			$jmlPelatihan = $jmlPelatihan > 0 ? getAngka($jmlPelatihan)." Kali" : "";
			
			$jmlPeserta = $arrPeserta["".$r[namaDepartemen].""];
			$jmlPeserta = $jmlPeserta > 0 ? getAngka($jmlPeserta)." Orang" : "";		
			
			
			$jmlHadir = $cntAbsensi["".$r[namaDepartemen].""] > 0 ? ceil($arrAbsensi["".$r[namaDepartemen].""] / $cntAbsensi["".$r[namaDepartemen].""]) : "";
			$jmlAbsen = $jmlPeserta - $jmlHadir;
			$prsHadir = $jmlPeserta > 0 ? getAngka($jmlHadir / $jmlPeserta * 100)."%" : "";
			$prsAbsen = $jmlPeserta > 0 ? getAngka($jmlAbsen / $jmlPeserta * 100)."%" : "";
			if($prsHadir == "0%") $prsHadir = "";
			if($prsAbsen == "0%") $prsAbsen = "";			

			$jamPelatihan = $arrJam["".$r[namaDepartemen].""] + floor($arrMenit["".$r[namaDepartemen].""]/60);
			$menitPelatihan = $arrMenit["".$r[namaDepartemen].""]%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";
					
			$jmlAverage = $jmlPeserta > 0 ? round(($jamPelatihan * 60 + $menitPelatihan) / $jmlPeserta) : 0;
			$jamAverage = floor($jmlAverage/60);
			$menitAverage = $jmlAverage%60;			
			$avgPelatihan = $menitAverage > 0 ?
			getAngka($jamAverage)." Jam ".getAngka($menitAverage)." Menit":
			getAngka($jamAverage)." Jam";
			$avgPelatihan = $jamAverage < 1 && $menitAverage > 1 ? getAngka($menitAverage)." Menit" : $avgPelatihan;
			$avgPelatihan = $jamAverage < 1 && $menitAverage < 1 ? "" : $avgPelatihan;


			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaDepartemen]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $jmlPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $jmlPeserta);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jmlHadir);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $prsHadir);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $jmlAbsen);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $prsAbsen);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $waktuPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $avgPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, "");

			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$no++;
			$rows++;
		}

		$totHadir = $cntAbsensi > 0 ? ceil($totAbsensi / $sumAbsensi) : "";
		$totAbsen = $totPeserta - $totHadir;
		$prsHadir = $totPeserta > 0 ? getAngka($totHadir / $totPeserta * 100)."%" : "";
		$prsAbsen = $totPeserta > 0 ? getAngka($totAbsen / $totPeserta * 100)."%" : "";		
		if($prsAbsen == "0%") $prsAbsen = "";
		if($prsHadir == "0%") $prsHadir = "";
				
		$totalJam = $totalJam + floor($totalMenit/60);
		$totalMenit = $totalMenit%60;			
		$waktuPelatihan = $totalMenit > 0 ?
		getAngka($totalJam)." Jam ".getAngka($totalMenit)." Menit":
		getAngka($totalJam)." Jam";
		
		$jmlAverage = $totPeserta > 0 ? round(($totalJam * 60 + $totalMenit) / $totPeserta) : 0;
		$jamAverage = floor($jmlAverage/60);
		$menitAverage = $jmlAverage%60;			
		$avgPelatihan = $menitAverage > 0 ?
		getAngka($jamAverage)." Jam ".getAngka($menitAverage)." Menit":
		getAngka($jamAverage)." Jam";
		$avgPelatihan = $jamAverage < 1 && $menitAverage > 1 ? getAngka($menitAverage)." Menit" : $avgPelatihan;
		$avgPelatihan = $jamAverage < 1 && $menitAverage < 1 ? "" : $avgPelatihan;
		
		$objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $totPelatihan." Kali");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $totPeserta." Orang");
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totHadir);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $prsHadir);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $totAbsen);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $prsAbsen);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $waktuPelatihan);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $avgPelatihan);		

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$rows.':J'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getStyle('A1:K'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:K'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:K'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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
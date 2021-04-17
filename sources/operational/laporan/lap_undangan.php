<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";

	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
				
		if (!in_array($_GET['pSearch'], array("-- ALL")))
			$sWhere= " where upper(trim(namaDepartemen))='".$_GET['pSearch']."'";
		
		$sql="select * from (
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t2.posisiPeserta as namaDepartemen, 'peserta' as jenisPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 join emp t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
			union
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t3.pos_name as namaDepartemen, 'pelatih' as jenisPeserta from plt_pelatihan t1 join dta_trainer t2 join dta_pegawai t3 on (t1.idTrainer=t2.idTrainer and lower(trim(t2.namaTrainer))=lower(trim(t3.name))) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
		) as dta $sWhere order by idPelatihan";
		$res=db($sql);
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";		
		$arrAbsensi=arrayQuery("select t1.idPelatihan, t2.idPegawai, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' and t3.idJadwal>0 group by 1,2");
		
		while($r=mysql_fetch_array($res)){
			$jamPelatihan = $menitPelatihan = 0;
			list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			
			$jmlHadir = $r[jenisPeserta] == "peserta" ? $arrAbsensi["".$r[idPelatihan].""]["".$r[id].""] : $jmlPelatihan;
			$jmlAbsen = $jmlPelatihan - $jmlHadir;
			
			$pelatihJam+= $r[jenisPeserta] == "peserta" ? 0 : $jamPelatihan;
			$pelatihMenit+= $r[jenisPeserta] == "peserta" ? 0 : $menitPelatihan;
			$pelatihanJam+= $r[jenisPeserta] == "peserta" ? $jamPelatihan : 0;
			$pelatihanMenit+= $r[jenisPeserta] == "peserta" ? $menitPelatihan : 0;	
			$pelatihanJumlah+= $jmlPelatihan;
			$pelatihanHadir+= $jmlHadir;
			$pelatihanAbsen+= $jmlAbsen;
		}
		
		$pelatihJam = $pelatihJam + floor($pelatihMenit/60);
		$pelatihMenit = $pelatihMenit%60;			
		$totalPelatih = $pelatihMenit > 0 ?
		getAngka($pelatihJam)." Jam ".getAngka($pelatihMenit)." Menit":
		getAngka($pelatihJam)." Jam";
		
		$pelatihanJam = $pelatihanJam + floor($pelatihanMenit/60);
		$pelatihanMenit = $pelatihanMenit%60;			
		$totalPelatihan = $pelatihanMenit > 0 ?
		getAngka($pelatihanJam)." Jam ".getAngka($pelatihanMenit)." Menit":
		getAngka($pelatihanJam)." Jam";
		
		return $totalPelatihan."\t".$totalPelatih."\t".getAngka($pelatihanJumlah)."\t".getAngka($pelatihanHadir)."\t".getAngka($pelatihanAbsen);
	}

	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

		
		if (!in_array($_GET['pSearch'], array("-- ALL")))
				$sWhere= " where upper(trim(namaDepartemen))='".$_GET['pSearch']."'";

		$arrOrder = array(	
			"namaPegawai",			
			"kodePelatihan",
			"namaPegawai",			
			);

		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		$sql="select * from (
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t2.posisiPeserta as namaDepartemen, 'peserta' as jenisPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 join emp t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
			union
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t3.pos_name as namaDepartemen, 'pelatih' as jenisPeserta from plt_pelatihan t1 join dta_trainer t2 join dta_pegawai t3 on (t1.idTrainer=t2.idTrainer and lower(trim(t2.namaTrainer))=lower(trim(t3.name))) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
		) as dta $sWhere order by $orderBy $sLimit";
		$res=db($sql);

		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from (
				select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t2.posisiPeserta as namaDepartemen, 'peserta' as jenisPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 join emp t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
				union
				select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t3.pos_name as namaDepartemen, 'pelatih' as jenisPeserta from plt_pelatihan t1 join dta_trainer t2 join dta_pegawai t3 on (t1.idTrainer=t2.idTrainer and lower(trim(t2.namaTrainer))=lower(trim(t3.name))) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
			) as dta $sWhere"),
			"aaData" => array(),
			);

		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";		
		$arrAbsensi=arrayQuery("select t1.idPelatihan, t2.idPegawai, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' and t3.idJadwal>0 group by 1,2");
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$jamPelatihan = $menitPelatihan = 0;
			list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";				
			
			$waktuPelatih = $r[jenisPeserta] == "peserta" ? "" : $waktuPelatihan;
			$waktuPelatihan = $r[jenisPeserta] == "peserta" ? $waktuPelatihan : "";		
			
			$jmlHadir = $r[jenisPeserta] == "peserta" ? $arrAbsensi["".$r[idPelatihan].""]["".$r[id].""] : $jmlPelatihan;
			$jmlAbsen = $jmlPelatihan - $jmlHadir;
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",
				"<div align=\"left\">".$r[kodePelatihan]."</div>",
				"<div align=\"left\">".strtoupper($r[namaPegawai])."</div>",
				"<div align=\"right\">".$waktuPelatihan."</div>",
				"<div align=\"right\">".$waktuPelatih."</div>",
				"<div align=\"center\">".getAngka($jmlPelatihan)."</div>",
				"<div align=\"center\">".getAngka($jmlHadir)."</div>",
				"<div align=\"center\">".getAngka($jmlAbsen)."</div>",
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
		$text = table(8, array(4,5,6,7,8));

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
					<th style=\"width:100px; vertical-align:middle;\">ID Training</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Nama Pegawai</th>
					<th style=\"width:100px; vertical-align:middle;\">Training Hours</th>
					<th style=\"width:100px; vertical-align:middle;\">Trainer Hours</th>
					<th style=\"width:100px; vertical-align:middle;\">Total Invite</th>
					<th style=\"width:100px; vertical-align:middle;\">Total Present</th>
					<th style=\"width:100px; vertical-align:middle;\">Total Absent</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>								
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tPelatihan\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tPelatih\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tJumlah\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"tHadir\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:center;\" id=\"pAbsen\">&nbsp;</td>
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
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

		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Laporan Rekapitulasi Undangan Training, Training Hours, dan Trainners"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Departemen : ".getField("select namaData from mst_data where kodeData='".$_GET[pSearch]."'"));

		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		


		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'ID TRAINING');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TRAINING HOURS');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TRAINER HOURS');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TOTAL INVITE');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'TOTAL PRESENT');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'TOTAL ABSENT');

		$rows = 5;
		
		if (!in_array($_GET['pSearch'], array("-- ALL")))
			$sWhere= " where upper(trim(namaDepartemen))='".$_GET['pSearch']."'";
		
		$sql="select * from (
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t2.posisiPeserta as namaDepartemen, 'peserta' as jenisPeserta from plt_pelatihan t1 join plt_pelatihan_peserta t2 join emp t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPegawai=t3.id) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
			union
			select t1.idPelatihan, t1.kodePelatihan, t3.name as namaPegawai, t3.id as idPegawai, t3.pos_name as namaDepartemen, 'pelatih' as jenisPeserta from plt_pelatihan t1 join dta_trainer t2 join dta_pegawai t3 on (t1.idTrainer=t2.idTrainer and lower(trim(t2.namaTrainer))=lower(trim(t3.name))) where t1.statusPelatihan='t' and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'
		) as dta $sWhere order by idPelatihan";
		$res=db($sql);
		
		$sWhere= " where t1.statusPelatihan='t'";
		$sWhere.= " and t1.mulaiPelatihan between '".setTanggal($_GET['mSearch'])."' and '".setTanggal($_GET['tSearch'])."'";		
		$arrAbsensi=arrayQuery("select t1.idPelatihan, t2.idPegawai, count(t3.idPeserta) as jumlahAbsensi from plt_pelatihan t1 join plt_pelatihan_peserta t2 join plt_pelatihan_absensi t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPelatihan=t3.idPelatihan and t2.idPeserta=t3.idPeserta) $sWhere and t3.statusAbsensi='t' and t3.idJadwal>0 group by 1,2");
		$no=1;
		while($r=mysql_fetch_array($res)){
			
			$jamPelatihan = $menitPelatihan = 0;
			list($jmlPelatihan, $jamPelatihan, $menitPelatihan) = explode("\t", getField("select concat(count(idJadwal), '\t', sum(hour(timediff(selesaiJadwal, mulaiJadwal))), '\t', sum(minute(timediff(selesaiJadwal, mulaiJadwal)))) from plt_pelatihan_jadwal where idPelatihan='".$r[idPelatihan]."'"));
			
			$jamPelatihan = $jamPelatihan + floor($menitPelatihan/60);
			$menitPelatihan = $menitPelatihan%60;			
			$waktuPelatihan = $menitPelatihan > 0 ?
			getAngka($jamPelatihan)." Jam ".getAngka($menitPelatihan)." Menit":
			getAngka($jamPelatihan)." Jam";				
			
			$waktuPelatih = $r[jenisPeserta] == "peserta" ? "" : $waktuPelatihan;
			$waktuPelatihan = $r[jenisPeserta] == "peserta" ? $waktuPelatihan : "";		
			
			$jmlHadir = $r[jenisPeserta] == "peserta" ? $arrAbsensi["".$r[idPelatihan].""]["".$r[id].""] : $jmlPelatihan;
			$jmlAbsen = $jmlPelatihan - $jmlHadir;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[kodePelatihan]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[namaPegawai]));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $waktuPelatihan);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $waktuPelatih);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows,getAngka($jmlPelatihan));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows,getAngka($jmlHadir));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($jmlAbsen));

			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$pelatihJam+= $r[jenisPeserta] == "peserta" ? 0 : $jamPelatihan;
			$pelatihMenit+= $r[jenisPeserta] == "peserta" ? 0 : $menitPelatihan;
			$pelatihanJam+= $r[jenisPeserta] == "peserta" ? $jamPelatihan : 0;
			$pelatihanMenit+= $r[jenisPeserta] == "peserta" ? $menitPelatihan : 0;	
			$pelatihanJumlah+= $jmlPelatihan;
			$pelatihanHadir+= $jmlHadir;
			$pelatihanAbsen+= $jmlAbsen;
			
			$no++;
			$rows++;
		}

		$pelatihJam = $pelatihJam + floor($pelatihMenit/60);
		$pelatihMenit = $pelatihMenit%60;			
		$totalPelatih = $pelatihMenit > 0 ?
		getAngka($pelatihJam)." Jam ".getAngka($pelatihMenit)." Menit":
		getAngka($pelatihJam)." Jam";
		
		$pelatihanJam = $pelatihanJam + floor($pelatihanMenit/60);
		$pelatihanMenit = $pelatihanMenit%60;			
		$totalPelatihan = $pelatihanMenit > 0 ?
		getAngka($pelatihanJam)." Jam ".getAngka($pelatihanMenit)." Menit":
		getAngka($pelatihanJam)." Jam";
		
		$objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);		
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $totalPelatihan);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $totalPelatih);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows,getAngka($pelatihanJumlah));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows,getAngka($pelatihanHadir));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, getAngka($pelatihanAbsen));

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':E'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
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
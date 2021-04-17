<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
$fFile = "files/export/";

function peserta(){
	global $db,$s,$inp,$par,$hari,$arrTitle,$arrParameter,$menuAccess;		
	$sql="select * from dta_pegawai where id='$par[idPegawai]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);											
	
	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Peserta Pelatihan</h1>
		".getBread(ucwords("peserta pelatihan"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
			<input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\" style=\"position:absolute; top:0; right:0; margin-right:20px;\"/>
			<div id=\"general\" class=\"subcontent\">						
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">Nama</label>
					<span class=\"field\">".$r[name]."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">NPP</label>
					<span class=\"field\">".$r[reg_no]."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">Jabatan</label>
					<span class=\"field\">".$r[pos_name]."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">Posisi</label>
					<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[rank]'")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">Alamat</label>
					<span class=\"field\">".nl2br($r[dom_address])."&nbsp;</span>
				</p>
				<table style=\"width:100%\">
					<tr>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small\" style=\"width:100px;\">Propinsi</label>
								<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[dom_prov]'")."&nbsp;</span>
							</p>
						</td>
						<td style=\"width:50%\">
							<p>
								<label class=\"l-input-small\" style=\"width:100px;\">Kota</label>
								<span class=\"field\">".getField("select namaData from mst_data where kodeData='$r[dom_city]'")."&nbsp;</span>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p>
								<label class=\"l-input-small\" style=\"width:100px;\">Telepon</label>
								<span class=\"field\">".$r[phone_no]."&nbsp;</span>
							</p>
						</td>
						<td>
							<p>
								<label class=\"l-input-small\" style=\"width:100px;\">Handphone</label>
								<span class=\"field\">".$r[cell_no]."&nbsp;</span>
							</p>
						</td>
					</tr>					
				</table>
				<p>
					<label class=\"l-input-small\" style=\"width:100px;\">Email</label>
					<span class=\"field\">".$r[cell_no]."&nbsp;</span>
				</p>
			</div>
		</form>	
	</div>";		
	return $text;
}

function lihat(){
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
		<div id=\"pos_r\">
			<a href=\"?par[mode]=xls".getPar($par,"mode,idPelatihan")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
		</div>
	</form>
	<br clear=\"all\" />
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>					
				<th>Pelatihan</th>					
				<th style=\"width:75px;\">Mulai</th>
				<th style=\"width:75px;\">Selesai</th>
				<th>Lokasi</th>
				<th>Vendor</th>
				<th>PIC</th>
				<th style=\"width:50px;\">Peserta</th>
			</tr>
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
			or lower(t1.namaPic) like '%".strtolower($par[filter])."%'
			or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
			)";
			
			$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from (
			select p1.*, case when p1.pelaksanaanPelatihan='e' then p2.namaTrainer else p1.namaPegawai end as namaPic from (
			select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
			) as p1 left join dta_trainer p2 on (p1.idTrainer=p2.idTrainer)
			) as t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){					
				$no++;			
				$cntPeserta = getField("select count(idPeserta) from plt_pelatihan_peserta where idPelatihan='$r[idPelatihan]'");			
				
				$text.="<tr>
				<td>$no.</td>			
				<td><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>
				<td align=\"center\">".getTanggal($r[mulaiPelatihan])."</td>
				<td align=\"center\">".getTanggal($r[selesaiPelatihan])."</td>
				<td>$r[lokasiPelatihan]</td>
				<td>$r[namaVendor]</td>
				<td>$r[namaPic]</td>
				<td align=\"center\">".getAngka($cntPeserta)."</td>					
			</tr>";			
		}
		
		$text.="</tbody>
	</table>
</div>";	

if($par[mode] == "xls"){			
	xls();			
	$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}	

return $text;
}

function detail(){
	global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
	$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					
	$pelaksanaanPelatihan =  $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";		
	
	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread(ucwords($par[mode]." data"))."								
</div>
<div class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  >	
		<div style=\"top:10px; right:35px; position:absolute\">
			<a href=\"?par[mode]=detXls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a> 
			<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/>
		</div>
		<div id=\"general\" style=\"margin-top:20px;\">					
			".dtaPelatihan()."					
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>PESERTA</h4></legend>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th>Nama</th>
							<th width=\"200\">Jabatan</th>
							<th width=\"200\">Posisi</th>
							<th width=\"75\">Umur</th>
							<th width=\"50\">View</th>
						</tr>
					</thead>
					<tbody>";
						
						$sql="select * from plt_pelatihan_peserta t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idPelatihan='$par[idPelatihan]' order by t2.name";
						$res=db($sql);
						$no=1;
						while($r=mysql_fetch_array($res)){								
							$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$r[posisiPeserta]</td>
							<td>$r[jabatanPeserta]</td>
							<td align=\"right\">".getAngka($r[umurPeserta])." Tahun</td>
							<td align=\"center\">
								<a href=\"#Detail\" title=\"Detail Data\" class=\"detail\"  onclick=\"openBox('popup.php?par[mode]=detPeserta&par[idPegawai]=$r[idPegawai]".getPar($par,"mode,idPegawai")."',725,450);\"><span>Detail</span></a>
							</td>
						</tr>";			
						$no++;
					}
					
					if($no == 1)
						$text.="<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";
				
				$text.="</tbody>
			</table>
		</fieldset>
	</div>				
</form>";

if($par[mode] == "detXls"){			
	detXls();			
	$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower("Peserta Pelatihan")).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}	

return $text;
}

function detail2(){
	global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;
	$sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);
	if (empty($r[idPelatihan])) {
		$r[idPelatihan] = getField('select idPelatihan from plt_pelatihan order by idPelatihan desc limit 1') + 1;
	}
	if (empty($r[idKategori])) {
		$r[idKategori] = $par[idKategori];
	}
	if (!is_array($detail)) {
		$detail = arrayQuery("select idDetail,concat(keteranganDetail, '\t', DATE_FORMAT(date(mulaiDetail),'%d/%m/%Y'), '\t', substring(time(mulaiDetail),1,5), '\t', DATE_FORMAT(date(selesaiDetail),'%d/%m/%Y'), '\t', substring(time(selesaiDetail),1,5)) from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]'");
	}

	$kodeModul = getField("select kodeModul from app_modul where folderModul = 'katalog'");

	$r[idPegawai] = empty($inp[idPegawai]) ? $r[idPegawai] : $inp[idPegawai];
	$r[idVendor] = empty($inp[idVendor]) ? $r[idVendor] : $inp[idVendor];
	$r[idKategori] = empty($inp[idKategori]) ? $r[idKategori] : $inp[idKategori];
	$r[idDepartemen] = empty($inp[idDepartemen]) ? $r[idDepartemen] : $inp[idDepartemen];
	$r[kodePelatihan] = empty($inp[kodePelatihan]) ? $r[kodePelatihan] : $inp[kodePelatihan];
	$r[judulPelatihan] = empty($inp[judulPelatihan]) ? $r[judulPelatihan] : $inp[judulPelatihan];
	$r[subPelatihan] = empty($inp[subPelatihan]) ? $r[subPelatihan] : $inp[subPelatihan];
	$r[pesertaPelatihan] = empty($inp[pesertaPelatihan]) ? $r[pesertaPelatihan] : setAngka($inp[pesertaPelatihan]);
	$r[pelaksanaanPelatihan] = empty($inp[pelaksanaanPelatihan]) ? $r[pelaksanaanPelatihan] : $inp[pelaksanaanPelatihan];
	$r[lokasiPelatihan] = empty($inp[lokasiPelatihan]) ? $r[lokasiPelatihan] : $inp[lokasiPelatihan];
	$r[biayaPelatihan] = empty($inp[biayaPelatihan]) ? $r[biayaPelatihan] : setAngka($inp[biayaPelatihan]);
	$r[filePelatihan] = empty($fileTemp) ? $r[filePelatihan] : upload($r[idPelatihan]);

	$eksternal = $r[pelaksanaanPelatihan] == 'e' ? 'checked="checked"' : '';
	$internal = empty($eksternal) ? 'checked="checked"' : '';
	$cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

	$text .="
	<style>
        #inp_kodeRekening__chosen{
		min-width:250px;
	}
</style>
<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords("import data"))."
		<span class=\"pagedesc\">&nbsp;</span> 
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"position:absolute; right:20px; top:14px;\">
				<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
			</div>
			<!--<fieldset>
			<legend>KATALOG PROGRAM</legend>
			<p>
				<label class=\"l-input-small\">Modul</label>
				<span class=\"field\">
					&nbsp;".getField("SELECT keterangan from app_site WHERE kodeSite = '$r[modul_pelatihan]'")."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Kategori Level</label>
				<span class=\"field\">
					&nbsp;".getField("SELECT namaMenu FROM app_menu WHERE kodeMenu = '$r[kategori_level_pelatihan]'")."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Program</label>
				<span class=\"field\">
					&nbsp;".getField("SELECT program FROM ctg_program WHERE id_program = '$r[program_pelatihan]'")."
				</span>
			</p>
		</fieldset>
		<br>-->
		<fieldset>
			<legend>PELATIHAN</legend>
			<p>
				<label class=\"l-input-small\">Judul Pelatihan</label>
				<span class=\"field\">
					&nbsp;".$r[judulPelatihan]."
				</span>
			</p>
			<table style='width:100%;'>
				<tr>
					<td style='width:50%;'>
						<p>
							<label class=\"l-input-small2\">Tanggal Mulai</label>
							<span class=\"field\">
								&nbsp;".getTanggal($r[mulaiPelatihan])."
							</span>
						</p>
					</td>
					<td style='width:50%;'>
						<p>
							<label class=\"l-input-small2\">Tanggal Selesai</label>
							<span class=\"field\">
								&nbsp;".getTanggal($r[selesaiPelatihan])."
							</span>
						</p>
					</td>
				</tr>
			</table>
			<table style='width:100%;'>
				<tr>
					<td style='width:50%;'>
						<p>
							<label class=\"l-input-small2\">Sub</label>
							<span class=\"field\">
								&nbsp;".$r[subPelatihan]."
							</span>
						</p>
					</td>
					<td style='width:50%;'>
						<p>
							<label class=\"l-input-small2\">Kode</label>
							<span class=\"field\">
								&nbsp;".$r[kodePelatihan]."
							</span>
						</p>
					</td>
				</tr>
			</table>
			<p>
                <label class=\"l-input-small\">Kategori</label>
                <span class=\"field\">
                &nbsp;" . namaData($r[idTraining]) . "
                </span>
            </p>
            <p>
                <label class=\"l-input-small\">Training</label>
                <span class=\"field\">
                &nbsp;" . namaData($r[idKategori]) . "
                </span>
            </p>
            <p>
                <label class=\"l-input-small\">Level</label>
                <span class=\"field\">
                &nbsp;" . namaData($r[idDepartemen]) . "
                </span>
            </p>
			<p>
				<label class=\"l-input-small\">Jumlah Peserta</label>
				<span class=\"field\">
					&nbsp;".getAngka($r[pesertaPelatihan])."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Pelaksanaan</label>
				<span class=\"field\">
					&nbsp;".($r[pelaksanaanPelatihan]=='e'?'Eksternal' : 'Internal')."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Vendor</label>
				<span class=\"field\">
					&nbsp;".getField("SELECT namaVendor from dta_vendor where kodeVendor = '$r[idVendor]'")."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Koordinator</label>
				<span class=\"field\">
					&nbsp;".getField("SELECT upper(namaTrainer) as namaTrainer from dta_trainer where idTrainer = '$r[idTrainer]'")."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Lokasi</label>
				<span class=\"field\">
					&nbsp;".$r[lokasiPelatihan]."
				</span>
			</p>
		</fieldset>
	</form>
</div>
</div>";
return $text;
}

function detXls(){		
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess;
	require_once 'plugins/PHPExcel.php';
	$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					
	$pelaksanaanPelatihan =  $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";		
	
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	
	$objPHPExcel->getActiveSheet()->getRowDimension('12')->setRowHeight(25);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);				
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
	$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
	$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
	$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
	$objPHPExcel->getActiveSheet()->mergeCells('A9:B9');
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PELATIHAN');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Pelatihan');
	$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Sub');
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'Jumlah Peserta');
	$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Pelaksanaan');
	$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Vendor');
	$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Lokasi');
	$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Biaya');
	$objPHPExcel->getActiveSheet()->setCellValue('A9', 'Penanggung Jawab');
	
	$objPHPExcel->getActiveSheet()->setCellValue('C2', ': '.$r[judulPelatihan]);
	$objPHPExcel->getActiveSheet()->setCellValue('C3', ': '.$r[subPelatihan]);
	$objPHPExcel->getActiveSheet()->setCellValue('C4', ': '.getAngka($r[pesertaPelatihan]).' Orang');
	$objPHPExcel->getActiveSheet()->setCellValue('C5', ': '.$pelaksanaanPelatihan);
	$objPHPExcel->getActiveSheet()->setCellValue('C6', ': '.getField("select namaVendor from dta_vendor where kodeVendor='".$r[idVendor]."'"));
	$objPHPExcel->getActiveSheet()->setCellValue('C7', ': '.$r[lokasiPelatihan]);
	$objPHPExcel->getActiveSheet()->setCellValue('C8', ': Rp. '.getAngka($r[biayaPelatihan]));
	$objPHPExcel->getActiveSheet()->setCellValue('C9', ': '.getField("select name from emp where id='".$r[idPegawai]."'"));
	
	$objPHPExcel->getActiveSheet()->getStyle('A11')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->mergeCells('A11:F11');
	$objPHPExcel->getActiveSheet()->setCellValue('A11', 'PESERTA');
	
	$objPHPExcel->getActiveSheet()->getStyle('A12:F12')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A12:F12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A12:F12')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A12:F12')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A12:F12')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
	
	$objPHPExcel->getActiveSheet()->mergeCells('B12:C12');
	$objPHPExcel->getActiveSheet()->setCellValue('A12', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B12', 'NAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('D12', 'JABATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('E12', 'POSISI');
	$objPHPExcel->getActiveSheet()->setCellValue('F12', 'UMUR');
	
	$rows = 13;
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
	
	$sql="select * from plt_pelatihan_peserta t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idPelatihan='$par[idPelatihan]' order by t2.name";
	$res=db($sql);
	$no=1;
	while($r=mysql_fetch_array($res)){										
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':F'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$rows.':C'.$rows);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[jabatanPeserta]);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[posisiPeserta]);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($r[umurPeserta]).' Tahun');	
		
		$rows++;							
		$no++;
	}
	
	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A12:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A12:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B12:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C12:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D12:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E12:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F12:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:F'.$rows)->getFont()->setName('Arial');
	$objPHPExcel->getActiveSheet()->getStyle('A13:F'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
	
	$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
	$objPHPExcel->setActiveSheetIndex(0);
	
		// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fFile.ucwords(strtolower("Peserta Pelatihan")).".xls");
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
	
	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
	$objPHPExcel->getActiveSheet()->getRowDimension('5')->setRowHeight(25);
	
	$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DAFTAR PESERTA');
	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Tahun '.$par[tahunPelatihan]);
	
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
	
	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', 'PELATIHAN');
	$objPHPExcel->getActiveSheet()->setCellValue('C4', 'MULAI');
	$objPHPExcel->getActiveSheet()->setCellValue('D4', 'SELESAI');
	$objPHPExcel->getActiveSheet()->setCellValue('E4', 'LOKASI');
	$objPHPExcel->getActiveSheet()->setCellValue('F4', 'VENDOR');
	$objPHPExcel->getActiveSheet()->setCellValue('G4', 'PIC');
	$objPHPExcel->getActiveSheet()->setCellValue('H4', 'PESERTA');
	
	$rows = 5;
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
	
	$sql="select t1.*, t2.namaVendor, t3.name as namaPegawai from plt_pelatihan t1 join dta_vendor t2 join emp t3 on (t1.idVendor=t2.kodeVendor and t1.idPegawai=t3.id) $filter order by t1.idPelatihan";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$no++;			
		$cntPeserta = getField("select count(idPeserta) from plt_pelatihan_peserta where idPelatihan='$r[idPelatihan]'");
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':D'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':H'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[judulPelatihan]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, getTanggal($r[mulaiPelatihan]));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, getTanggal($r[selesaiPelatihan]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[lokasiPelatihan]);			
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[namaVendor]);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[namaPegawai]);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $cntPeserta);
		
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
	$objPHPExcel->getActiveSheet()->getStyle('A5:H'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
	
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
	
	$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
	$objPHPExcel->setActiveSheetIndex(0);
	
		// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
}	

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){	
		case "detail2":
		$text = detail2();
		break;

		case "detPeserta":
		$text = peserta();
		break;
		case "detXls":
		$text = detail();
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
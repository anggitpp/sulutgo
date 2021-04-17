<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
$fFile = "files/dokumen/";

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

function jadwal(){
	global $db,$s,$inp,$par,$hari,$arrTitle,$arrParameter,$menuAccess;		
	$sql="select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]' and idJadwal='$par[idJadwal]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);											
	
	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Jadwal Pelatihan</h1>
		".getBread(ucwords("jadwal pelatihan"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">	
			<input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\" style=\"position:absolute; top:0; right:0; margin-right:20px;\"/>
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<span class=\"field\">".getTanggal($r[tanggalJadwal], "t")."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Uraian</label>
					<span class=\"field\">".$r[judulJadwal]."&nbsp;</span>
				</p>					
				<p>
					<label class=\"l-input-small\">Mulai</label>
					<span class=\"field\">".substr($r[mulaiJadwal],0,5)."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Selesai</label>
					<span class=\"field\">".substr($r[selesaiJadwal],0,5)."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<span class=\"field\">".nl2br($r[keteranganJadwal])."&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">PIC</label>
					<span class=\"field\">".getField("select name from emp where id='".$r[idPegawai]."'")."&nbsp;</span>
				</p>
			</div>
		</form>	
	</div>";		
	return $text;
}


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
		<div id=\"pos_r\" style=\"floar:right\">
			<strong>Keterangan : </strong>
			<img src=\"styles/images/t.png\" style=\"margin-left:20px;\"> Terlaksana
			<img src=\"styles/images/p.png\" style=\"margin-left:20px;\"> Sedang
			<img src=\"styles/images/f.png\" style=\"margin-left:20px;\"> Batal
			<img src=\"styles/images/o.png\" style=\"margin-left:20px;\"> Belum
		</div>
	</form>
	<br clear=\"all\" />
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
				
				if(!empty($par[idKategori]))
					$filter.=" and t1.idKategori='".$par[idKategori]."'";
				
				if(!empty($par[filter]))		
					$filter.= " and (
				lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
				or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
				or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
				)";
				
				$periodeTanggal = date('Ym');
				
				$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from plt_pelatihan t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){						
					$no++;
					list($tahunMulai, $bulanMulai) = explode("-", $r[mulaiPelatihan]);
					list($tahunSelesai, $bulanSelesai) = explode("-", $r[selesaiPelatihan]);
					$text.="<tr>
					<td>$no.</td>			
					<td><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>";
					for($i=1; $i<=12; $i++){
						$periodePelatihan = $par[tahunPelatihan].str_pad($i, 2, "0", STR_PAD_LEFT);
						
						$background = "#fff";
						if($periodeTanggal < $periodePelatihan) $background = "#1f11be";
						if($periodeTanggal == $periodePelatihan) $background = "#c1b00e";
						if($periodeTanggal > $periodePelatihan) $background = "#1fc22e";
						
						$color = $tahunMulai.$bulanMulai <= $periodePelatihan && $tahunSelesai.$bulanSelesai >= $periodePelatihan ? "background:".$background."" : "";
						$text.="<td style=\"".$color."\">&nbsp;</td>";
					}
					$text.="</tr>";							
				}	
				
				$text.="</tbody>
			</table>			
		</div>";			
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$menuAccess,$fFile;				
		$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		$pelaksanaanPelatihan =  $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."								
	</div>
	<div class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."#detail\" enctype=\"multipart/form-data\">	
			<div style=\"top:10px; right:35px; position:absolute\">
				<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/>
			</div>
			<div id=\"general\" style=\"margin-top:20px;\">					
				".dtaPelatihan()."
				<ul class=\"hornav\">
					<li class=\"current\"><a href=\"#jadwal\">Jadwal</a></li>
					<li><a href=\"#peserta\">Peserta</a></li>
					<li><a href=\"#biaya\">Biaya</a></li>
				</ul>
				<div id=\"jadwal\" class=\"subcontent\">
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Uraian</th>
								<th width=\"150\">Mulai</th>
								<th width=\"150\">Selesai</th>
								<th width=\"50\">View</th>
							</tr>
						</thead>
						<tbody>";
							
							$sql="select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]'order by mulaiJadwal";
							$res=db($sql);
							$no=1;
							while($r=mysql_fetch_array($res)){		
								$text.="<tr>
								<td>$no.</td>
								<td>$r[judulJadwal]</td>										
								<td align=\"center\">".getTanggal($r[tanggalJadwal])." ".substr($r[mulaiJadwal],0,5)."</td>
								<td align=\"center\">".getTanggal($r[tanggalJadwal])." ".substr($r[selesaiJadwal],0,5)."</td>
								<td align=\"center\">
									<a href=\"#Detail\" title=\"Detail Data\" class=\"detail\"  onclick=\"openBox('popup.php?par[mode]=detJadwal&par[idJadwal]=$r[idJadwal]".getPar($par,"mode,idJadwal")."',725,450);\"><span>Detail</span></a>
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
					</tr>";
					
					$text.="</tbody>
				</table>
			</div>
			
			<div id=\"peserta\" class=\"subcontent\" style=\"display:none;\">
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
							<td>$r[jabatanPeserta]</td>
							<td>$r[posisiPeserta]</td>
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
		</div>
		
		<div id=\"biaya\" class=\"subcontent\" style=\"display:none;\">
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
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
	</div>

	<fieldset style=\"padding:10px; border-radius: 10px; margin-top:20px;\">
		<legend style=\"padding:10px; margin-left:20px;\"><h4>FOTO</h4></legend>
		<ul class=\"listfile\">";				
			$sql="select * from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and tipeDokumen='f' order by idDokumen";
			$res=db($sql);
			$no=1;
			while($r=mysql_fetch_array($res)){
				$text.="<li>
				<a class=\"image\" href=\"#\">
					<span class=\"img\"><img src=\"".$fFile.$r[fileDokumen]."\" alt=\"$r[judulDokumen]\" width=\"125\" height=\"100\" onclick=\"openBox('view.php?doc=dokumen&id=$r[idDokumen]".getPar($par,"mode,idDokumen")."',1000,550);\"></span>
					<span class=\"filename\">$r[judulDokumen]</span>
				</a>
			</li>";
			$no++;
		}
		
		$text.="</ul>
	</fieldset>
	
	<fieldset style=\"padding:10px; border-radius: 10px; margin-top:30px;\">
		<legend style=\"padding:10px; margin-left:20px;\"><h4>DOKUMEN</h4></legend>
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Dokumen</th>
					<th width=\"50\">View</th>
					<th width=\"100\">Besar</th>
				</tr>
			</thead>
			<tbody>";
				
				$sql="select * from plt_pelatihan_dokumen where idPelatihan='$par[idPelatihan]' and tipeDokumen='d' order by idDokumen";
				$res=db($sql);
				$no=1;
				while($r=mysql_fetch_array($res)){								
					$text.="<tr>
					<td>$no.</td>
					<td>$r[judulDokumen]</td>
					<td align=\"center\"><a href=\"#View\" title=\"View Dokumen\" o onclick=\"openBox('view.php?doc=dokumen&id=$r[idDokumen]".getPar($par,"mode,idDokumen")."',1000,550);\"><img src=\"".getIcon($r[fileDokumen])."\"></a></td>
					<td align=\"right\">".getAngka($r[ukuranDokumen] / 1024)." KB</td>
				</tr>";			
				$no++;
			}
			
			if($no == 1)
				$text.="<tr>
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

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){	
		case "detail2":
		$text = detail2();
		break;

		case "detJadwal":
		$text = jadwal();
		break;
		case "detPeserta":
		$text = peserta();
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
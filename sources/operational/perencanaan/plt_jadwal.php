<?php
if (!isset($menuAccess[$s]['view'])) echo '<script>logout();</script>';

switch ($par[mode]) {

	case "datas":
		datas();
		break;

	case "detail2":
		$text = detail2();
	break;

	case 'del':
		if (isset($menuAccess[$s]['delete'])) {
			$text = hapus();
		} else {
			$text = dataa();
		}
	break;

	case 'edit':
		if (isset($menuAccess[$s]['edit'])) {
			$text = empty($_submit) ? form() : ubah();
		} else {
			$text = dataa();
		}
	break;

	case 'add':
		if (isset($menuAccess[$s]['add'])) {
			$text = empty($_submit) ? form() : tambah();
		} else {
			$text = dataa();
		}
	break;

	case 'dta':
		$text = dataa();
	break;

	case 'det':
		$text = detail();
	break;

	default:
		$text = calendar();
	break;
}
echo $text;

function datas() {

	global $par;

	$arr_color = ["#fa5050", "#f75ead", "#ef75ff", "#bb73fa", "#716afc", "#5b9bfc", "#4cccff", "#4de2f0", "#51e096", "#50d161", "#abe356", "#d1e860", "#f0a857"];

	$year = empty($par['tahunPelatihan']) ? date('Y') : $par['tahunPelatihan'];

	$res = db("SELECT * FROM `plt_pelatihan` WHERE '$year' BETWEEN YEAR(`mulaiPelatihan`) AND YEAR(`selesaiPelatihan`) ORDER BY `idPelatihan`");

	while($row = mysql_fetch_assoc($res)) {

		@$no++;

		shuffle($arr_color);

		$datas[] = [
			"id" 		=> $row['idPelatihan'],
			"title" 	=> $row['judulPelatihan'],
			"start" 	=> $row['mulaiPelatihan'],
			"end" 		=> $row['selesaiPelatihan'],
			"color" 	=> $arr_color[0],
			"tmp" 		=> $row['judulPelatihan']."\n" . $row['lokasiPelatihan'] . " : " . getTanggal($row['mulaiPelatihan']) . " s.d " . getTanggal($row['selesaiPelatihan']),
		];

	}

	echo json_encode($datas);
}

function detail2() {

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
			<fieldset>
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
			<br>
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

function hapus() {

	global $db,$s,$inp,$par,$cUsername;

	$sql = "delete from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]' and idJadwal='$par[idJadwal]'";

	db($sql);

	echo "<script>window.location='?par[mode]=det".getPar($par, 'mode,idJadwal')."';</script>";
}

function ubah() {

	global $db,$s,$inp,$par,$cUsername;

	repField();

	if (isset($inp[evaluasi_status])) {
		$evaluasi_status = 't';
		$id_pegawai = $inp[idPegawai];
	}else{
		$evaluasi_status = 'f';
		$id_pegawai='0';
	}

	$sql = "update plt_pelatihan_jadwal set idPegawai='$id_pegawai', evaluasi_status = '$evaluasi_status', judulJadwal='$inp[judulJadwal]', mulaiJadwal='$inp[mulaiJadwal]', selesaiJadwal='$inp[selesaiJadwal]', keteranganJadwal='$inp[keteranganJadwal]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPelatihan='$par[idPelatihan]' and idJadwal='$par[idJadwal]'";

	db($sql);

	echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par, 'mode,idJadwal')."';</script>";
}

function tambah() {

	global $db,$s,$inp,$par,$cUsername;

	repField();

	if (isset($inp[evaluasi_status])) {
		$evaluasi_status = 't';
		$id_pegawai = $inp[idPegawai];
	}else{
		$evaluasi_status = 'f';
		$id_pegawai='0';

	}

	$idJadwal = getField("select idJadwal from plt_pelatihan_jadwal where idPelatihan='".$par[idPelatihan]."' order by idJadwal desc limit 1") + 1;

	$sql = "insert into plt_pelatihan_jadwal (idPelatihan, idJadwal, idPegawai, evaluasi_status, judulJadwal, tanggalJadwal, mulaiJadwal, selesaiJadwal, keteranganJadwal, createBy, createTime) values ('$par[idPelatihan]', '$idJadwal', '$id_pegawai', '$evaluasi_status', '$inp[judulJadwal]', '$par[tanggalJadwal]', '$inp[mulaiJadwal]', '$inp[selesaiJadwal]', '$inp[keteranganJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')";

	db($sql);

	echo "<script>window.parent.location='index.php?par[mode]=det".getPar($par, 'mode,idJadwal')."';</script>";
}

function form() {

	global $db,$s,$inp,$par,$hari,$arrTitle,$arrParameter,$menuAccess;

	echo "
	<script>
		window.onload = function() {
			if(window.location.hash != '#loaded'){
				window.location = window.location + '#loaded';
				window.location.reload();
			}
		}
	</script>";

$sql = "select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]' and idJadwal='$par[idJadwal]'";

$res = db($sql);

$r = mysql_fetch_array($res);

$cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");

$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

$idTrainer = getField("SELECT idTrainer FROM plt_pelatihan where idPelatihan = '$par[idPelatihan]'");

$checked = $r[evaluasi_status] == "p" ? "checked=\"checked\"" : "";
$styleVendor = $r[evaluasi_status] == "p" ? "style=\"display:block;\"" : "style=\"display:none;\"";



setValidation('is_null', 'inp[judulJadwal]', 'anda harus mengisi uraian');

setValidation('is_null', 'mulaiJadwal', 'anda harus mengisi mulai');

setValidation('is_null', 'selesaiJadwal', 'anda harus mengisi selesai');


$text = getValidation();

$text .= '<div class="centercontent contentpopup">
<div class="pageheader">
	<h1 class="pagetitle">Jadwal Pelatihan</h1>
	'.getBread(ucwords($par[mode].' jadwal pelatihan')).'
</div>
<div id="contentwrapper" class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?_submit=1'.getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		
		<p style=\"position:absolute;right:5px;top:5px;\">
			<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return pas();\"/>
		</p>
		<div id=\"general\" class=\"subcontent\">
			<p>
				<label class=\"l-input-small\">Hari $hari</label>
				<span class=\"field\" style=\"border:0px;\">".getTanggal($par[tanggalJadwal], 't')."&nbsp;</span>
			</p>
			<p>
				<label class=\"l-input-small\">Uraian</label>
				<div class=\"field\">
					<input type=\"text\" id=\"inp[judulJadwal]\" name=\"inp[judulJadwal]\" value=\"$r[judulJadwal]\" class=\"mediuminput\" style=\"width:425px;\" maxlength=\"150\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Mulai</label>
				<div class=\"field\">
					<input type=\"text\" id=\"mulaiJadwal\" name=\"inp[mulaiJadwal]\" value=\"".substr($r[mulaiJadwal], 0, 5).'" size="10" maxlength="5" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" readonly="readonly"/>
				</div>
			</p>
			<p>
				<label class="l-input-small">Selesai</label>
				<div class="field">
					<input type="text" id="selesaiJadwal" name="inp[selesaiJadwal]" value="'.substr($r[selesaiJadwal], 0, 5)."\" size=\"10\" maxlength=\"5\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<div class=\"field\">
					<textarea id=\"inp[keteranganJadwal]\" name=\"inp[keteranganJadwal]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350x;\">$r[keteranganJadwal]</textarea>
				</div>
			</p>
			<p>
				
				<p>
					<label class=\"l-input-small\" >Evaluasi</label>
					<div class=\"field\">
						<div class=\"checkbox\" style=\"padding-top: 5px;\">
							<input type=\"checkbox\" id=\"inp[evaluasi_status]\" name=\"inp[evaluasi_status]\" value=\"on\" onclick=\"myFunction();\"  $checked ><span>ADA</span>
						</div>
					</div>
				</p>
				
				<div id=\"fieldVendor\" $styleVendor>
					<label class=\"l-input-small\" >Trainer</label>
					<div class=\"field\">
						".comboData("select idTrainer, namaTrainer  from dta_trainer where idTrainer = '$idTrainer' order by namaTrainer", 'idTrainer', 'namaTrainer', 'inp[idPegawai]', ' ', $r[idPegawai], '', '435px', '').'									
					</div>
				</div>
				
			</p>

			
		</div>
	</form>
</div>';

return $text;
}

function detail()
{
	global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;

	$sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";

	$res = db($sql);

	$r = mysql_fetch_array($res);

	$pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == 'e' ? 'Eksternal' : 'Internal';

	$text .= '<div class="pageheader">
	<h1 class="pagetitle">'.$arrTitle[$s].'</h1>
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?'.getPar($par).'#detail\" enctype=\"multipart/form-data\">
		<div id="general" style="margin-top:20px;">
			' . dtaPelatihan('RENCANA PELATIHAN');

			$text.="
			<h4 style='float:left; padding-top:10px;'>DETAIL JADWAL</h4>
			<!--<a href=\"#\" id=\"\" class=\"btn btn1 btn_document\" style='float:right; margin-bottom:5px;' onclick=\"openBox('popup.php?par[mode]=edit&par[idJadwal]=$r[idJadwal]&par[tanggalJadwal]=$tanggalJadwal&hari=$hari".getPar($par, 'mode,idJadwal,tanggalJadwal')."',725,450);\"><span>Tambah Jadwal</span></a>-->
			<a href=\"#\" id=\"btnExport2\" class=\"btn btn1 btn_inboxi\" style='float:right; margin-bottom:5px;'><span>Export</span></a>
			<hr style='clear:both; margin-bottom:10px;'>
			";

			$sql = "select * from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]' order by idDetail";

			$res = db($sql);

			while ($r = mysql_fetch_array($res)) {
				list($mulaiDetail) = explode(' ', $r[mulaiDetail]);

				list($selesaiDetail) = explode(' ', $r[selesaiDetail]);

				$tanggalDetail = $mulaiDetail;

				while ($tanggalDetail <= $selesaiDetail) {
					list($tahun, $bulan, $tanggal) = explode('-', $tanggalDetail);

					$arrTanggal[$tanggalDetail] = $tanggalDetail;

					$tanggalDetail = date('Y-m-d', dateAdd('d', 1, mktime(0, 0, 0, $bulan, $tanggal, $tahun)));
				}
			}

			if (empty($par[tanggalJadwal])) {
				$par[tanggalJadwal] = min($arrTanggal);
			}

			if (is_array($arrTanggal)) {
				$text .= '<ul class="hornav">';

				$hari = 1;

				ksort($arrTanggal);

				reset($arrTanggal);

				while (list($tanggalJadwal) = each($arrTanggal)) {
					$current = $tanggalJadwal == $par[tanggalJadwal] ? 'class="current"' : '';

					$text .= '<li '.$current.' style="margin-bottom:10px;"><a href="#tab_'.$tanggalJadwal.'">Hari '.$hari.'</a></li>';

					++$hari;
				}

				$text .= '</ul>';

				$hari = 1;

				ksort($arrTanggal);

				reset($arrTanggal);

				while (list($tanggalJadwal) = each($arrTanggal)) {
					$display = $tanggalJadwal == $par[tanggalJadwal] ? 'display: block;' : 'display: none;';

					$text .= '<div id="tab_'.$tanggalJadwal.'" class="subcontent" style="border:0px; clear:both; '.$display.'">
					<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
						<thead>
							<tr>
								<th width="20">No.</th>
								<th>Uraian</th>
								<th width="75">Mulai</th>
								<th width="75">Selesai</th>';

								if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
									$text .= '<th width="50">Kontrol</th>';
								}

								$text .= '</tr>
							</thead>
							<tbody>';

								$text .= '<tr>
								<td>&nbsp;</td>
								<td><strong>'.getTanggal($tanggalJadwal, 't') . '&nbsp;-&nbsp;' . getField("SELECT keteranganDetail FROM plt_pelatihan_detail WHERE idPelatihan = '$par[idPelatihan]' AND '$tanggalJadwal' BETWEEN DATE(mulaiDetail) AND DATE(selesaiDetail)").'</strong></td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>';

								if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
									$text .= '<td>&nbsp;</td>';
								}

								$text .= '</tr>';

								$sql = "select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]' and tanggalJadwal='$tanggalJadwal' order by mulaiJadwal";

								$res = db($sql);

								$no = 1;

								while ($r = mysql_fetch_array($res)) {
									$text .= "<tr>
									<td>$no.</td>
									<td>$r[judulJadwal]</td>
									<td align=\"center\">".substr($r[mulaiJadwal], 0, 5).'</td>
									<td align="center">'.substr($r[selesaiJadwal], 0, 5).'</td>';

									if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
										$text .= '<td align="center">';

										if (isset($menuAccess[$s]['edit'])) {
											$text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idJadwal]=$r[idJadwal]&par[tanggalJadwal]=$tanggalJadwal&hari=$hari".getPar($par, 'mode,idJadwal,tanggalJadwal')."',725,450);\"><span>Edit</span></a>";
										}

										if (isset($menuAccess[$s]['delete'])) {
											$text .= "<a href=\"?par[mode]=del&par[idJadwal]=$r[idJadwal]&par[tanggalJadwal]=$tanggalJadwal&hari=$hari".getPar($par, 'mode,idJadwal,tanggalJadwal')."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
										}

										$text .= '</td>';
									}

									$text .= '</tr>';

									++$no;
								}

								$text .= '</tbody>
							</table>';

							if (isset($menuAccess[$s]['add'])) {
								$text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add&par[tanggalJadwal]=$tanggalJadwal&hari=$hari".getPar($par, 'mode,idJadwal,tanggalJadwal')."',725,450);\" style=\"float:right;\"><span>Tambah Data</span></a>";
							}

							$text .= '
						</div>';

						++$hari;
					}
				} else {
					$text .= 'Data pelaksanaan belum ada pada rencana pelatihan.';
				}

				$text .= '
			</div>
		</form>';

		return $text;
	}

function calendar() {

		global $db,$s,$inp,$par,$_submit,$arrTitle,$fFile,$arrParameter,$menuAccess;

		if (empty($_submit) && empty($par[tahunPelatihan])) {
			$par[tahunPelatihan] = date('Y');
		}

		$arrData = json_decode(file_get_contents($url), true);

		$tanggalPelatihan = $par[tahunPelatihan] == date('Y') ? date('d') : 1;

		if (empty($par[bulanPelatihan])) {
			$bulanPelatihan = $par[tahunPelatihan] == date('Y') ? date('m') - 1 : 0;
		} else {
			$bulanPelatihan = $par[bulanPelatihan] - 1;
		}

		$tahunPelatihan = empty($par[tahunPelatihan]) ? date('Y') : $par[tahunPelatihan];

	$text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$arrTitle[$s]</h1>
		" . getBread() . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">

		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\" style=\"position: absolute; top: 0; right: 0; margin-top: 10px; margin-right: 20px;\">
			<div>
				<input type=\"hidden\" name=\"_submit\" value=\"t\">
				Periode : " . comboYear('par[tahunPelatihan]', $par['tahunPelatihan'], '', "onchange=\"document.getElementById('form').submit();\"", "", "All") . "
				&nbsp;
				<a href=\"?" . getPar($par, 'mode, idPelatihan, bulanPelatihan') . "&par[mode]=dta\" class=\"btn btn1 btn_list\"><span>LIST DATA</span></a>
			</div>
		</form>

		<br clear=\"all\" />

		<div id=\"calendar\"></div>
		<input type=\"hidden\" id=\"getPar\" value=\"" . getPar($par, 'mode, idPelatihan') . "\"/>

		<script type=\"text/javascript\" src=\"scripts/calendar.js\"></script>
		<script type=\"text/javascript\">
			jQuery(function() {
				jQuery('#calendar').fullCalendar({
					year: '$tahunPelatihan',
					month: '$bulanPelatihan',
					date: '$tanggalPelatihan',
					header: {
						left: 'month, agendaWeek, agendaDay',
						center: 'title',
						right: 'prev, next'
					},
					buttonText: {
						prev: '&laquo;',
						next: '&raquo;',
						prevYear: '&nbsp;&lt;&lt;&nbsp;',
						nextYear: '&nbsp;&gt;&gt;&nbsp;',
						today: 'today',
						month: 'month',
						week: 'week',
						day: 'day'
					},
					events: {
						url: 'ajax.php?" . getPar($par, 'mode') . "&par[mode]=datas',
						cache: true
					},
					eventMouseover: function(calEvent, jsEvent) {
					
						arr = calEvent.tmp.split(\"\\n\");

						var tooltip = '<div class=\"tooltipevent\" style=\"background:'+ calEvent.color +'; color:#fff; padding:10px 20px; position:absolute;z-index:10000; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;\">';

						tooltip = tooltip + '<strong>' + arr[0] + '</strong><br>';
						tooltip = tooltip + arr[1] + '<br>';
						tooltip = tooltip + '</div>';

						jQuery(\"body\").append(tooltip);
						jQuery(this).mouseover(function(e) {
							jQuery(this).css('z-index', 10000);
							jQuery('.tooltipevent').fadeIn('500');
							jQuery('.tooltipevent').fadeTo('10', 1.9);
						}).mousemove(function(e) {
							jQuery('.tooltipevent').css('top', e.pageY + 10);
							jQuery('.tooltipevent').css('left', e.pageX + 20);
						});
						
					},
					eventMouseout: function(calEvent, jsEvent) {
						jQuery(this).css('z-index', 8);
						jQuery('.tooltipevent').remove();
					},
					eventClick: function (calEvent, jsEvent, view) {
						openBox('popup.php?" . getPar($par, 'mode, idPelatihan') . "&par[mode]=det&par[idPelatihan]=' + calEvent.id, '1000', '600')
						console.log(calEvent)
					},
				})

			})
		</script>";

	// 	$filter = "where idPelatihan is not null and statusPelatihan='t'";

	// 	if (!empty($par[tahunPelatihan])) {
	// 		$filter .= ' and '.$par[tahunPelatihan].' between year(mulaiPelatihan) and year(selesaiPelatihan)';
	// 	}

	// 	if (empty($par[idPelatihan])) {
	// 		$par[idPelatihan] = getField("select idPelatihan from plt_pelatihan $filter order by idPelatihan");
	// 	}

	// 	$sql = "select * from plt_pelatihan where idPelatihan='".$par[idPelatihan]."'";

	// 	$res = db($sql);

	// 	$r = mysql_fetch_array($res);

	// 	if (!empty($r[idPelatihan])) {
	// 		$periodePelatihan = getTanggal($r[mulaiPelatihan], 't').' s.d '.getTanggal($r[selesaiPelatihan], 't');
	// 	}

	// 	if (!empty($r[idPelatihan])) {
	// 		$pesertaPelatihan = getAngka($r[pesertaPelatihan]).' Orang';
	// 	}

	// 	if (!empty($r[idPelatihan])) {
	// 		$pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == 'e' ? 'Eksternal' : 'Internal';
	// 	}

	// 	if (!empty($r[idPelatihan])) {
	// 		$biayaPelatihan = 'Rp. '.getAngka($r[biayaPelatihan]);
	// 	}

	// 	if (!empty($r[filePelatihan])) {
	// 		$filePelatihan = "<a href=\"download.php?d=rencana&f=$r[idPelatihan]\"><img src=\"".getIcon($fFile.'/'.$r[filePelatihan]).'" align="left" style="padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;"></a>';
	// 	}else{
	// 		$filePelatihan = "-";
	// 	}

	// 	$text .= "<div class=\"one_half last dashboard_right\" style=\"margin-left:20px;\">
	// 	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">
	// 		<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
	// 			<legend style=\"padding:10px; margin-left:20px;\"><h4>VIEW DETAIL</h4></legend>
	// 			<p>
	// 				<label class=\"l-input-small\" style=\"width:100px;\">Pelatihan</label>
	// 				<span class=\"field\" style=\"margin-left:100px;\">$r[judulPelatihan]&nbsp;</span>
	// 			</p>
	// 			<table style=\"width:100%; margin-top:-5px; margin-bottom:-5px;\" cellpadding=\"0\" cellspacing=\"0\">
	// 				<tr>
	// 					<td style=\"width:50%\">
	// 						<p>
	// 							<label class=\"l-input-small\" style=\"width:100px;\">Sub</label>
	// 							<span class=\"field\" style=\"margin-left:100px;\">$r[subPelatihan]&nbsp;</span>
	// 						</p>
	// 					</td>
	// 					<td style=\"width:50%\">
	// 						<p>
	// 							<label class=\"l-input-small\" style=\"width:50px;\">Kode</label>
	// 							<span class=\"field\" style=\"margin-left:50px;\">$r[kodePelatihan]&nbsp;</span>
	// 						</p>
	// 					</td>
	// 				</tr>
	// 			</table>
	// 			<p>
	// 				<label class=\"l-input-small\" style=\"width:100px;\">Jumlah Peserta</label>
	// 				<span class=\"field\" style=\"margin-left:100px;\">".$pesertaPelatihan.'&nbsp;</span>
	// 			</p>
	// 			<p>
	// 				<label class="l-input-small" style="width:100px;">Tanggal</label>
	// 				<span class="field" style="margin-left:100px;">'.$periodePelatihan.'&nbsp;</span>
	// 			</p>
	// 			<p>
	// 				<label class="l-input-small" style="width:100px;">Pelaksanaan</label>
	// 				<span class="field" style="margin-left:100px;">'.$pelaksanaanPelatihan.'&nbsp;</span>
	// 			</p>';

	// 			if ($r[pelaksanaanPelatihan] == 'e') {
	// 				$text .= '<p>
	// 				<label class="l-input-small" style="width:100px;">Vendor</label>
	// 				<span class="field" style="margin-left:100px;">'.getField("select namaVendor from dta_vendor where kodeVendor='".$r[idVendor]."'").'&nbsp;</span>
	// 			</p>';
	// 		}

	// 		$text .= "<p>
	// 		<label class=\"l-input-small\" style=\"width:100px;\">Lokasi</label>
	// 		<span class=\"field\" style=\"margin-left:100px;\">$r[lokasiPelatihan]&nbsp;</span>
	// 	</p>
	// 	<p>
	// 		<label class=\"l-input-small\" style=\"width:100px;\">Biaya</label>
	// 		<span class=\"field\" style=\"margin-left:100px;\">".$biayaPelatihan.'&nbsp;</span>
	// 	</p>';

	// 	if ($r[pelaksanaanPelatihan] == 'e') {
	// 		$text .= '<p>
	// 		<label class="l-input-small" style="width:100px;">Trainer</label>
	// 		<span class="field" style="margin-left:100px;">'.getField("select namaTrainer from dta_trainer where idTrainer='".$r[idTrainer]."'").'&nbsp;</span>
	// 	</p>';
	// } else {
	// 	$text .= '<p>
	// 	<label class="l-input-small" style="width:100px;">PIC</label>
	// 	<span class="field" style="margin-left:100px;">'.getField("select name from emp where id='".$r[idPegawai]."'").'&nbsp;</span>
	// </p>';
// }

// $text .= '<p>
// <label class="l-input-small" style="width:100px;">File</label>
// <span class="field" style="margin-left:100px;">'.$filePelatihan.'&nbsp;</span>
// </p>
// <a href="?par[mode]=det'.getPar($par, 'mode,bulanPelatihan').'" class="btn btn1 btn_info2"><span>DETAIL DATA</span></a>
// </fieldset>
// </form>
// </div>';

	return $text;
}

function dataa() {

	global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;

	if (empty($_submit) && empty($par[tahunPelatihan])) {
		$par[tahunPelatihan] = date('Y');
	}

	$text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$arrTitle[$s]</h1>
		" . getBread() . "
	</div>

	<div id=\"contentwrapper\" class=\"contentwrapper\">
	
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\" style=\"position: absolute; top: 0; right: 0; margin-top: 10px; margin-right: 20px;\">
			<div>
				<input type=\"hidden\" name=\"_submit\" value=\"t\">
				Periode : " . comboYear('par[tahunPelatihan]', $par['tahunPelatihan'], '', "onchange=\"document.getElementById('form').submit();\"", "", "All") . "
				&nbsp;
				<a href=\"?" . getPar($par, 'mode, idPelatihan, bulanPelatihan') . "\" class=\"btn btn1 btn_grid\"><span>KALENDER DATA</span></a>
			</div>
		</form>

		<br>
	
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float: left;\">
				<table>
					<tr>
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Cari...\"/></td>
						<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData", 'kodeData', 'namaData', 'par[idKategori]', 'All', $par[idKategori], '', '200px', 'chosen-select').'</td>
						<td><input type="submit" value="GO" class="btn btn_search btn-small"/> </td>
					</tr>
				</table>
			</div>
		</form>

		<br clear="all" />
	
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
			<thead>
				<tr>
					<th width="20">No.</th>
					<th>Pelatihan</th>
					<th style="width:75px;">Mulai</th>
					<th style="width:75px;">Selesai</th>
					<th>Lokasi</th>
					<th>Vendor</th>
					<th style="width:50px;">Jadwal</th>
				</tr>
			</thead>
			<tbody>';

			$filter = 'where t1.idPelatihan is not null';

			if (!empty($par[tahunPelatihan])) {
				$filter .= ' and '.$par[tahunPelatihan].' between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)';
			}

			if (!empty($par[idKategori])) {
				$filter .= " and t1.idKategori='".$par[idKategori]."'";
			}

			if (!empty($par[filter])) {
				$filter .= " and (
				lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
				or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
				or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
				)";
			}

			$sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from plt_pelatihan t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";

			$res = db($sql);

			while ($r = mysql_fetch_array($res)) {
				++$no;

				$cntJadwal = getField("select count(idJadwal) from plt_pelatihan_jadwal where idPelatihan='$r[idPelatihan]'");

				$statusJadwal = $cntJadwal ? getAngka($cntJadwal) : '<img src="styles/images/f.png" title="Belum Ada">';

				$text .= "<tr>
				<td>$no.</td>
				<td><a onclick=\"openBox('popup.php?".getPar($par, 'mode,idPelatihan')."&par[mode]=det&par[idPelatihan]=$r[idPelatihan]', '1000', '600')\">$r[judulPelatihan]</a></td>
				<td align=\"center\">".getTanggal($r[mulaiPelatihan]).'</td>
				<td align="center">'.getTanggal($r[selesaiPelatihan])."</td>
				<td>$r[lokasiPelatihan]</td>
				<td>$r[namaVendor]</td>
				<td align=\"center\">$statusJadwal</td>
			</tr>";
		}

		$text .= '</tbody>
		</table>
	</div>';

return $text;
}

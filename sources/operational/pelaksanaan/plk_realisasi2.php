<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/rab/";
$fExport = "files/export/";

function hapusFile()
{
    global $db, $s, $inp, $par, $fFile, $cUsername;
    $fileRab = getField("select fileRab from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'");
    if (file_exists($fFile . $fileRab) and $fileRab != "") unlink($fFile . $fileRab);

    $sql = "update plt_pelatihan_rab set fileRab='' where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    db($sql);
    echo "<script>window.location='?par[mode]=$inp[mode]" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $db, $s, $inp, $par, $fFile, $cUsername;
    $fileRab = getField("select fileRab from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'");
    if (file_exists($fFile . $fileRab) and $fileRab != "") unlink($fFile . $fileRab);

    $sql = "delete from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    db($sql);
    echo "<script>window.location='?par[mode]=det" . getPar($par, "mode,idRab") . "';</script>";
}

function upload($idRab)
{
    global $db, $s, $inp, $par, $fFile;
    $fileRab = getField("select fileRab from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'");

    $fileUpload = $_FILES["fileRab"]["tmp_name"];
    $fileUpload_name = $_FILES["fileRab"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $fileRab = "rab-" . $par[idPelatihan] . "." . $idRab . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $fileRab);
    }

    return $fileRab;
}

function update()
{
    global $db, $s, $inp, $par, $cUsername;
    repField();
    $fileRab = upload($par[idRab]);

    $sql = "update plt_pelatihan_rab set realisasiRab='" . setAngka($inp[realisasiRab]) . "', catatanRab='$inp[catatanRab]', fileRab='" . $fileRab . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    db($sql);

    echo "<script>window.parent.location='index.php?par[mode]=det" . getPar($par, "mode,idRab") . "';</script>";
}

function ubah()
{
    global $db, $s, $inp, $par, $cUsername;
    repField();
    $fileRab = upload($par[idRab]);

    $sql = "update plt_pelatihan_rab set judulRab='$inp[judulRab]', jumlahRab='" . setAngka($inp[jumlahRab]) . "', satuanRab='$inp[satuanRab]', hargaRab='" . setAngka($inp[hargaRab]) . "', realisasiRab='" . setAngka($inp[realisasiRab]) . "', catatanRab='$inp[catatanRab]', fileRab='" . $fileRab . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    db($sql);

    echo "<script>window.parent.location='index.php?par[mode]=det" . getPar($par, "mode,idRab") . "';</script>";
}

function tambah()
{
    global $db, $s, $inp, $par, $cUsername;
    repField();
    $idRab = getField("select idRab from plt_pelatihan_rab where idPelatihan='" . $par[idPelatihan] . "' order by idRab desc limit 1") + 1;
    $fileRab = upload($idRab);

    $sql = "insert into plt_pelatihan_rab (idPelatihan, idRab, judulRab, jumlahRab, satuanRab, hargaRab, realisasiRab, catatanRab, fileRab, statusRab, createBy, createTime) values ('$par[idPelatihan]', '$idRab', '$inp[judulRab]', '" . setAngka($inp[jumlahRab]) . "', '$inp[satuanRab]', '" . setAngka($inp[hargaRab]) . "', '" . setAngka($inp[realisasiRab]) . "', '$inp[catatanRab]', '$fileRab', 'f', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    echo "<script>window.parent.location='index.php?par[mode]=det" . getPar($par, "mode,idRab") . "';</script>";
}

function realisasi()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $fFile;

    $sql = "select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[judulRab]", "anda harus mengisi uraian");
    setValidation("is_null", "inp[satuanRab]", "anda harus mengisi satuan");
    $text = getValidation();

    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Realisasi Biaya</h1>
		" . getBread(ucwords($par[mode] . " realisasi biaya")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px; padding-top:0px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>BUDGET</h4></legend>				
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Uraian</label>
					<span class=\"field\">" . $r[judulRab] . "&nbsp;</span>	
				</p>
				<p>
					<table style=\"width:100%;\">
						<tr>
							<td>
								<label class=\"l-input-small\" style=\"width:125px;\">Jumlah</label>
								<span class=\"field\">" . getAngka($r[jumlahRab]) . "&nbsp;</span>	
							</td>
							<td>			
								<label class=\"l-input-small\" style=\"width:125px;\">Satuan</label>
								<span class=\"field\">" . $r[satuanRab] . "&nbsp;</span>
							</td>
						</tr>
					</table>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Harga Satuan</label>
					<span class=\"field\">" . getAngka($r[hargaRab]) . "&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Total</label>
					<span class=\"field\">" . getAngka($r[nilaiRab]) . "&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Keterangan</label>
					<span class=\"field\">" . nl2br($r[keteranganRab]) . "&nbsp;</span>
				</p>					
			</fieldset>
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px; padding-top:0px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>REALISASI</h4></legend>				
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Total</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[realisasiRab]\" name=\"inp[realisasiRab]\" value=\"" . getAngka($r[realisasiRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"cekAngka(this);\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[catatanRab]\" name=\"inp[catatanRab]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:425px;\">$r[catatanRab]</textarea>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:125px;\">File</label>
					<div class=\"field\">";
    $text .= empty($r[fileRab]) ?
        "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:265px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:325px;\">
							<input type=\"file\" id=\"fileRab\" name=\"fileRab\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
						</div>" :
        "<a href=\"download.php?d=rab&f=$par[idPelatihan].$par[idRab]\"><img src=\"" . getIcon($fFile . "/" . $r[fileRab]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						<input type=\"file\" id=\"fileRab\" name=\"fileRab\" style=\"display:none;\" />
						<a href=\"?par[mode]=delFile&inp[mode]=set" . getPar($par, "mode") . "\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
    $text .= "</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</fieldset>
			</form>	
		</div>";
    return $text;
}

function form()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

    $sql = "select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[judulRab]", "anda harus mengisi uraian");
    setValidation("is_null", "inp[satuanRab]", "anda harus mengisi satuan");
    $text = getValidation();

    $text .= "<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">Realisasi Biaya</h1>
			" . getBread(ucwords($par[mode] . " realisasi biaya")) . "
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">	
					<p>
						<label class=\"l-input-small\" style=\"width:125px;\">Uraian</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[judulRab]\" name=\"inp[judulRab]\" value=\"$r[judulRab]\" class=\"mediuminput\" style=\"width:425px;\" maxlength=\"150\"/>
						</div>	
					</p>
					<p>
						<table style=\"width:100%;\">
							<tr>
								<td>
									<label class=\"l-input-small\" style=\"width:125px;\">Jumlah</label>
									<div class=\"field\">
										<input type=\"text\" id=\"inp[jumlahRab]\" name=\"inp[jumlahRab]\" value=\"" . getAngka($r[jumlahRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"realisasiRab();\"/>
									</div>
								</td>
								<td>						
									<label class=\"l-input-small\" style=\"width:125px;\">Satuan</label>
									<div class=\"field\">
										<input type=\"text\" id=\"inp[satuanRab]\" name=\"inp[satuanRab]\" value=\"$r[satuanRab]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
									</div>
								</td>
							</tr>
						</table>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:125px;\">Harga Satuan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[hargaRab]\" name=\"inp[hargaRab]\" value=\"" . getAngka($r[hargaRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"realisasiRab();\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:125px;\">Total</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[realisasiRab]\" name=\"inp[realisasiRab]\" value=\"" . getAngka($r[realisasiRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" readonly=\"readonly\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:125px;\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[catatanRab]\" name=\"inp[catatanRab]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:425px;\">$r[catatanRab]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:125px;\">File</label>
						<div class=\"field\">";
    $text .= empty($r[fileRab]) ?
        "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:265px;\" maxlength=\"100\" />
							<div class=\"fakeupload\" style=\"width:325px;\">
								<input type=\"file\" id=\"fileRab\" name=\"fileRab\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>" :
        "<a href=\"download.php?d=rab&f=$par[idPelatihan].$par[idRab]\"><img src=\"" . getIcon($fFile . "/" . $r[fileRab]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
							<input type=\"file\" id=\"fileRab\" name=\"fileRab\" style=\"display:none;\" />
							<a href=\"?par[mode]=delFile&inp[mode]=set" . getPar($par, "mode") . "\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
							<br clear=\"all\">";
    $text .= "</div>
						</p>
						<p>
							<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
							<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
						</p>
					</div>
				</form>	
			</div>";
    return $text;
}

function detail()
{
    global $db, $s, $inp, $par, $det, $detail, $arrTitle, $arrParameter, $fileTemp, $fFile, $menuAccess;
    $text .= "<div class=\"pageheader\">
			<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
			" . getBread(ucwords($par[mode] . " data")) . "								
		</div>
		<div class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?" . getPar($par) . "#detail\" enctype=\"multipart/form-data\">	
				<div style=\"top:10px; right:35px; position:absolute\">
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, idPelatihan") . "';\"/>
				</div>
				<div id=\"general\" style=\"margin-top:20px;\">					
					" . dtaPelatihan("PELATIHAN") . "
					<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
						<legend style=\"padding:10px; margin-left:20px;\"><h4>REALISASI BIAYA</h4></legend>
						<strong>BIAYA Rp. " . getAngka(getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'")) . " &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;REALISASI Rp. " . getAngka(getField("select sum(realisasiRab) from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'")) . " </strong>";
    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode,idRab") . "',725,450);\" style=\"float:right; margin-top:-5px; margin-bottom:10px;  margin-right:10px;\"><span>Tambah Data</span></a>";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Uraian</th>
								<th width=\"75\">Jumlah</th>
								<th width=\"75\">Satuan</th>
								<th width=\"100\">Nilai</th>
								<th width=\"100\">Biaya</th>
								<th width=\"100\">Realisasi</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "</tr>
							</thead>
							<tbody>";

    $sql = "select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' order by idRab";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
									<td>$no.</td>
									<td>$r[judulRab]</td>
									<td align=\"right\">" . getAngka($r[jumlahRab]) . "</td>
									<td>$r[satuanRab]</td>
									<td align=\"right\">" . getAngka($r[hargaRab]) . "</td>
									<td align=\"right\">" . getAngka($r[nilaiRab]) . "</td>
									<td align=\"right\">" . getAngka($r[realisasiRab]) . "</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if ($r[statusRab] == "t") {
                if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Set Realisasi\" class=\"check\"  onclick=\"openBox('popup.php?par[mode]=set&par[idRab]=$r[idRab]" . getPar($par, "mode,idRab") . "',725,575);\"><span>Set Realisasi</span></a>";
            } else {
                if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idRab]=$r[idRab]" . getPar($par, "mode,idRab") . "',725,450);\"><span>Edit</span></a>";

                if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=del&par[idRab]=$r[idRab]" . getPar($par, "mode,idRab") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            }
            $text .= "</td>";
        }
        $text .= "</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
							</table>
						</fieldset>
					</div>				
				</form>";
    return $text;
}

function detail2()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrParam;
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
    $cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[5] . "' order by urutanData limit 1");
    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[6] . "' order by urutanData limit 1");

    $text .= "
				<style>
        #inp_kodeRekening__chosen{
					min-width:250px;
				}
			</style>
			<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
					" . getBread(ucwords("import data")) . "
					<span class=\"pagedesc\">&nbsp;</span> 
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
					<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
						<div style=\"position:absolute; right:20px; top:14px;\">
							<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
						</div>
						<!--<fieldset>
						<legend>KATALOG PROGRAM</legend>
						<p>
							<label class=\"l-input-small\">Modul</label>
							<span class=\"field\">
								&nbsp;" . getField("SELECT keterangan from app_site WHERE kodeSite = '$r[modul_pelatihan]'") . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Kategori Level</label>
							<span class=\"field\">
								&nbsp;" . getField("SELECT namaMenu FROM app_menu WHERE kodeMenu = '$r[kategori_level_pelatihan]'") . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Program</label>
							<span class=\"field\">
								&nbsp;" . getField("SELECT program FROM ctg_program WHERE id_program = '$r[program_pelatihan]'") . "
							</span>
						</p>
					</fieldset>
					<br>-->
					<fieldset>
						<legend>PELATIHAN</legend>
						<p>
							<label class=\"l-input-small\">Judul Pelatihan</label>
							<span class=\"field\">
								&nbsp;" . $r[judulPelatihan] . "
							</span>
						</p>
						<table style='width:100%;'>
							<tr>
								<td style='width:50%;'>
									<p>
										<label class=\"l-input-small2\">Tanggal Mulai</label>
										<span class=\"field\">
											&nbsp;" . getTanggal($r[mulaiPelatihan]) . "
										</span>
									</p>
								</td>
								<td style='width:50%;'>
									<p>
										<label class=\"l-input-small2\">Tanggal Selesai</label>
										<span class=\"field\">
											&nbsp;" . getTanggal($r[selesaiPelatihan]) . "
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
											&nbsp;" . $r[subPelatihan] . "
										</span>
									</p>
								</td>
								<td style='width:50%;'>
									<p>
										<label class=\"l-input-small2\">Kode</label>
										<span class=\"field\">
											&nbsp;" . $r[kodePelatihan] . "
										</span>
									</p>
								</td>
							</tr>
						</table>
						<p>
							<label class=\"l-input-small\">Kategori</label>
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
								&nbsp;" . getAngka($r[pesertaPelatihan]) . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Pelaksanaan</label>
							<span class=\"field\">
								&nbsp;" . ($r[pelaksanaanPelatihan] == 'e' ? 'Eksternal' : 'Internal') . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Vendor</label>
							<span class=\"field\">
								&nbsp;" . getField("SELECT namaVendor from dta_vendor where kodeVendor = '$r[idVendor]'") . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Koordinator</label>
							<span class=\"field\">
								&nbsp;" . getField("SELECT upper(namaTrainer) as namaTrainer from dta_trainer where idTrainer = '$r[idTrainer]'") . "
							</span>
						</p>
						<p>
							<label class=\"l-input-small\">Lokasi</label>
							<span class=\"field\">
								&nbsp;" . $r[lokasiPelatihan] . "
							</span>
						</p>
					</fieldset>
				</form>
			</div>
		</div>";
    return $text;
}

function lihat()
{
    global $db, $s, $inp, $par, $_submit, $arrTitle, $arrParameter, $menuAccess;
    if (empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
    $text .= "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<div style=\"padding-bottom:10px;\">
		</div>
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<input type=\"hidden\" name=\"_submit\" value=\"t\">
			<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : " . comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"", "", "All") . "</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>								
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td>" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' order by namaData", "kodeData", "namaData", "par[idKategori]", "All", $par[idKategori], "", "200px", "chosen-select") . "</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>	
			<div id=\"pos_r\" style=\"float:right;\">
				<a href=\"?par[mode]=xls" . getPar($par, "mode") . "\" class=\"btn btn1 btn_inboxi\" title=\"Export Data\"><span>Export Data</span></a>
			</div>			
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Pelatihan</th>					
					<th>Lokasi</th>
					<th>PIC</th>					
					<th width=\"125\">Biaya</th>
					<th width=\"125\">Realisasi</th>
				</tr>
			</thead>
			<tbody>";

    $filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
    if (!empty($par[tahunPelatihan]))
        $filter .= " and " . $par[tahunPelatihan] . " between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";

    if (!empty($par[idKategori]))
        $filter .= " and t1.idKategori='" . $par[idKategori] . "'";

    if (!empty($par[filter]))
        $filter .= " and (
				lower(t1.judulPelatihan) like '%" . strtolower($par[filter]) . "%'
				or lower(t1.lokasiPelatihan) like '%" . strtolower($par[filter]) . "%'
				or lower(t1.namaPegawai) like '%" . strtolower($par[filter]) . "%'
				or lower(t2.namaTrainer) like '%" . strtolower($par[filter]) . "%'
				)";

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
				select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
				) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $filter order by t1.idPelatihan";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        list($nilaiRab, $nilaiRealisasi) = explode("\t", getField("select concat(sum(nilaiRab), '\t', sum(realisasiRab)) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'"));

        $text .= "<tr>
					<td>$no.</td>			
					<td><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "\">$r[judulPelatihan]</a></td>
					<td>$r[lokasiPelatihan]</td>
					<td>$r[namaPic]</td>
					<td align=\"right\">" . getAngka($nilaiRab) . "</td>
					<td align=\"right\">" . getAngka($nilaiRealisasi) . "</td>
				</tr>";

        $totalRab += $nilaiRab;
        $totalRealisasi += $nilaiRealisasi;
    }

    $text .= "</tbody>
			<tfoot>
				<tr>					
					<td colspan=\"4\" style=\"text-align:right\"><strong>TOTAL<strong></td>
					<td style=\"text-align:right\">" . getAngka($totalRab) . "</td>
					<td style=\"text-align:right\">" . getAngka($totalRealisasi) . "</td>
				</tr>
			</tfoot>
		</table>
	</div>";
    $text .= "<iframe src=\"download.php?d=exp&f=" . $arrTitle[$s] . "_" . date('dmY') . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    return $text;
}

function xls()
{
    global $s, $par, $fExport, $cNama, $arrTitle;
    require_once 'plugins/PHPExcel.php';

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s] . "_" . date("dmY"));
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

    $objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
    $objPHPExcel->getActiveSheet()->mergeCells('A2:F2');
    $objPHPExcel->getActiveSheet()->mergeCells('A3:F3');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->setCellValue('A4', "NO");
    $objPHPExcel->getActiveSheet()->setCellValue('B4', "PELATIHAN");
    $objPHPExcel->getActiveSheet()->setCellValue('C4', "LOKASI");
    $objPHPExcel->getActiveSheet()->setCellValue('D4', "PIC");
    $objPHPExcel->getActiveSheet()->setCellValue('E4', "BIAYA");
    $objPHPExcel->getActiveSheet()->setCellValue('F4', "REALISASI");

    $no = 0;
    $currentRow = 5;
    $filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
    if (!empty($par[tahunPelatihan]))
        $filter .= " and " . $par[tahunPelatihan] . " between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";

    if (!empty($par[idKategori]))
        $filter .= " and t1.idKategori='" . $par[idKategori] . "'";

    if (!empty($par[filter]))
        $filter .= " and (
	lower(t1.judulPelatihan) like '%" . strtolower($par[filter]) . "%'
	or lower(t1.lokasiPelatihan) like '%" . strtolower($par[filter]) . "%'
	or lower(t1.namaPegawai) like '%" . strtolower($par[filter]) . "%'
	or lower(t2.namaTrainer) like '%" . strtolower($par[filter]) . "%'
	)";

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
	select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
	) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) $filter order by t1.idPelatihan";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        list($nilaiRab, $nilaiRealisasi) = explode("\t", getField("select concat(sum(nilaiRab), '\t', sum(realisasiRab)) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'"));

        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $currentRow, $no . ".", PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $currentRow, $r[judulPelatihan]);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $currentRow, $r[lokasiPelatihan], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('D' . $currentRow, $r[namaPic], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('E' . $currentRow, getAngka($nilaiRab), PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('F' . $currentRow, getAngka($nilaiRealisasi), PHPExcel_Cell_DataType::TYPE_STRING);

        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':F' . $currentRow)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':F' . $currentRow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':F' . $currentRow)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':F' . $currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $currentRow++;
        $totalRab += $nilaiRab;
        $totalRealisasi += $nilaiRealisasi;
    }

    $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':D' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $currentRow . ':F' . $currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $currentRow . ':D' . $currentRow);
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $currentRow, "Total");
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $currentRow, getAngka($totalRab, 0), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $currentRow, getAngka($totalRealisasi, 0), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getStyle('A' . $currentRow . ':F' . $currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('B3B8BA');
    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);
    $objPHPExcel->setActiveSheetIndex(0);
    // Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fExport . $arrTitle[$s] . "_" . date('dmY') . ".xls");
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess;

    switch ($par[mode]) {

        case "detail2":
            $text = detail2();
            break;

        case "delFile":
            if (isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
            break;
        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;
        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
            break;
        case "set":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? realisasi() : update(); else $text = lihat();
            break;
        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
            break;
        case "app":
            $text = approval();
            break;
        case "det":
            $text = detail();
            break;
        case "xls":
            xls();
        default:
            $text = lihat();
            break;
    }
    return $text;
}

?>
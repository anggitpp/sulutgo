<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";

function getContent($par)
{

    switch ($par['mode']) {

        case "detail2":
            $text = detail2();
            break;

        case "detail_peserta_biaya":
            $text = detail_peserta_biaya();
            break;

        case "det":
            $text = detail();
            break;

        case "detJadwal":
            $text = jadwal();
            break;

        case "detPeserta":
            $text = peserta();
            break;

        case "det_pelatihan":
            $text = detail_pelatihan();
            break;

        default:
            $text = lihat();
            break;
    }

    return $text;
}

function form()
{
    global $par;

    $sql = "select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and idRab='$par[idRab]'";
    $res = db($sql);

    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[judulRab]", "anda harus mengisi uraian");
    setValidation("is_null", "inp[satuanRab]", "anda harus mengisi satuan");
    $text = getValidation();

    $text .= "<div class=\"centercontent contentpopup\">
      <div class=\"pageheader\">
        <h1 class=\"pagetitle\">Biaya</h1>
        " . getBread(ucwords($par[mode] . " biaya")) . "
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
                    <label class=\"l-input-small\" style=\"width:125px;\">Biaya Investasi</label>
                    <div class=\"field\">
                      <input type=\"text\" id=\"inp[jumlahRab]\" name=\"inp[jumlahRab]\" value=\"" . getAngka($r[jumlahRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"nilaiRab();\"/>
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
              <table style=\"width:100%;\">
                <tr>
                  <td>
                    <label class=\"l-input-small\" style=\"width:125px;\">Jumlah Peserta</label>
                    <div class=\"field\">
                      <input type=\"text\" id=\"inp[hargaRab]\" name=\"inp[hargaRab]\" value=\"" . getAngka($r[hargaRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"nilaiRab();\"/>
                    </div>
                  </td>
                  <td>            
                    <label class=\"l-input-small\" style=\"width:125px;\">Satuan</label>
                    <div class=\"field\">
                      <input type=\"text\" id=\"inp[satuanHarga]\" name=\"inp[satuanHarga]\" value=\"$r[satuanHarga]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
                    </div>
                  </td>
                </tr>
              </table>
            </p>
    
            <p>
              <table style=\"width:100%;\">
                <tr>
                  <td>
                    <label class=\"l-input-small\" style=\"width:125px;\">Jumlah Hari</label>
                    <div class=\"field\">";
    if ($par[mode] == "add") {
        $r[hargaPengalih] = "1";
    } else {
        $r[hargaPengalih] = $r[hargaPengalih];
    }
    $text .= "<input type=\"text\" id=\"inp[hargaPengalih]\" name=\"inp[hargaPengalih]\" value=\"" . getAngka($r[hargaPengalih]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" onkeyup=\"nilaiRab();\"/>
    
                    </div>
                  </td>
                  <td>            
                    <label class=\"l-input-small\" style=\"width:125px;\">Satuan</label>
                    <div class=\"field\">
                      <input type=\"text\" id=\"inp[satuanPengalih]\" name=\"inp[satuanPengalih]\" value=\"$r[satuanPengalih]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
                    </div>
                  </td>
                </tr>
              </table>
            </p>
    
            
            <p>
              <label class=\"l-input-small\" style=\"width:125px;\">Total</label>
              <div class=\"field\">
                <input type=\"text\" id=\"inp[nilaiRab]\" name=\"inp[nilaiRab]\" value=\"" . getAngka($r[nilaiRab]) . "\" class=\"mediuminput\" style=\"width:100px; text-align:right\" readonly=\"readonly\"/>
              </div>
            </p>
            <p>
              <label class=\"l-input-small\" style=\"width:125px;\">Keterangan</label>
              <div class=\"field\">
                <textarea id=\"inp[keteranganRab]\" name=\"inp[keteranganRab]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:425px;\">$r[keteranganRab]</textarea>
              </div>
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

    $text = "<div class=\"pageheader\">
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
    global $db, $s, $inp, $par, $_submit, $arrTitle, $arrParameter, $menuAccess, $areaCheck;

    if (empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
    $areaCheck2 = $areaCheck;

    $text .= "<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread() . "
  </div>    
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <div style=\"padding-bottom:10px;\">
    </div>
    <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
      <input type=\"hidden\" name=\"_submit\" value=\"t\">
      <div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">
      </div>
      <div id=\"pos_l\" style=\"float:left;\">
        <table>
          <tr>
            <td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\"/></td>
            <td>" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' order by namaData", "kodeData", "namaData", "par[idKategori]", "All", $par[idKategori], "", "200px", "chosen-select") . "</td>
            <td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
          </tr>
        </table>
      </div>
      <div id=\"right\" style=\"float:right; margin-bottom:5px;\"> 
        &nbsp;Periode : " . comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"", "", "All") . "&nbsp;<a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
      </div>
    </form>
    <br clear=\"all\" />
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
      <thead>
        <tr>
          <th rowspan=\"2\" width=\"20\">No.</th>         
          <th rowspan=\"2\">Pelatihan</th>          
          <th rowspan=\"2\">Lokasi</th>
          <th rowspan=\"2\">PIC</th>
          <th rowspan=\"2\" style=\"width:50px;\">Approval</th>
          <th colspan=\"3\"style=\"width:200px;\">Biaya</th>
        </tr>
        <tr>
          <th style=\"width:66.6px; vertical-align:right;\">Perjalanan Dinas</th>
          <th style=\"width:66.6px; vertical-align:right;\">Program</th>
          <th style=\"width:66.6px; vertical-align:right;\">Total</th>

        </tr>
      </thead>
      <tbody>";

    $filter = "where t1.idPelatihan is not null";
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

    if (!empty($par[lokasi]))
        $filter .= " AND t1.idLokasi IN ($areaCheck)";

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
        select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
        ) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) left outer join budget_pelatihan_perencanaan a on (a.id_pelatihan_perencanaan = t1.idPelatihan) $filter order by t1.idPelatihan";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        $nilaiRab = getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'");
        $idPic = getField("SELECT id_pegawai_team from plt_team where id_pelatihan_team = '$r[idPelatihan]' AND id_jabatan_team = '7310'");
        $namaPIC = getField("SELECT name from emp where id = '$idPic'");
        $nilaiPegawai = getField("select sum(u_inap + u_makan + u_cuci + u_jalan + u_tiket) from budget_pelatihan where id_pelatihan='$r[program_pelatihan]' AND id_pelatihan_perencanaan = '$r[idPelatihan]'");


        $statusPelatihan = "<img src=\"styles/images/p.png\" title='Belum Diproses'>";
        if ($r[statusPelatihan] == "t") $statusPelatihan = "<img src=\"styles/images/t.png\" title='Setuju'>";
        if ($r[statusPelatihan] == "f") $statusPelatihan = "<img src=\"styles/images/f.png\" title='Tolak'>";
        if ($r[statusPelatihan] == "p") $statusPelatihan = "<img src=\"styles/images/o.png\" title='Tunda'>";

        $text .= "<tr>
          <td>$no.</td>     
          <td><a href='#' onclick=\"openBox('popup.php?par[mode]=detail2&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode, idPelatihan") . "',940,550)\">" . $r[judulPelatihan] . "</a></td>
          <td>$r[lokasiPelatihan]</td>
          <td>$namaPIC</td>
          <td align=\"center\">";
        $text .= empty($r[statusPelatihan]) ? "$statusPelatihan" : "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=app&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "',800,375);\" title=\"Approval\">$statusPelatihan</a></td>";
        $text .= "<td align=\"right\"><a href=\"index.php?par[mode]=detail_peserta_biaya&par[program]=$r[program_pelatihan]&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,program,idPelatihan") . "\">" . getAngka($nilaiPegawai) . "</a></td>";
        $text .= "<td align=\"right\"><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "\">" . getAngka($nilaiRab) . "</a></td>";
        $text .= "<td align=\"right\">" . getAngka($nilaiPegawai + $nilaiRab) . "</td>";
        $text .= "</tr>";
        $totalsemua1 = $nilaiRab + $nilaiPegawai;
        $totalRab += $nilaiRab;
        $totalPegawai += $nilaiPegawai;
        $totalSemua += $totalsemua1;
    }

    $text .= "</tbody>
          <tfoot>
            <tr>
              <td>&nbsp;</td>               
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td style=\"text-align:center\"><strong>TOTAL<strong></td>
              <td style=\"text-align:right\">" . getAngka($totalPegawai) . "</td>
              <td style=\"text-align:right\">" . getAngka($totalRab) . "</td>
              <td style=\"text-align:right\">" . getAngka($totalSemua) . "</td>

            </tr>
          </tfoot>
        </table>
      </div>";
    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    $text .= "
     <script>
       jQuery(\"#btnExport\").live('click', function(e){
        e.preventDefault();
        window.location.href=\"?par[mode]=xls\"+\"" . getPar($par, "mode") . "\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
      });
    </script>
    ";
    return $text;
}

function detail_peserta_biaya()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrColor, $arrParameter;

    $sql = "SELECT * FROM ctg_program WHERE id_program = '$par[program]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $idBiaya = getField("SELECT id_pelatihan_perencanaan FROM budget_pelatihan_perencanaan WHERE id_pelatihan_perencanaan = '$par[idPelatihan]'");

    $namaModul = getField("SELECT keterangan FROM app_site WHERE kodeSite='$r[id_modul]'");
    $namaKategori = getField("SELECT keterangan FROM app_menu WHERE kodeMenu='$r[id_kategori]'");

    if (empty($_submit) && empty($par[tahun])) $par[tahun] = date('Y');
    $text .= "
    <div class=\"pageheader\">
      <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
      <div class=\"simpan\" style=\"float:right; margin-top:-50px; margin-right:20px;\">";

    $text .= "<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, program,pelatihan") . "';\"/>";


    $text .= "</div>
        " . getBread() . "
        <span class=\"pagedesc\">&nbsp;</span>

      </div> 

      <!--<p style=\"position: absolute; right: 20px; top: 10px;\">
      </p>   
      
      <div id=\"contentwrapper\" class=\"contentwrapper\">
       <form action=\"\" method=\"post\" id=\"form2\" class=\"stdform\" onsubmit=\"return false;\">
         <div id=\"pos_l\" style=\"float:left;\">

         </div>



         <fieldset style=\"padding:10px; border-radius: 10px; margin-bottom: 20px;\">
          <legend>Katalog Program</legend>
          <p>
            <label class=\"l-input-small\">Modul</label>
            <span class=\"field\">" . $namaModul . "&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small\">Kategori</label>
            <span class=\"field\">" . $namaKategori . "&nbsp;</span>
          </p>
          <p>
            <label class=\"l-input-small\">Program</label>
            <span class=\"field\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=popup_detail&par[id_program]=$par[program]" . getPar($par, "mode, id_program") . "', 1000, 625)\">
              <img src=\"styles/images/icons/detail.png\" style=\"padding: 0px 2px; margin-bottom: -3px;\" />$r[program]&nbsp;</a></span>
            </p>
            <p>
              <label class=\"l-input-small\">Durasi</label>
              <span class=\"field\">$r[durasi] Hari</span>
            </p>
          </fieldset>
        </form>
        <div class=\"widgetbox\">
          <div class=\"title\" style=\"margin:0;\">
            <h3>" . $arrTitle[$s] . "</h3>
          </div>
        </div>-->

        <div id=\"contentwrapper\" class=\"contentwrapper\">

        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\"> 

        " . dtaPelatihan() . "

        <br>

        <div class=\"widgetbox\">
          <div class=\"title\" style=\"margin:0;\">
            <h3>" . $arrTitle[$s] . "</h3>
          </div>
        </div>

          <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:20px;\" id=\"dyntables\">
            <thead>
              <tr>
                <th rowspan = \"2\"width=\"10\">No.</th>
                <th rowspan = \"2\"width=\"190\">Nama</th>
                <th rowspan = \"2\"width=\"175\">Jabatan</th>
                <th rowspan = \"2\"width=\"115\">Uang Saku</th>
                <th rowspan = \"2\"width=\"115\">Uang Penginapan</th>
                <th rowspan = \"2\"width=\"115\">Uang Makan</th>
                <th rowspan = \"2\"width=\"115\">Uang Cuci</th>
                <th rowspan = \"2\"width=\"115\">Uang Transport (Lokal)</th>
                <th rowspan = \"2\"width=\"115\">Uang Tiket</th>
              </tr>


            </thead>
            <tbody>";

    $res = db("SELECT *, b.`idPegawai` AS `idp` FROM `plt_pelatihan` AS `a` JOIN `plt_pelatihan_peserta` AS `b` ON a.`idPelatihan` = b.`idPelatihan` WHERE a.`idPelatihan` = '$par[idPelatihan]'");

    while ($r = mysql_fetch_array($res)) {
        $arrMaster[] = $r;
    }

    $arrKode = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");

    foreach ($arrMaster as $key => $value) {

        $no++;

        $jabatanid = getField("SELECT `rank` FROM `emp_phist` WHERE `parent_id` = '$value[idp]' AND `status` = '1' ORDER BY `id` DESC");

        $pelatihan = db("SELECT * FROM `budget_pelatihan` WHERE `id_pelatihan` = '$par[program]' AND `id_pelatihan_perencanaan` = '$par[idPelatihan]' AND `id_pegawai` = '$value[idp]'");
        $pelatihan = mysql_fetch_assoc($pelatihan);

        // <tr>
        //   <td align=\"right\">$no.</td>
        //   <td align=\"left\">".getField("SELECT name FROM emp WHERE id= '$value[idp]' ")."</td>
        //   <td align=\"left\">".getField("SELECT namaData FROM mst_data where kodeData = '$jabatanid'")."</td>
        //   <td align=\"left\">".comboKey("inp[data][$value[idp]][u_hotel]",  $arrBudget, "$pelatihan[u_hotel]", "", "100%")."</td>
        //   <td align=\"left\">".comboKey("inp[data][$value[idp]][u_harian]",  $arrBudget_UH, "$pelatihan[u_harian]", "", "100%")."</td>
        //   <td align=\"left\">".comboKey("inp[data][$value[idp]][u_transport]",  $arrBudget_SUT, "$pelatihan[u_transport]", "", "100%")."</td>
        // </tr>

        $text .= "
              <tr>
                <td align=\"right\">$no.</td>
                <td align=\"left\">" . getField("SELECT name FROM emp WHERE id= '$value[idp]' ") . "</td>
                <td align=\"left\">" . getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$jabatanid'") . "</td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_saku]\" value=\"" . getAngka($pelatihan['u_saku']) . "\" class=\"mediuminput\" maxlength=\"13\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_inap]\" value=\"" . getAngka($pelatihan['u_inap']) . "\" class=\"mediuminput\" maxlength=\"13\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_makan]\" value=\"" . getAngka($pelatihan['u_makan']) . "\" class=\"mediuminput\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_cuci]\" value=\"" . getAngka($pelatihan['u_cuci']) . "\" class=\"mediuminput\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_jalan]\" value=\"" . getAngka($pelatihan['u_jalan']) . "\" class=\"mediuminput\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
                <td align=\"left\"><input type=\"text\" name=\"inp[data][$value[idp]][u_tiket]\" value=\"" . getAngka($pelatihan['u_tiket']) . "\" class=\"mediuminput\" style=\"width:95%;\" onkeyup=\"jQuery(this).val(formatNumber(jQuery(this).val()))\"/></td>
              </tr> 

              <input type=\"hidden\" name=\"inp[data][$value[idp]][id_pelatihan]\" value=\"$par[program]\"></input>
              <input type=\"hidden\" name=\"inp[data][$value[idp]][id_jabatan]\" value=\"$jabatanid\"></input>
              <input type=\"hidden\" name=\"inp[data][$value[idp]][id]\" value=\"$pelatihan[id_bpelatihan]\"></input>
              <input type=\"hidden\" name=\"inp[data][$value[idp]][id_pelatihan_perencanaan]\" value=\"$par[idPelatihan]\"></input>";

        $total_saku += $pelatihan['u_saku'];
        $total_inap += $pelatihan['u_inap'];
        $total_makan += $pelatihan['u_makan'];
        $total_cuci += $pelatihan['u_cuci'];
        $total_jalan += $pelatihan['u_jalan'];
        $total_tiket += $pelatihan['u_tiket'];

    }

    $text .= "

            <tr>
              <td align=\"right\" colspan=\"3\"><strong>Total</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_saku) . "</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_inap) . "</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_makan) . "</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_cuci) . "</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_jalan) . "</strong></td>
              <td align=\"right\"><strong>Rp. " . getAngka($total_tiket) . "</strong></td>
            </tr>

          </tbody>
        </table>
      </form>
    </div>";

    return $text;
}

function proses_akademik_biaya()
{

    global $db, $s, $par, $dta_, $not, $cUsername, $inp;

    unset($inp["page"]);

    foreach ($inp['data'] as $key => $value) {

        $value['u_saku'] = str_replace(",", "", $value['u_saku']);
        $value['u_inap'] = str_replace(",", "", $value['u_inap']);
        $value['u_makan'] = str_replace(",", "", $value['u_makan']);
        $value['u_cuci'] = str_replace(",", "", $value['u_cuci']);
        $value['u_jalan'] = str_replace(",", "", $value['u_jalan']);
        $value['u_tiket'] = str_replace(",", "", $value['u_tiket']);

        if (!empty($value['id'])) {

            $sql_biaya = "UPDATE `budget_pelatihan` SET

        `u_saku`      = '$value[u_saku]',
        `u_inap`      = '$value[u_inap]',
        `u_makan`     = '$value[u_makan]',
        `u_cuci`      = '$value[u_cuci]',
        `u_jalan`     = '$value[u_jalan]',
        `u_tiket`     = '$value[u_tiket]',
        `update_by`   = '$cUsername',
        `update_date` = NOW()
        
        WHERE

        `id_bpelatihan` = '$value[id]' AND `id_pelatihan_perencanaan` = '$value[id_pelatihan_perencanaan]'";

        } else {

            $sql_biaya = " INSERT INTO `budget_pelatihan`( `id_pelatihan`, `id_pelatihan_perencanaan`,`id_pegawai`, `id_jabatan`, `u_saku`, `u_inap`, `u_makan`, `u_cuci`, `u_jalan`, `u_tiket`, `create_by`, `create_date`) VALUES ('$value[id_pelatihan]','$value[id_pelatihan_perencanaan]','$key','$value[id_jabatan]','$value[u_saku]', '$value[u_inap]', '$value[u_makan]', '$value[u_cuci]', '$value[u_jalan]', '$value[u_tiket]', '$cUsername', NOW());";

        }

        db($sql_biaya);
    }

    echo "<script>window.location='?par[mode]=detail_peserta_biaya" . getPar($par, "mode") . "';</script>";
}

function detail_pelatihan()
{
    global $db, $s, $inp, $par, $det, $detail, $arrTitle, $arrParameter, $fileTemp, $menuAccess;
    $sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";

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
        " . dtaPelatihan() . "
        <ul class=\"hornav\">
          <li class=\"current\"><a href=\"#jadwal\">Jadwal</a></li>
          <li><a href=\"#peserta\">Peserta</a></li>
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

    $sql = "select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]'order by mulaiJadwal";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
                <td>$no.</td>
                <td>$r[judulJadwal]</td>                    
                <td align=\"center\">" . getTanggal($r[tanggalJadwal]) . " " . substr($r[mulaiJadwal], 0, 5) . "</td>
                <td align=\"center\">" . getTanggal($r[tanggalJadwal]) . " " . substr($r[selesaiJadwal], 0, 5) . "</td>
                <td align=\"center\">
                  <a href=\"#Detail\" title=\"Detail Data\" class=\"detail\"  onclick=\"openBox('popup.php?par[mode]=detJadwal&par[idJadwal]=$r[idJadwal]" . getPar($par, "mode,idJadwal") . "',725,450);\"><span>Detail</span></a>
                </td>
              </tr>";
        $no++;
    }

    if ($no == 1)
        $text .= "<tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>               
            <td>&nbsp;</td>
          </tr>";

    $text .= "</tbody>
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

    $sql = "select * from plt_pelatihan_peserta t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idPelatihan='$par[idPelatihan]' order by t2.name";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $jabatanid = getField("SELECT pos_name from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");
        $dirid = getField("SELECT div_id from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");
        $posid = getField("SELECT posisi_kerja from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");
        $text .= "<tr>
             <td>$no.</td>
             <td>" . strtoupper($r[name]) . "</td>
             <td>" . getField("SELECT namaData FROM mst_data where kodeData ='$jabatanid'") . "</td>
             <td>" . getField("SELECT namaData FROM mst_data where kodeData ='$posid'") . "</td>
             <td align=\"right\">" . getAngka($r[umurPeserta]) . " Tahun</td>
             <td align=\"center\">
              <a href=\"#Detail\" title=\"Detail Data\" class=\"detail\"  onclick=\"openBox('popup.php?par[mode]=detPeserta&par[idPegawai]=$r[idPegawai]" . getPar($par, "mode,idPegawai") . "',725,450);\"><span>Detail</span></a>
            </td>
          </tr>";
        $no++;
    }

    if ($no == 1)
        $text .= "<tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>";

    $text .= "</tbody>
    </table>
  </div>
</div>        
</form>";
    return $text;
}

function peserta()
{
    global $db, $s, $inp, $par, $hari, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from dta_pegawai where id='$par[idPegawai]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $jabatanid = getField("SELECT pos_name from sdm_posisi where id_pegawai = '$par[idPegawai]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$par[idPegawai]')");
    $dirid = getField("SELECT div_id from sdm_posisi where id_pegawai = '$par[idPegawai]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$par[idPegawai]')");
    $posid = getField("SELECT posisi_kerja from sdm_posisi where id_pegawai = '$par[idPegawai]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$par[idPegawai]')");
    $text .= "<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">Peserta Pelatihan</h1>
    " . getBread(ucwords("peserta pelatihan")) . "
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">  
      <input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\" style=\"position:absolute; top:0; right:0; margin-right:20px;\"/>
      <div id=\"general\" class=\"subcontent\">           
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">Nama</label>
          <span class=\"field\">" . $r[name] . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">NPP</label>
          <span class=\"field\">" . $r[reg_no] . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">Jabatan</label>
          <span class=\"field\">" . getField("SELECT namaData FROM mst_data where kodeData ='$jabatanid'") . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">Posisi</label>
          <span class=\"field\">" . getField("select namaData from mst_data where kodeData='$posid'") . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">Alamat</label>
          <span class=\"field\">" . nl2br($r[dom_address]) . "&nbsp;</span>
        </p>
        <table style=\"width:100%\">
          <tr>
            <td style=\"width:50%\">
              <p>
                <label class=\"l-input-small\" style=\"width:100px;\">Propinsi</label>
                <span class=\"field\">" . getField("select namaData from mst_data where kodeData='$r[dom_prov]'") . "&nbsp;</span>
              </p>
            </td>
            <td style=\"width:50%\">
              <p>
                <label class=\"l-input-small\" style=\"width:100px;\">Kota</label>
                <span class=\"field\">" . getField("select namaData from mst_data where kodeData='$r[dom_city]'") . "&nbsp;</span>
              </p>
            </td>
          </tr>
          <tr>
            <td>
              <p>
                <label class=\"l-input-small\" style=\"width:100px;\">Telepon</label>
                <span class=\"field\">" . $r[phone_no] . "&nbsp;</span>
              </p>
            </td>
            <td>
              <p>
                <label class=\"l-input-small\" style=\"width:100px;\">Handphone</label>
                <span class=\"field\">" . $r[cell_no] . "&nbsp;</span>
              </p>
            </td>
          </tr>         
        </table>
        <p>
          <label class=\"l-input-small\" style=\"width:100px;\">Email</label>
          <span class=\"field\">" . $r[cell_no] . "&nbsp;</span>
        </p>
      </div>
    </form> 
  </div>";
    return $text;
}

function jadwal()
{
    global $db, $s, $inp, $par, $hari, $arrTitle, $arrParameter, $menuAccess;
    $sql = "select * from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]' and idJadwal='$par[idJadwal]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">Jadwal Pelatihan</h1>
    " . getBread(ucwords("jadwal pelatihan")) . "
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">  
      <input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\" style=\"position:absolute; top:0; right:0; margin-right:20px;\"/>
      <div id=\"general\" class=\"subcontent\"> 
        <p>
          <label class=\"l-input-small\">Tanggal</label>
          <span class=\"field\">" . getTanggal($r[tanggalJadwal], "t") . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Uraian</label>
          <span class=\"field\">" . $r[judulJadwal] . "&nbsp;</span>
        </p>          
        <p>
          <label class=\"l-input-small\">Mulai</label>
          <span class=\"field\">" . substr($r[mulaiJadwal], 0, 5) . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Selesai</label>
          <span class=\"field\">" . substr($r[selesaiJadwal], 0, 5) . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Keterangan</label>
          <span class=\"field\">" . nl2br($r[keteranganJadwal]) . "&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">PIC</label>
          <span class=\"field\">" . getField("select name from emp where id='" . $r[idPegawai] . "'") . "&nbsp;</span>
        </p>
      </div>
    </form> 
  </div>";
    return $text;
}

function xls()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $cNama, $fFile, $menuAccess, $areaCheck, $cID;

    $direktori = $fFile;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";
    $field = array("no", "pelatihan", "lokasi", "pic", "approval", "biaya" => array("peserta", "rab", "total"));

    $filter = "where t1.idPelatihan is not null";
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

    if (!empty($par[lokasi]))
        $filter .= " AND t1.idLokasi IN ($areaCheck)";

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
  select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
  ) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) left outer join budget_pelatihan_perencanaan a on (a.id_pelatihan_perencanaan = t1.idPelatihan) $filter order by t1.idPelatihan";

    $res = db($sql);
    $no = 0;

    while ($r = mysql_fetch_array($res)) {
        $no++;
        $nilaiRab = getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'");
        $idPic = getField("SELECT id_pegawai_team from plt_team where id_pelatihan_team = '$r[idPelatihan]' AND id_jabatan_team = '7310'");
        $namaPIC = getField("SELECT name from emp where id = '$idPic'");
        $nilaiPegawai = getField("select sum(u_hotel + u_harian + u_transport) from budget_pelatihan where id_pelatihan='$r[program_pelatihan]' AND id_pelatihan_perencanaan = '$r[idPelatihan]'");


        if ($r[statusPelatihan] == "t") $statusPelatihan = "Setuju";
        if ($r[statusPelatihan] == "f") $statusPelatihan = "Ditolak";
        if ($r[statusPelatihan] == "p") $statusPelatihan = "Ditunda";
        if ($r[statusPelatihan] == "") $statusPelatihan = "Belum Diproses";

        $data[] = array($no . "\t center",
            $r[judulPelatihan] . "\t left",
            $r[lokasiPelatihan] . "\t left",
            $namaPIC . "\t left",
            $statusPelatihan . "\t left",
            getAngka($nilaiPegawai) . "\t right",
            getAngka($nilaiRab) . "\t right",
            getAngka($nilaiPegawai + $nilaiRab) . "\t right");
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}
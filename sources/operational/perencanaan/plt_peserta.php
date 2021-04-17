<?php

if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/export/";

$path_import = "files/imports/";
$file_log = "files/logs/data_perserta.log";
$file_template = "files/templates/template-peserta.xlsx";

function getContent($par)
{
    global $s, $_submit, $menuAccess;

    switch ($par['mode']) {

        case "detail":
            $text = detail();
            break;

        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
            break;

        case "add":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
            break;

        case "get":
            $text = dataa();
            break;

        case "addPeserta":
            if (isset($menuAccess[$s]['add']) || isset($menuAccess[$s]['add']))
                include "dlg_peserta.php";
            else
                $text = lihat();
            break;

        case "import":
            formImport("Import Peserta");
            break;

        case "decode":
            echo uploadXLS();
            break;

        case "insert_import":
            if (isset($menuAccess[$s]["add"]))
                insertByImport();
            else
                echo "Membutuhkan akses!";
            break;

        default:
            $text = lihat();
            break;

    }

    return $text;
}

function lihat()
{

    global $s, $par, $_submit, $arrTitle, $arrParameter, $areaCheck;

    if (empty($_submit) && empty($par[tahunPelatihan]))
        $par[tahunPelatihan] = date('Y');

    $areaCheck2 = $areaCheck;

    if (!empty($par[lokasi]))
        $areaCheck = $par[lokasi];

    $text = "<div class=\"pageheader\">
            <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
            " . getBread() . "
        </div>

    <div id=\"contentwrapper\" class=\"contentwrapper\">
    
        <div style=\"position: absolute; top: 10px; right: 20px;\">
            &nbsp;Periode : " . comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"", "", "All") . "
        </div>
        
        <br>
    
        <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    
            <input type=\"hidden\" name=\"_submit\" value=\"t\">
    
            <div id=\"pos_l\" style=\"float:left;\">
    
                <table>
                    <tr>
                        <td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\"/></td>
                        <td>" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' order by namaData", "kodeData", "namaData", "par[idKategori]", "All", $par[idKategori], "", "200px", "chosen-select") . "</td>
                        <td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
                    </tr>
    
                </table>
            </div>
            <div id=\"right\" style=\"float:right; margin-bottom:5px;\">";

    if (isset($menuAccess[$s]['add'])) {
        $text .= '<a class="btn btn1 btn_inboxi" id="import"><span>Import</span></a>&emsp;';
    }

    $text .= "
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
            </div>
    
        </form>
    
        <br clear=\"all\" />
    
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\" style=\"margin-top: 5px;\">
    
            <thead>
                <tr>
                    <th width=\"20\">No.</th>					
                    <th>Pelatihan</th>					
                    <th style=\"width:75px;\">Mulai</th>
                    <th style=\"width:75px;\">Selesai</th>
                    <th>Lokasi</th>
                    <th>Vendor</th>
                    <th style=\"width:50px;\">Peserta</th>
                    <th style=\"width:125px;\">Surat Tugas</th>
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
                or lower(t2.namaVendor) like '%" . strtolower($par[filter]) . "%'
                )";

    if (!empty($par[lokasi]))
        $filter .= " AND t1.idLokasi IN ($areaCheck)";

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from plt_pelatihan t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";

    $res = db($sql);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $cntPeserta = getField("select count(idPeserta) from plt_pelatihan_peserta where idPelatihan='$r[idPelatihan]'");

        $statusPeserta = $cntPeserta ? getAngka($cntPeserta) : "<img src=\"styles/images/f.png\" title=\"Belum Ada\">";

        $text .= "<tr>
                    <td>$no.</td>
                    <td><a href='#' onclick=\"openBox('popup.php?par[mode]=detail&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode, idPelatihan") . "',940,550)\">$r[judulPelatihan]</a></td>
                    <td align=\"center\">" . getTanggal($r[mulaiPelatihan]) . "</td>
                    <td align=\"center\">" . getTanggal($r[selesaiPelatihan]) . "</td>
                    <td>$r[lokasiPelatihan]</td>
                    <td>$r[namaVendor]</td>
                    <td align=\"center\"><a href=\"?par[mode]=add&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "\">$statusPeserta</a></td>
                    <td align=\"center\"><a href=\"?par[mode]=xls&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "\"><img src=\"styles/images/xls.png\" style=\"width:20px;\" title=\"Surat Tugas\"></a></td>
                </tr>";

    }

    $text .= "</tbody>
        </table>
    
    </div>";

    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=" . strtolower(str_replace("--", "-", preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ", "-", str_replace("-", "_", strtolower(getField("select judulPelatihan from plt_pelatihan where idPelatihan='" . $par[idPelatihan] . "'"))))))) . ".xls\" frameborder=\"1\" width=\"100%\" height=\"0\"></iframe>";
    }

    return $text;
}

function detail()
{

    global $s, $inp, $par, $arrTitle;

    $res = db("select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
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
    </div>";

    return $text;
}

function dataa()
{
    global $id;

    $sql = "select * from emp_phist where parent_id='" . $id . "' and status='1'";

    $res = db($sql);

    $r = mysql_fetch_array($res);


    $tanggalLahir = getField("select birth_date from emp where id='" . $id . "'");

    if (getTanggal($tanggalLahir)) $umur = getAngka(selisihTahun($tanggalLahir, date("Y-m-d")));

    return $r[div_id] . " \t" . getField("select namaData from mst_data where kodeData='$r[dept_id]'") . " \t" . $umur;
}

function hapus()
{
    global $par;

    $sql = "delete from plt_pelatihan_peserta where idPelatihan='$par[idPelatihan]' and idPeserta='$par[idPeserta]'";

    db($sql);

    echo "<script>window.location='?par[mode]=add" . getPar($par, "mode,idPeserta") . "';</script>";

}


function ubah()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "update plt_pelatihan_peserta set idPegawai='$inp[idPegawai]', jabatanPeserta='$inp[jabatanPeserta]', posisiPeserta='$inp[posisiPeserta]', umurPeserta='" . setAngka($inp[umurPeserta]) . "', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPelatihan='$par[idPelatihan]' and idPeserta='$par[idPeserta]'";

    db($sql);

    echo "<script>window.parent.location='index.php?par[mode]=add" . getPar($par, "mode,idPeserta") . "';</script>";
}


function tambah()
{
    global $inp, $par, $cUsername;

    repField();

    $idPeserta = getField("select idPeserta from plt_pelatihan_peserta where idPelatihan='" . $par[idPelatihan] . "' order by idPeserta desc limit 1") + 1;

    $sql = "insert into plt_pelatihan_peserta (idPelatihan, idPeserta, idPegawai, jabatanPeserta, posisiPeserta, umurPeserta, createBy, createTime) values ('$par[idPelatihan]', '$idPeserta', '$inp[idPegawai]', '$inp[jabatanPeserta]', '$inp[posisiPeserta]', '" . setAngka($inp[umurPeserta]) . "', '$cUsername', '" . date('Y-m-d H:i:s') . "')";

    db($sql);

    echo "<script>window.parent.location='index.php?par[mode]=add" . getPar($par, "mode,idPeserta") . "';</script>";

}


function form()
{
    global $s, $par, $arrTitle, $arrParameter, $menuAccess;

    $sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";

    $res = db($sql);

    $r = mysql_fetch_array($res);

    $pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";

    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[6] . "' order by urutanData limit 1");


    setValidation("is_null", "inp[idPegawai]", "anda harus mengisi nama");

    $text = getValidation();


    $text .= "<div class=\"pageheader\">
        <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        " . getBread(ucwords($par[mode] . " data")) . "
    </div>

    <div class=\"contentwrapper\">
    
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\"  >	
    
            <div style=\"top:10px; right:35px; position:absolute\">
    
                <input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, idPelatihan") . "';\"/>
    
            </div>
    
            <div id=\"general\" style=\"margin-top:20px;\">					
    
                " . dtaPelatihan("RENCANA PELATIHAN") . "					
    
                <fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
    
                    <legend style=\"padding:10px; margin-left:20px;\"><h4>PESERTA</h4></legend>
                    
                    <div style=\"float: right; margin-bottom: 10px; margin-top: -16px;\">";

    if (isset($menuAccess[$s]['add'])) {
        $text .= '<a class="btn btn1 btn_inboxi" id="import"><span>Import</span></a>&emsp;';
    }

    $text .= "
                        <a href=\"#\" id=\"btnAddPeserta\" class=\"btn btn1 btn_document\" title=\"Tambah Peserta\"><span>Tambah Peserta</span></a>
                    </div>
                    
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
    
                        <thead>
                            <tr>
                                <th width=\"20\">No.</th>
                                <th>Nama</th>
                                <th width=\"200\">Unit Kerja</th>
                                <th width=\"200\">Jabatan</th>
                                <th width=\"75\">Umur</th>";

    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
        $text .= "<th width=\"50\">Kontrol</th>";

    $text .= "    </tr>
                        </thead>
                        <tbody>";

    $res = db("SELECT * FROM `plt_pelatihan_peserta` t1 JOIN `emp` t2 ON t1.`idPegawai` = t2.`id` WHERE t1.`idPelatihan` = '$par[idPelatihan]' ORDER BY t2.`name`");
    $no = 1;

    while ($r = mysql_fetch_array($res)) {

        $jabatanid = getField("SELECT pos_name from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");
        $dirid = getField("SELECT div_id from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");
        $posid = getField("SELECT posisi_kerja from sdm_posisi where id_pegawai = '$r[id]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[id]')");

        if ($r['idPeserta'] == $par['idPeserta']) {

            $text .= "<tr>

                                <td>$no.</td>

                                <td>" . comboData("select id, upper(name) as name from emp where status='$status' and id not in ('" . implode("','", $arrID) . "') order by name", "id", "name", "inp[idPegawai]", " ", $r[idPegawai], "onchange=\"getField('" . getPar($par, "mode") . "');\"", "100%", "chosen-select") . "</td>

                                <td><input type=\"text\" id=\"inp[posisiPeserta]\" name=\"inp[posisiPeserta]\" value=\"$r[posisiPeserta]\" class=\"mediuminput\" style=\"width:90%;\" maxlength=\"150\"/></td>
                                <td><input type=\"text\" id=\"inp[jabatanPeserta]\" name=\"inp[jabatanPeserta]\" value=\"$r[jabatanPeserta]\" class=\"mediuminput\" style=\"width:90%;\" maxlength=\"150\"/></td>

                                <td><input type=\"text\" id=\"inp[umurPeserta]\" name=\"inp[umurPeserta]\" value=\"$r[umurPeserta]\" class=\"mediuminput\" style=\"width:80%; text-align:right\" onkeyup=\"cekAngka(this)\" maxlength=\"150\"/></td>

                                <td><input type=\"submit\" class=\"add\" name=\"simpan\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>

                            </tr>";

        } else {

            // $text.="<tr>

            // <td>$no.</td>

            // <td>".strtoupper($r[name])."</td>

            // <td>".getField("SELECT namaData from mst_data where kodeData ='$jabatanid'")."</td>

            // <td>".getField("SELECT namaData from mst_data where kodeData ='$posid'")."</td>

            // <td align=\"right\">".getAngka($r[umurPeserta])." Tahun</td>";

            // if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){

            // 	$text.="<td align=\"center\">";

            // 	if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idPeserta]=$r[idPeserta]".getPar($par,"mode,idPeserta")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";


            // 	if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPeserta]=$r[idPeserta]".getPar($par,"mode,idPeserta")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

            // 	$text.="</td>";

            // }

            // $text.="</tr>";

            $text .= "
                                        <tr>
                                            <td>$no.</td>
                                            <td>" . strtoupper($r[name]) . "</td>
                                            <td>$r[posisiPeserta]</td>
                                            <td>$r[jabatanPeserta]</td>
                                            <td align=\"right\">" . getAngka($r[umurPeserta]) . " Tahun</td>";
            if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
                $text .= "<td align=\"center\">";
                if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"?par[mode]=edit&par[idPeserta]=$r[idPeserta]" . getPar($par, "mode,idPeserta") . "\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
                if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=del&par[idPeserta]=$r[idPeserta]" . getPar($par, "mode,idPeserta") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                $text .= "</td>";
            }
            $text .= "</tr>";

        }

        $arrID[] = $r[idPegawai];

        $no++;

    }

    // if(isset($menuAccess[$s]["add"]) && $par[mode] == "add") {

    // 	$text.="<tr>

    // 		<td>$no.</td>

    // 		<td>".comboData("select id, upper(name) as name from emp where id not in ('".implode("','", $arrID)."') order by name","id","name","inp[idPegawai]"," ",$r[idPegawai],"onchange=\"getField('".getPar($par, "mode")."');\"", "100%","chosen-select")."</td>

    // 		<td><input type=\"text\" id=\"inp[jabatanPeserta]\" name=\"inp[jabatanPeserta]\" value=\"$r[jabatanPeserta]\" class=\"mediuminput\" style=\"width:90%;\" maxlength=\"150\"/></td>

    // 		<td><input type=\"text\" id=\"inp[posisiPeserta]\" name=\"inp[posisiPeserta]\" value=\"$r[posisiPeserta]\" class=\"mediuminput\" style=\"width:90%;\" maxlength=\"150\"/></td>

    // 		<td><input type=\"text\" id=\"inp[umurPeserta]\" name=\"inp[umurPeserta]\" value=\"$r[umurPeserta]\" class=\"mediuminput\" style=\"width:80%; text-align:right\" onkeyup=\"cekAngka(this)\" maxlength=\"150\"/></td>

    // 		<td><input type=\"submit\" class=\"add\" name=\"simpan\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>

    // 	</tr>";

    // }


    $text .= "</tbody>
				</table>
			</fieldset>

		</div>

	</form>
	<script>

		jQuery(document).ready(function(){

			jQuery(\"#btnAddPeserta\").click(function(e){
				openBox(\"popup.php?par[mode]=addPeserta&par[existing]=" . implode(",", $arrID) . getPar($par, "mode,tahunPelatihan") . "\", 900, 600);
			});
			
		});
			
        jQuery(\"#import\").click(function () {
                openBox(`popup.php?par[mode]=import" . getPar($par, "mode") . "`, 700, 250)
        })

	</script>";

    return $text;
}

function xls()
{

    global $s, $par, $arrTitle, $cNama, $fFile;

    require_once 'plugins/PHPExcel.php';

    $sql = "select * from plt_pelatihan where idPelatihan='" . $par[idPelatihan] . "'";

    $res = db($sql);

    $r = mysql_fetch_array($res);

    $mulaiJadwal = getField("select concat(substring(mulaiJadwal,1,5), ' - ', substring(selesaiJadwal,1,5)) from plt_pelatihan_jadwal where idPelatihan='" . $par[idPelatihan] . "' order by idJadwal limit 1");


    $sql_ = "select * from (

	select t2.id, t2.name, t2.pos_name, t2.dept_id from plt_pelatihan_peserta t1 join dta_pegawai t2 join mst_data t3 on (t1.idPegawai=t2.id) where t1.idPelatihan='" . $par[idPelatihan] . "'

	) as d1 left join mst_data d2 on (d1.dept_id=d2.kodeData)";

    $res_ = db($sql_);

    while ($r_ = mysql_fetch_array($res_)) {

        if (empty($r_['namaData']))
            $r_['namaData'] = "UNDEFINED";

        $arrDepartemen[$r_['namaData']] = $r_['namaData'];

        $arrPegawai[$r_['namaData']][$r_['id']] = strtoupper($r_['name']) . "\t" . strtoupper($r_['pos_name']);

    }

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator($cNama)
        ->setLastModifiedBy($cNama)
        ->setTitle($arrTitle[$s]);

    $idx = 0;

    if (is_array($arrDepartemen)) {

        reset($arrDepartemen);

        while (list($namaDepartemen) = each($arrDepartemen)) {

            if ($idx > 0) $objPHPExcel->createSheet($idx);

            $objPHPExcel->setActiveSheetIndex($idx);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(2);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(45);

            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(50);

            $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setSize(24);

            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
            $objPHPExcel->getActiveSheet()->mergeCells('A3:E3');

            $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C4:C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
            $objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
            $objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
            $objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
            $objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
            $objPHPExcel->getActiveSheet()->mergeCells('D5:E5');
            $objPHPExcel->getActiveSheet()->mergeCells('D6:E6');
            $objPHPExcel->getActiveSheet()->mergeCells('D7:E7');

            $objPHPExcel->getActiveSheet()->setCellValue('A2', "SURAT TUGAS");
            $objPHPExcel->getActiveSheet()->setCellValue('A4', "Tanggal");
            $objPHPExcel->getActiveSheet()->setCellValue('A5', "Tembusan");
            $objPHPExcel->getActiveSheet()->setCellValue('A7', "Kepada");
            $objPHPExcel->getActiveSheet()->setCellValue('C4', ":");
            $objPHPExcel->getActiveSheet()->setCellValue('C5', ":");
            $objPHPExcel->getActiveSheet()->setCellValue('C7', ":");
            $objPHPExcel->getActiveSheet()->setCellValue('D4', getTanggal($r[mulaiPelatihan], "t"));
            $objPHPExcel->getActiveSheet()->setCellValue('D5', "1. Yth. General Manager");
            $objPHPExcel->getActiveSheet()->setCellValue('D6', "2. File");


            $objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9:E9')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $objPHPExcel->getActiveSheet()->mergeCells('B9:D9');

            $objPHPExcel->getActiveSheet()->setCellValue('A9', 'NO.');
            $objPHPExcel->getActiveSheet()->setCellValue('B9', 'NAMA');
            $objPHPExcel->getActiveSheet()->setCellValue('E9', 'JABATAN');


            $rows = 10;

            $no = 1;

            if (is_array($arrPegawai[$namaDepartemen])) {

                reset($arrPegawai[$namaDepartemen]);

                while (list($idPegawai, $valPegawai) = each($arrPegawai[$namaDepartemen])) {

                    list($namaPegawai, $jabatanPegawai) = explode("\t", $valPegawai);

                    $objPHPExcel->getActiveSheet()->getStyle('A' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->mergeCells('B' . $rows . ':D' . $rows);

                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, $no);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $rows, $namaPegawai);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $rows, $jabatanPegawai);


                    $rows++;

                    $no++;

                }

            }

            $objPHPExcel->getActiveSheet()->mergeCells('B' . $rows . ':D' . $rows);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('B' . $rows . ':D' . $rows);

            $rows++;
            $rows--;

            $objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9:A' . $rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A9:A' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('D9:D' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('E9:E' . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $rows++;
            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':B' . $rows);
            $objPHPExcel->getActiveSheet()->mergeCells('D' . $rows . ':E' . $rows);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "Nama Pelatihan");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rows, ":");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, $r[judulPelatihan]);

            $objPHPExcel->getActiveSheet()->getStyle('C' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':B' . $rows);
            $objPHPExcel->getActiveSheet()->mergeCells('D' . $rows . ':E' . $rows);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "Tanggal");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rows, ":");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, getTanggal($r[mulaiPelatihan], "t"));

            $objPHPExcel->getActiveSheet()->getStyle('C' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':B' . $rows);
            $objPHPExcel->getActiveSheet()->mergeCells('D' . $rows . ':E' . $rows);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "Waktu");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rows, ":");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, $mulaiJadwal);

            $objPHPExcel->getActiveSheet()->getStyle('C' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':B' . $rows);
            $objPHPExcel->getActiveSheet()->mergeCells('D' . $rows . ':E' . $rows);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "Tempat");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rows, ":");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, $r[lokasiPelatihan]);

            $objPHPExcel->getActiveSheet()->getStyle('C' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $rows++;
            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "Head Departement");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, "Mengetahui");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rows, "Menyetujui");
            $objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);

            $rows++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rows . ':C' . $rows);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rows, "(.....................................)");
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rows, "(.....................................)");
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rows, "(.....................................)");

            $objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A1:E' . $rows)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getStyle('A1:E' . $rows)->getFont()->setName('Times');
            $objPHPExcel->getActiveSheet()->getStyle('A4:E' . $rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


            $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);

            $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

            $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
            $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
            $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
            $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);


            $objPHPExcel->getActiveSheet()->setTitle($namaDepartemen);

            $idx++;

        }

    }

    $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fFile . strtolower(str_replace("--", "-", preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(" ", "-", str_replace("-", "_", strtolower($r[judulPelatihan])))))) . ".xls");
}

function insertByImport()
{

    global $par, $inp, $file_log, $cID;

    $log = fopen($file_log, "a+");

    $data_size = $inp['size'];
    $data_position = $inp['position'];
    $data = json_decode($inp['data']);

    // FINISH HIM :v
    if ($data_position == $data_size) {

        fwrite($log, "\nFINISH \t\t: " . date("d/m/Y H:i:s") . "\n");
        fclose($log);

        return;
    }

    $number = $data[0];
    $nik = $data[1];
    $name = $data[2];

    $participant_id = getField("SELECT `idPeserta` FROM `plt_pelatihan_peserta` WHERE `idPelatihan` = '$par[idPelatihan]' ORDER BY `idPeserta` DESC LIMIT 1") + 1;
    $emp_id = getField("SELECT `id` FROM `emp` WHERE `reg_no` = '$nik'");
    $emp_age = getField("SELECT YEAR(`birth_date`) FROM `emp` WHERE `reg_no` = '$nik'");

    $location = getField("SELECT `namaData` FROM `mst_data` t1 JOIN `emp_phist` t2 ON t1.`kodeData` = t2.`dir_id` WHERE t2.`parent_id` = '$emp_id' AND t2.`status` = '1'");
    $position = getField("SELECT `pos_name` FROM `emp_phist` WHERE `parent_id` = '$emp_id' AND `status` = '1'");

    $sql = "INSERT INTO `plt_pelatihan_peserta` SET
        `idPelatihan` = '$par[idPelatihan]',
        `idPeserta` = '$participant_id',
        `idPegawai` = '$emp_id',
        `posisiPeserta` = '$location',
        `jabatanPeserta` = '$position',
        `umurPeserta` = '" . (date('Y') - $emp_age) . "',
        `createTime` = '" . date('Y-m-d H:i:s') . "',
        `createBy` = '$cID'
    ";

    if (db($sql)) {

        fwrite($log, "INSERT \t\t: " . date("d/m/Y H:i:s") . " \tNo: $nik\n");
        fclose($log);

        return;
    }

    fwrite($log, "FAILED \t\t: " . date("d/m/Y H:i:s") . " \tNo: $number\n");
    fwrite($log, json_encode($par));
    fclose($log);
}
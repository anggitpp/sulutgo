<?php
if (!isset($menuAccess[$s]['view'])) echo '<script>logout();</script>';

$fFile = 'files/rencana/';
$folder_upload = 'files/rencana/dokumen/';

$path_import = "files/imports/";
$file_log = "files/logs/data_vendor.log";
$file_template = "files/templates/template-vendor.xlsx";

function getContent($par)
{

    global $s, $_submit, $menuAccess;

    switch ($par['mode']) {

        case "delDokumen":
            $text = delDokumen();
            break;

        case "viewFile":
            $text = viewFile();
            break;

        case "saveDokumen":
            $text = saveDokumen();
            break;

        case "addDokumen":
            $text = addDokumen();
            break;

        case "fp":
            $text = fp();
            break;

        case "detail":
            $text = detail();
            break;

        case 'delFile':
            if (isset($menuAccess[$s]['edit'])) {
                $text = hapusFile();
            } else {
                $text = lihat();
            }
            break;

        case 'del':
            if (isset($menuAccess[$s]['delete'])) {
                $text = hapus();
            } else {
                $text = lihat();
            }
            break;

        case "id":
            $text = id();
            break;

        case "id2":
            $text = id2();
            break;

        case 'edit':
            if (isset($menuAccess[$s]['edit'])) {
                $text = empty($_submit) ? form() : ubah();
            } else {
                $text = lihat();
            }
            break;

        case 'add':
            if (isset($menuAccess[$s]['add'])) {
                $text = empty($_submit) ? form() : tambah();
            } else {
                $text = lihat();
            }
            break;

        case 'app':
            if (isset($menuAccess[$s]['apprlv1'])) {
                $text = empty($_submit) ? approval() : update();
            } else {
                $text = approval();
            }
            break;

        case "team":
            $text = empty($_submit) ? detail_team() : add_team();
            break;

        case "tambahTeam":
            $text = empty($_submit) ? form_team() : add_team();
            break;

        case "editTeam":
            $text = empty($_submit) ? form_team() : ubah_team();
            break;

        case "delTeam":
            $text = hapus_team();
            break;

        case 'trainers':
            $text = trainers();
            break;

        case "import":
            formImport("Import Rencana");
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

    global $s, $par, $_submit, $arrTitle, $arrParameter, $menuAccess, $areaCheck;

    if (empty($_submit) && empty($par[tahunPelatihan])) {
        $par[tahunPelatihan] = date('Y');
    }

    $text .= "<div class=\"pageheader\">
  <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
  " . getBread() . "
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
  <div style=\"position: absolute; top: 10px; right: 20px;\">
         &nbsp;Periode : " . comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"", "", "All") . "&nbsp;
  </div>
  <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    <input type=\"hidden\" name=\"_submit\" value=\"t\">
    <div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">
    </div>
    <div id=\"pos_l\" style=\"float:left;\">
      <table>
        <tr>
          <td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder='Search'/></td>
          <td>" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' order by namaData", "kodeData", "namaData", "par[idKategori]", "All", $par[idKategori], "", "200px", "chosen-select") . "</td>
          <td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
        </tr>
      </table>
    </div>
    <div id=\"pos_r\">
     ";

    if (isset($menuAccess[$s]['add'])) {
        $text .= '<a class="btn btn1 btn_inboxi" id="import"><span>Import</span></a>&emsp;';
    }

    if (isset($menuAccess[$s]['add'])) {
        $text .= '<a href="?par[mode]=add' . getPar($par, 'mode,idPelatihan') . '" class="btn btn1 btn_document"><span>Tambah Data</span></a>';
    }

    $text .= '</div>
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
     <th style="width:75px;">Team</th>
     <th style="width:50px;">File</th>
     <th style="width:50px;">Approval</th>';
    if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
        $text .= '<th width="50">Kontrol</th>';
    }
    $text .= '</tr>
  </thead>
  <tbody>';

    $filter = 'where t1.idPelatihan is not null';
    if (!empty($par[tahunPelatihan])) {
        $filter .= ' AND ' . $par[tahunPelatihan] . ' between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)';
    }

    if (!empty($par[idKategori])) {
        $filter .= " and t1.idKategori='" . $par[idKategori] . "'";
    }

    if (!empty($par[filter])) {
        $filter .= " and (
      lower(t1.judulPelatihan) like '%" . strtolower($par[filter]) . "%'
      or lower(t1.lokasiPelatihan) like '%" . strtolower($par[filter]) . "%'
      or lower(t2.namaVendor) like '%" . strtolower($par[filter]) . "%'
      )";
    }

    if (!empty($par[lokasi])) {
        $filter .= " AND t1.idLokasi IN ($areaCheck)";
    }

    $sql = "select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from plt_pelatihan t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        ++$no;

        $statusPelatihan = "<img src=\"styles/images/p.png\" title='Belum Diproses'>";
        if ($r[statusPelatihan] == 't') {
            $statusPelatihan = "<img src=\"styles/images/t.png\" title='Setuju'>";
        }
        if ($r[statusPelatihan] == 'f') {
            $statusPelatihan = "<img src=\"styles/images/f.png\" title='Tolak'>";
        }
        if ($r[statusPelatihan] == 'p') {
            $statusPelatihan = "<img src=\"styles/images/o.png\" title='Tunda'>";
        }
        $jumlahTeam = getField("SELECT count(*) FROM plt_team WHERE id_pelatihan_team = '$r[idPelatihan]' ");

        $text .= "<tr>
      <td>$no.</td>
      <td><a href='#' onclick=\"openBox('popup.php?par[mode]=detail&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode, idPelatihan") . "',940,550)\">$r[judulPelatihan]</a></td>
      <td align=\"center\">" . getTanggal($r[mulaiPelatihan]) . '</td>
      <td align="center">' . getTanggal($r[selesaiPelatihan]) . "</td>
      <td>$r[lokasiPelatihan]</td>
      <td>$r[namaVendor]</td>
      <td align=\"center\"><a href=\"index.php?par[mode]=team&par[pelatihan]=$r[idPelatihan]" . getPar($par, "mode,idPelatihan") . "\">" . $jumlahTeam . "</a></td>
      <td align=\"center\">" . (!empty($r[filePelatihan]) ? "<img src=\"" . getIcon($r[filePelatihan]) . "\" style='width:20px;' onclick=\"openBox('view.php?doc=rencana_pelatihan&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode, idPelatihan") . "',900,525);\">" : " - ") . "</td>
      <td align=\"center\">";
        $text .= (empty($r[statusPelatihan]) && !isset($menuAccess[$s]['apprlv1'])) ? "$statusPelatihan" : "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=app&par[idPelatihan]=$r[idPelatihan]" . getPar($par, 'mode,idPelatihan') . "',800,375);\" title=\"Approval\">$statusPelatihan</a></td>";
        if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {
            $text .= '<td align="center">';
            if (isset($menuAccess[$s]['edit'])) {
                $text .= "<a href=\"?par[mode]=edit&par[idPelatihan]=$r[idPelatihan]" . getPar($par, 'mode,idPelatihan') . '" title="Edit Data" class="edit"><span>Edit</span></a>';
            }

            if (isset($menuAccess[$s]['delete']) && $r[statusPelatihan] != 't') {
                $text .= "<a href=\"?par[mode]=del&par[idPelatihan]=$r[idPelatihan]" . getPar($par, 'mode,idPelatihan') . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            }
            $text .= '</td>';
        }
        $text .= '</tr>';
    }

    $text .= '</tbody>
    </table>
  </div>';

    $text .= "<script>
        
        jQuery(\"#import\").click(function () {
            openBox(`popup.php?par[mode]=import" . getPar($par, "mode") . "`, 700, 250)
        })
        
    </script>";

    return $text;
}

function delDokumen()
{

    global $par, $folder_upload;

    $file = getField("SELECT file FROM perencanaan_dokumen WHERE id_pdokumen ='$par[id_pdokumen]'");

    if (file_exists($folder_upload . $file) and $file != "")
        unlink($folder_upload . $file);

    $sql = "DELETE FROM perencanaan_dokumen WHERE id_pdokumen = '$par[id_pdokumen]'";
    db($sql);

    echo "<script>window.location.href='index.php?par[mode]=edit" . getPar($par, "mode,id_pdokumen") . "';</script>";
}

function viewFile()
{
    global $s, $inp, $par, $cUsername, $arrParam, $folder_upload;
}

function saveDokumen()
{
    global $s, $inp, $par, $cUsername, $arrParam, $folder_upload;

    repField();

    $lastID = getField("SELECT id_pdokumen FROM perencanaan_dokumen ORDER BY id_pdokumen DESC LIMIT 1") + 1;

    if (empty($par[id_pdokumen])) {
        $file = uploadFiles("$lastID", "file", "$folder_upload", "PRD");

        $sql = "INSERT INTO perencanaan_dokumen (id_pdokumen, id_pelatihan, file, keterangan, created_date, created_by) VALUES ('$lastID','$par[idPelatihan]','$file','$inp[keterangan]',now(),'$cUsername')";

        /*var_dump($sql);
        die();*/
    }

    db($sql);

    echo "<script>alert('Data berhasil disimpan');closeBox();</script>";
    echo "<script>reloadPage();</script>";
}

function addDokumen()
{

    global $par;

    $sql = db("SELECT * FROM perencanaan_dokumen WHERE id_pdokumen = '$par[id_pdokumen]'");
    $r = mysql_fetch_array($sql);

    $text = "
    <style>
        #inp_kodeRekening__chosen{
            min-width:250px;
        }
    </style>
    <div class=\"centercontent contentpopup\">
      <div class=\"pageheader\">
        <h1 class=\"pagetitle\">FILE</h1>
        <span class=\"pagedesc\">&nbsp;</span> 
      </div>
      <div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?par[mode]=saveDokumen" . getPar($par, "mode") . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
          <div style=\"position:absolute; right:20px; top:14px;\">
            <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
            <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Tutup\" onclick=\"closeBox();\"/>
          </div>
          <fieldset>
            <p>
              <label class=\"l-input-small\">File</label>
              <div class=\"field\">";

    if (empty($r['file'])) {
        $text .= "<input type=\"file\" id=\"file\" name=\"file\" class=\"mediuminput\" style=\"width: 300px; margin-top: 5px;\">";
    } else {
        $text .= "
                          <img src=\"" . getIcon($r[file]) . "\" title=\"Download\" style=\"margin-top: 5px; width:20px;\">
                          <a href=\"?par[mode]=delete_file" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" >Delete</a>";
    }

    $text .= "
              </div>
            </p>
            <p>
              <label class=\"l-input-small\">Keterangan</label>
              <div class=\"field\">
                <textarea rows=\"5\" style=\"width:300px;\" class=\"mediuminput\" name=\"inp[keterangan]\">$r[keterangan]</textarea>
              </div>
            </p>
          </fieldset>
        </form>
      </div>
    </div>";

    return $text;
}

function fp()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrParam;
}

function detail()
{

    global $s, $inp, $par, $arrTitle;

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

    $text = "
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

function trainers()
{

    global $par;

    $res = arrayQuery("SELECT `idTrainer`, UPPER(`namaTrainer`) AS `namaTrainer` FROM `dta_trainer` WHERE `idVendor` = '$par[vendor_id]' ORDER BY `namaTrainer`");

    echo json_encode($res);
}

function upload($idPelatihan)
{
    global $db, $s, $inp, $par, $fFile;
    $filePelatihan = getField("SELECT `filePelatihan` from plt_pelatihan where idPelatihan='$par[idPelatihan]'");

    $fileUpload = $_FILES['filePelatihan']['tmp_name'];
    $fileUpload_name = $_FILES['filePelatihan']['name'];
    if (($fileUpload != '') and ($fileUpload != 'none')) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $filePelatihan = 'rencana-' . $idPelatihan . '.' . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $filePelatihan);
    } else {
        if (!empty($inp[filePelatihan])) {
            $filePelatihan = 'rencana-' . $idPelatihan . '.' . getExtension($inp[filePelatihan]);
        }
    }

    return $filePelatihan;
}

function hapusFile()
{
    global $db, $s, $inp, $par, $fFile, $cUsername;
    $filePelatihan = getField("SELECT `filePelatihan` from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
    if (file_exists($fFile . $filePelatihan) and $filePelatihan != '') {
        unlink($fFile . $filePelatihan);
    }

    $sql = "update plt_pelatihan set filePelatihan='' where idPelatihan='$par[idPelatihan]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit" . getPar($par, 'mode') . "';</script>";
}

function hapus()
{
    global $db, $s, $inp, $par, $fFile, $cUsername;
    $filePelatihan = getField("SELECT `filePelatihan` from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
    if (file_exists($fFile . $filePelatihan) and $filePelatihan != '') {
        unlink($fFile . $filePelatihan);
    }

    $sql = "delete from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
    db($sql);
    $sql = "delete from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]'";
    db($sql);
    $sql = "delete from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'";
    db($sql);
    $sql = "delete from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]'";
    db($sql);
    $sql = "delete from plt_pelatihan_peserta where idPelatihan='$par[idPelatihan]'";
    db($sql);
    $sql = "delete from ctg_program_biaya_detail where id_pelatihan = ''$par[idPelatihan]''";
    db($sql);
    echo "<script>window.location='?" . getPar($par, 'mode,idPelatihan') . "';</script>";
}

function update()
{
    global $db, $s, $inp, $par, $detail, $cUsername;
    repField();

    $sql = "update plt_pelatihan set keteranganPelatihan='$inp[keteranganPelatihan]', statusPelatihan='$inp[statusPelatihan]', approveBy='$cUsername', approveTime='" . date('Y-m-d H:i:s') . "' where idPelatihan='$par[idPelatihan]'";
    db($sql);

    echo "<script>window.parent.location='index.php?" . getPar($par, 'mode,idPelatihan') . "';</script>";
}

function ubah()
{
    global $db, $s, $inp, $par, $detail, $cUsername, $folder_upload, $fFile;
    repField();
    $filePelatihan = uploadFiles("$par[idPelatihan]", "filePelatihan", "$fFile", "FP");

    $sql = "update plt_pelatihan set idPegawai='$inp[idPegawai]', idVendor='$inp[idVendor]', mulaiPelatihan='" . setTanggal($inp[mulaiPelatihan]) . "',selesaiPelatihan='" . setTanggal($inp[selesaiPelatihan]) . "', idTrainer='$inp[idTrainer]', idKategori='$inp[idKategori]', idTraining='$inp[idTraining]', modul_pelatihan='$inp[modul_pelatihan]', kategori_level_pelatihan='$inp[kategori_level_pelatihan]', program_pelatihan='$inp[program_pelatihan]',idDepartemen='$inp[idDepartemen]', kodePelatihan='$inp[kodePelatihan]', judulPelatihan='$inp[judulPelatihan]', subPelatihan='$inp[subPelatihan]', pesertaPelatihan='" . setAngka($inp[pesertaPelatihan]) . "', pelaksanaanPelatihan='$inp[pelaksanaanPelatihan]', lokasiPelatihan='$inp[lokasiPelatihan]', biayaPelatihan='" . setAngka($inp[biayaPelatihan]) . "', filePelatihan='$filePelatihan', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idPelatihan='$par[idPelatihan]'";

    db($sql);

    foreach ($inp['batas'] as $key => $value) {
        $total += setAngka($value['biaya_semua']);

        $cekidmaster = getField("SELECT id_master FROM ctg_program_biaya_detail where id_pelatihan = '$par[idPelatihan]' and id_master = '$key'");
        if (empty($cekidmaster)) {
            $sql_biaya = "INSERT ctg_program_biaya_detail SET id_pelatihan = '$par[idPelatihan]' , id_program='$inp[program_pelatihan]', id_master='$key', biaya_perkategori='" . setAngka($value['biaya_semua']) . "'";

        } else {
            $sql_biaya = "UPDATE ctg_program_biaya_detail SET  id_program='$inp[program_pelatihan]', biaya_perkategori='" . setAngka($value['biaya_semua']) . "' where id_pelatihan = '$par[idPelatihan]' and id_master = '$cekidmaster' ";

        }
        db($sql_biaya);

    }

    db("delete from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]'");
    db("delete from plt_pelatihan_jadwal where idPelatihan='$par[idPelatihan]'");
    if (is_array($detail)) {
        ksort($detail);
        reset($detail);
        while (list($idDetail, $valDetail) = each($detail)) {
            list($keteranganDetail, $tanggalMulai, $waktuMulai, $tanggalSelesai, $waktuSelesai) = explode("\t", $valDetail);
            $mulaiDetail = setTanggal($tanggalMulai) . ' ' . $waktuMulai . ':00';
            $selesaiDetail = setTanggal($tanggalSelesai) . ' ' . $waktuSelesai . ':00';
            $sql = "insert into plt_pelatihan_detail (idPelatihan, idDetail, keteranganDetail, mulaiDetail, selesaiDetail, createBy, createTime) values ('$par[idPelatihan]', '$idDetail', '$keteranganDetail', '$mulaiDetail', '$selesaiDetail', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
            db($sql);

            $idJadwal = getField("select idJadwal from plt_pelatihan_jadwal where idPelatihan='" . $par[idPelatihan] . "' order by idJadwal desc limit 1") + 1;

            $sql = "insert into plt_pelatihan_jadwal (idPelatihan, idJadwal, idPegawai, judulJadwal, tanggalJadwal, mulaiJadwal, selesaiJadwal, keteranganJadwal, createBy, createTime) values ('$par[idPelatihan]', '$idJadwal', '$inp[idPegawai]', '$keteranganDetail', '" . setTanggal($tanggalMulai) . "', '$waktuMulai', '$waktuSelesai', '', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
            db($sql);
        }
    }

    // list($mulaiPelatihan, $selesaiPelatihan) = explode("\t", getField("select concat(min(date(mulaiDetail)), '\t', max(date(selesaiDetail))) from plt_pelatihan_detail where idPelatihan='".$par[idPelatihan]."'"));
    // $sql = "update plt_pelatihan set mulaiPelatihan='".$mulaiPelatihan."', selesaiPelatihan='".$selesaiPelatihan."' where idPelatihan='".$par[idPelatihan]."'";
    // db($sql);

    echo "<script>window.location='?" . getPar($par, 'mode,idPelatihan') . "';</script>";
}

function tambah()
{
    global $db, $s, $inp, $par, $detail, $cUsername, $fFile;

    repField();

    $idPelatihan = getField('select idPelatihan from plt_pelatihan order by idPelatihan desc limit 1') + 1;
    $filePelatihan = uploadFiles("$idPelatihan", "filePelatihan", "$fFile", "FP");

    $sql = "insert into plt_pelatihan (idPelatihan, idPegawai, idVendor, idTrainer, idKategori, idTraining, modul_pelatihan , kategori_level_pelatihan, program_pelatihan,idDepartemen, mulaiPelatihan,selesaiPelatihan,kodePelatihan, judulPelatihan, subPelatihan, pesertaPelatihan, pelaksanaanPelatihan, lokasiPelatihan, biayaPelatihan, filePelatihan, createBy, createTime) values ('$idPelatihan', '$inp[idPegawai]', '$inp[idVendor]', '$inp[idTrainer]', '$inp[idKategori]', '$inp[idTraining]', '$inp[modul_pelatihan]', '$inp[kategori_level_pelatihan]', '$inp[program_pelatihan]','$inp[idDepartemen]', '" . setTanggal($inp[mulaiPelatihan]) . "','" . setTanggal($inp[selesaiPelatihan]) . "', '$inp[kodePelatihan]', '$inp[judulPelatihan]', '$inp[subPelatihan]', '" . setAngka($inp[pesertaPelatihan]) . "', '$inp[pelaksanaanPelatihan]', '$inp[lokasiPelatihan]', '" . setAngka($inp[biayaPelatihan]) . "', '$filePelatihan', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    foreach ($inp['batas'] as $key => $value) {
        $total += setAngka($value['biaya_semua']);

        $cekidmaster = getField("SELECT id_master FROM ctg_program_biaya_detail where id_pelatihan = '$idPelatihan' and id_master = '$key'");
        if (empty($cekidmaster)) {

            $sql_biaya = "INSERT ctg_program_biaya_detail SET id_pelatihan = '$idPelatihan' , id_program='$inp[program_pelatihan]', id_master='$key', biaya_perkategori='" . setAngka($value['biaya_semua']) . "'";

        } else {
            $sql_biaya = "UPDATE ctg_program_biaya_detail SET  id_program='$inp[program_pelatihan]', biaya_perkategori='" . setAngka($value['biaya_semua']) . "' where id_pelatihan = '$idPelatihan' and id_master = '$cekidmaster' ";

        }
        db($sql_biaya);
    }

    if (is_array($detail)) {

        ksort($detail);
        reset($detail);

        while (list($idDetail, $valDetail) = each($detail)) {
            list($keteranganDetail, $tanggalMulai, $waktuMulai, $tanggalSelesai, $waktuSelesai) = explode("\t", $valDetail);
            $mulaiDetail = setTanggal($tanggalMulai) . ' ' . $waktuMulai . ':00';
            $selesaiDetail = setTanggal($tanggalSelesai) . ' ' . $waktuSelesai . ':00';
            $sql = "insert into plt_pelatihan_detail (idPelatihan, idDetail, keteranganDetail, mulaiDetail, selesaiDetail, createBy, createTime) values ('$idPelatihan', '$idDetail', '$keteranganDetail', '$mulaiDetail', '$selesaiDetail', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
            db($sql);

            $idJadwal = getField("select idJadwal from plt_pelatihan_jadwal where idPelatihan='" . $idPelatihan . "' order by idJadwal desc limit 1") + 1;

            $sql = "insert into plt_pelatihan_jadwal (idPelatihan, idJadwal, idPegawai, judulJadwal, tanggalJadwal, mulaiJadwal, selesaiJadwal, keteranganJadwal, createBy, createTime) values ('$idPelatihan', '$idJadwal', '$inp[idPegawai]', '$keteranganDetail', '" . setTanggal($tanggalMulai) . "', '$waktuMulai', '$waktuSelesai', '', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
            db($sql);
        }

    }

    // list($mulaiPelatihan, $selesaiPelatihan) = explode("\t", getField("select concat(min(date(mulaiDetail)), '\t', max(date(selesaiDetail))) from plt_pelatihan_detail where idPelatihan='".$idPelatihan."'"));
    // $sql = "update plt_pelatihan set mulaiPelatihan='".$mulaiPelatihan."', selesaiPelatihan='".$selesaiPelatihan."' where idPelatihan='".$idPelatihan."'";
    // db($sql);

    echo "<script>window.location='?par[mode]=edit&par[idPelatihan]=$idPelatihan" . getPar($par, 'mode,idPelatihan') . "';</script>";
}

function approval()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername;
    $sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (empty($r[approveBy])) {
        $r[approveBy] = $cUsername;
    }
    if (empty($r[approveTime]) || $r[approveTime] == '0000-00-00 00:00:00') {
        $r[approveTime] = date('Y-m-d H:i:s');
    }
    list($tanggalPelatihan) = explode(' ', $r[approveTime]);

    $pending = $r[statusPelatihan] == 'p' ? 'checked="checked"' : '';
    $false = $r[statusPelatihan] == 'f' ? 'checked="checked"' : '';
    $true = (empty($pending) && empty($false)) ? 'checked="checked"' : '';

    $statusPelatihan = 'Belum Diproses';
    if ($r[statusPelatihan] == 't') {
        $statusPelatihan = 'Setuju';
    }
    if ($r[statusPelatihan] == 'f') {
        $statusPelatihan = 'Tolak';
    }
    if ($r[statusPelatihan] == 'p') {
        $statusPelatihan = 'Tunda';
    }

    setValidation('is_null', 'inp[keteranganPelatihan]', 'anda harus mengisi keterangan');
    $text = getValidation();

    $text .= '<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">APPROVAL RENCANA PELATIHAN</h1>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form id="form" name="form" method="post" class="stdform" action="?_submit=1' . getPar($par) . '" onsubmit="return validation(document.form);">
     <div id="general" class="subcontent">
      <p>
       <label class="l-input-small">Nama</label>';
    $text .= isset($menuAccess[$s]['apprlv1']) ?
        '<div class="field">
       <input type="text" id="inp[approveBy]" name="inp[approveBy]"  value="' . getField("select namaUser from app_user where username='$r[approveBy]'") . '" class="mediuminput" style="width:300px;" disabled="disabled"/>
     </div>' :
        '<span class="field">
     ' . getField("select namaUser from app_user where username='$r[approveBy]'") . '&nbsp;
   </span>';
    $text .= '
 </p>
 <p>
   <label class="l-input-small">Tanggal</label>';
    $text .= isset($menuAccess[$s]['apprlv1']) ?
        '<div class="field">
   <input type="text" id="tanggalPelatihan" name="inp[tanggalPelatihan]" size="10" maxlength="10" value="' . getTanggal($tanggalPelatihan) . '" class="vsmallinput hasDatePicker"  disabled="disabled"/>
 </div>' :
        '<span class="field">
 ' . getTanggal($tanggalPelatihan, 't') . '&nbsp;
</span>';
    $text .= '</p>
<p>
  <label class="l-input-small">Keterangan</label>';
    $text .= isset($menuAccess[$s]['apprlv1']) ?
        "<div class=\"field\">
  <textarea id=\"inp[keteranganPelatihan]\" name=\"inp[keteranganPelatihan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganPelatihan]</textarea>
</div>" :
        '<span class="field">
' . nl2br($r[keteranganPelatihan]) . '&nbsp;
</span>';
    $text .= '</p>
<p>
  <label class="l-input-small">Status</label>';
    $text .= isset($menuAccess[$s]['apprlv1']) ?
        "<div class=\"fradio\">
  <input type=\"radio\" id=\"true\" name=\"inp[statusPelatihan]\" value=\"t\" $true /> <span class=\"sradio\">Setuju</span>
  <input type=\"radio\" id=\"pending\" name=\"inp[statusPelatihan]\" value=\"p\" $pending /> <span class=\"sradio\">Tunda</span>
  <input type=\"radio\" id=\"false\" name=\"inp[statusPelatihan]\" value=\"f\" $false /> <span class=\"sradio\">Tolak</span>
</div>" :
        '<span class="field">
' . $statusPelatihan . '&nbsp;
</span>';
    $text .= '</p>
<p>';
    $text .= isset($menuAccess[$s]['apprlv1']) ?
        '<input type="submit" class="submit radius2" name="btnSave" value="Save"/>
  <input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>' :
        '<input type="button" class="cancel radius2" value="Close" style="float:right" onclick="closeBox();"/>
  <br clear="all">';
    $text .= '</p>
</div>
</form>
</div>';

    return $text;
}

function id()
{
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeMenu, '\t', namaMenu) from app_menu WHERE kodeData!='PKTG' AND kodeSite='$par[modul]' AND kodeInduk='0' ORDER BY urutanMenu");
    return implode("\n", $data);
}

function id2()
{
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(id_program, '\t', program) from ctg_program WHERE id_kategori='$par[kategori_level]' ORDER BY id_program");
    return implode("\n", $data);
}

function form()
{
    global $db, $s, $inp, $par, $det, $detail, $arrTitle, $arrParameter, $fileTemp, $fFile, $menuAccess, $areaCheck, $p;

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
    $r[idKategori] = empty($inp[idKategori]) ? $r[idKategori] : $inp[idKategori];
    $r[idTraining] = empty($inp[idTraining]) ? $r[idTraining] : $inp[idTraining];
    $r[idDepartemen] = empty($inp[idDepartemen]) ? $r[idDepartemen] : $inp[idDepartemen];

    $r[judulPelatihan] = empty($inp[judulPelatihan]) ? $r[judulPelatihan] : $inp[judulPelatihan];

    $r['mulaiPelatihan'] = empty($inp['mulaiPelatihan']) ? $r['mulaiPelatihan'] : setTanggal($inp['mulaiPelatihan']);
    $r['selesaiPelatihan'] = empty($inp['selesaiPelatihan']) ? $r['selesaiPelatihan'] : setTanggal($inp['selesaiPelatihan']);

    $r[subPelatihan] = empty($inp[subPelatihan]) ? $r[subPelatihan] : $inp[subPelatihan];
    $r[kodePelatihan] = empty($inp[kodePelatihan]) ? $r[kodePelatihan] : $inp[kodePelatihan];

    $r[pesertaPelatihan] = empty($inp[pesertaPelatihan]) ? $r[pesertaPelatihan] : setAngka($inp[pesertaPelatihan]);
    $r[pelaksanaanPelatihan] = empty($inp[pelaksanaanPelatihan]) ? $r[pelaksanaanPelatihan] : $inp[pelaksanaanPelatihan];

    $r[idVendor] = empty($inp[idVendor]) ? $r[idVendor] : $inp[idVendor];
    $r[idTrainer] = empty($inp[idTrainer]) ? $r[idTrainer] : $inp[idTrainer];

    $r[lokasiPelatihan] = empty($inp[lokasiPelatihan]) ? $r[lokasiPelatihan] : $inp[lokasiPelatihan];
    $r[biayaPelatihan] = empty($inp[biayaPelatihan]) ? $r[biayaPelatihan] : setAngka($inp[biayaPelatihan]);
    $r[filePelatihan] = empty($fileTemp) ? $r[filePelatihan] : $filePelatihan = uploadFiles("$r[idPelatihan]", "filePelatihan", "$fFile", "FP");;

    $eksternal = $r[pelaksanaanPelatihan] == 'e' ? 'checked="checked"' : '';
    $internal = empty($eksternal) ? 'checked="checked"' : '';

    $cat = getField("SELECT `kodeData` FROM `mst_data` WHERE `statusData` = 't' AND `kodeCategory` = '" . $arrParameter[5] . "' ORDER BY `urutanData` LIMIT 1");
    $status = getField("SELECT `kodeData` FROM `mst_data` WHERE `statusData` = 't' AND `kodeCategory` = '" . $arrParameter[6] . "' ORDER BY `urutanData` LIMIT 1");

    if (empty($fileTemp)) {
        $deleteFile = '<a href="?par[mode]=delFile' . getPar($par, 'mode') . "\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>";
    }

    if (!empty($r[kategori_level_pelatihan])) {
        $kodeMenu = "$r[kategori_level_pelatihan]";
    }

    setValidation('is_null', 'inp[judulPelatihan]', 'anda harus mengisi judul pelatihan');
    setValidation('is_null', 'inp[mulaiPelatihan]', 'anda harus mengisi Tanggal Mulai');
    setValidation('is_null', 'inp[selesaiPelatihan]', 'anda harus mengisi Tanggal Selesai');
    setValidation('is_null', 'inp[subPelatihan]', 'anda harus mengisi Sub');
    setValidation('is_null', 'inp[kodePelatihan]', 'anda harus mengisi Kode');
    setValidation('is_null', 'inp[idKategori]', 'anda harus mengisi Kategori');
    setValidation('is_null', 'inp[idTraining]', 'anda harus mengisi Training');
    setValidation('is_null', 'inp[idDepartemen]', 'anda harus mengisi Level');
    setValidation('is_null', 'inp[pesertaPelatihan]', 'anda harus mengisi Jumlah Peserta');
    setValidation('is_null', 'inp[idVendor]', 'anda harus mengisi Vendor');
    setValidation('is_null', 'inp[idTrainer]', 'anda harus mengisi Kordinator');
    setValidation('is_null', 'inp[lokasiPelatihan]', 'anda harus mengisi Lokasi');
    setValidation('is_null', 'inp[idPegawai]', 'anda harus mengisi Penanggung Jawab');
    $text = getValidation();

    $text .= "<style>
        #inp_kodeRekening__chosen{
          min-width:250px;
        }

        #inp_modul__chosen{
            min-width:520px;
        }

        #inp_kategori_level__chosen{
            min-width:520px;
        }

        #inp_program__chosen{
            min-width:520px;
        }
    </style>";

    $text .= '<div class="pageheader">
    <h1 class="pagetitle">' . $arrTitle[$s] . '</h1>
    ' . getBread(ucwords($par[mode] . ' data')) . '
    </div>
<div class="contentwrapper">

  <form id="form" name="form" method="post" class="stdform" action="?' . getPar($par) . "#detail\" enctype=\"multipart/form-data\">

   <!--<fieldset style=\"padding:10px; border-radius: 10px;\">
     <legend style=\"padding:10px; margin-left:20px;\"><h4>KATALOG PROGRAM</h4></legend>
     <p>
      <label class=\"l-input-small\">Modul</label>
      <div class=\"field\">
        " . comboData("SELECT kodeSite, keterangan from app_site WHERE kodeModul ='$kodeModul' ORDER BY kodeSite ASC", "kodeSite", "keterangan", "inp[modul_pelatihan]", "Pilih Modul", "$r[modul_pelatihan]", "onchange=\"getKodeSite('" . getPar($par, "mode") . "');\"", "520px", "chosen-select", "") . "
      </div>
    </p>
    <p>
      <label class=\"l-input-small\">Kategori Level</label>
      <div class=\"field\">
        " . comboData("SELECT `kodeMenu`, `namaMenu` FROM `app_menu` WHERE `kodeData` != 'PKTG'  AND `kodeSite` = '$r[modul_pelatihan]' AND `kodeInduk` = '0' ORDER BY `urutanMenu`", "kodeMenu", "namaMenu", "inp[kategori_level_pelatihan]", "Pilih Kategori Level", "$r[kategori_level_pelatihan]", "onchange=\"getKodeMenu('" . getPar($par, "mode") . "');\"", "520px", "chosen-select", "") . "
      </div>
    </p>
    <p>
      <label class=\"l-input-small\">Program</label>
      <span class=\"field\">
        " . comboData("SELECT id_program, program FROM ctg_program WHERE id_kategori = '$kodeMenu' ORDER BY id_program", "id_program", "program", "inp[program_pelatihan]", "Pilih Program", "$r[program_pelatihan]", "onchange=\"getIdProgram('" . getPar($par, "mode") . "');\"", "520px", "chosen-select", "") . "
      </span>
    </p>
  </fieldset>-->
  
  <fieldset  style=\"padding:10px; border-radius: 10px;\">
   <legend style=\"padding:10px; margin-left:20px;\"><h4>PELATIHAN</h4></legend>
   <div id=\"general\" style=\"margin-top:20px;\">

    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
      <tr>
        <td colspan=\"2\">
          <p>
            <label class=\"l-input-small\">Judul Pelatihan</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[judulPelatihan]\" name=\"inp[judulPelatihan]\"  value=\"$r[judulPelatihan]\" class=\"mediuminput\" style=\"width:530px;\" maxlength=\"150\" />
            </div>
          </p>
        </td>
      </tr>
      <tr>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small2\">Sub</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[subPelatihan]\" name=\"inp[subPelatihan]\"  value=\"$r[subPelatihan]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"150\" />
            </div>
          </p>
        </td>
        <td width=\"50%\">
          
        </td>
      </tr>
      <tr>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small2\">Tanggal Mulai</label>
            <div class=\"field\">
              <input type=\"text\" name=\"inp[mulaiPelatihan]\"  value=\"" . getTanggal($r[mulaiPelatihan]) . "\" id=\"inp[mulaiPelatihan]\" class=\"hasDatePicker\" style=\"width:220px;\"/>
            </div>
          </p>
        </td>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small\">Tanggal Selesai</label>
            <div class=\"field\">
              <input type=\"text\" name=\"inp[selesaiPelatihan]\"  value=\"" . getTanggal($r[selesaiPelatihan]) . "\" id=\"inp[selesaiPelatihan]\" class=\"hasDatePicker\" style=\"width:220px;\"/>
            </div>
          </p>
        </td>
      </tr>
      <tr>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small2\">Kategori</label>
            <div class=\"field\">
                " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[88] . "' ORDER BY `urutanData`", 'kodeData', 'namaData', 'inp[idTraining]', ' ', $r[idTraining], '', '213px', 'chosen-select') . "
            </div>
          </p>
        </td>
        <td width=\"50%\">
          
        </td>
      </tr>
      <tr>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small2\">Training</label>
            <div class=\"field\">
                " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' AND `kodeInduk` = '$r[idTraining]' ORDER BY `urutanData`", 'kodeData', 'namaData', 'inp[idKategori]', ' ', $r[idKategori], '', '213px', 'chosen-select') . "
            </div>
          </p>
        </td>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small\">Kode</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[kodePelatihan]\" name=\"inp[kodePelatihan]\"  value=\"$r[kodePelatihan]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"50\"/>
            </div>
          </p>
        </td>
      </tr>
      <tr>
        <td width=\"50%\">
          <p>
            <label class=\"l-input-small2\">Level</label>
            <div class=\"field\">
              " . comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'LVL' AND `statusData` = 't' ORDER BY `urutanData`", "kodeData", "namaData", "inp[idDepartemen]", " ", $r[idDepartemen], "", "213px", "chosen-select") . "
            </div>
          </p>
        </td>
        <td width=\"50%\">

        </td>
      </tr>
      <tr>
        <td colspan=\"2\">
          <p>
            <label class=\"l-input-small\">Jumlah Peserta</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[pesertaPelatihan]\" name=\"inp[pesertaPelatihan]\"  value=\"" . getAngka($r[pesertaPelatihan]) . "\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\"/> Orang
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Pelaksanaan</label>
            <div class=\"fradio\">
              <input type=\"radio\" id=\"true\" name=\"inp[pelaksanaanPelatihan]\" value=\"i\" onclick=\"pelaksanaan('" . $arrParameter[51] . "');\" $internal /> <span class=\"sradio\">In-House</span>
              <input type=\"radio\" id=\"false\" name=\"inp[pelaksanaanPelatihan]\" value=\"e\" onclick=\"pelaksanaan('0');\" $eksternal /> <span class=\"sradio\">Public Training</span>
            </div>
          </p>

          <p>
            <label class=\"l-input-small\">Vendor</label>
            <div class=\"field\">
              " . comboData("SELECT * FROM `dta_vendor` WHERE `statusVendor` = 't' ORDER BY `namaVendor`", 'kodeVendor', 'namaVendor', 'inp[idVendor]', ' ', $r[idVendor], "onchange=\"getTrainer('" . getPar($par, 'mode') . "');\"", '360px', 'chosen-select') . "
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Kordinator</label>
            <div class=\"field\">
              " . comboData("SELECT `idTrainer`, UPPER(`namaTrainer`) AS `namaTrainer` FROM `dta_trainer` WHERE `idVendor` = '$r[idVendor]' AND `statusTrainer` = 't' ORDER BY `namaTrainer`", "idTrainer", "namaTrainer", "inp[idTrainer]", " ", $r[idTrainer], "", "360px", "chosen-select") . "
            </div>
          </p>
          <p>

            <label class=\"l-input-small\">Lokasi</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[lokasiPelatihan]\" name=\"inp[lokasiPelatihan]\"  value=\"$r[lokasiPelatihan]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
            </div>
          </p>

          <table id=\"iPegawai\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;\">
            <tr>
              <td>
                <p>
                  <label class=\"l-input-small\">Penanggung Jawab</label>
                  <div class=\"field\">
                    " . comboData("SELECT `id`, UPPER(`name`) AS `name` FROM `emp` WHERE `cat` = '$cat' AND `status` = '$status' ORDER BY `name`", "id", "name", "inp[idPegawai]", "", $r[idPegawai], "", "360px", "chosen-select") . "
                  </div>
                </p>
              </td>
            </tr>
          </table>
          <p>
            <label class=\"l-input-small\">File</label>
            <div class=\"field\">";
    $text .= empty($r[filePelatihan]) ?
        "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:315px;\" maxlength=\"100\" />
              <div class=\"fakeupload\" style=\"width:375px;\">
               <input type=\"file\" id=\"filePelatihan\" name=\"filePelatihan\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
             </div>" :
        "<a href=\"download.php?d=rencana&f=$r[idPelatihan]&e=" . getExtension($fileTemp) . '">
             <img src="' . getIcon($fFile . "/" . $r[filePelatihan]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
             <input type=\"file\" id=\"filePelatihan\" name=\"filePelatihan\" style=\"display:none;\" />
             " . $deleteFile . "
             <input type=\"hidden\" id=\"fileTemp\" name=\"fileTemp\" value=\"'" . $fileTemp . "'\"/>
             <br clear=\"all\">";
    $text .= "</div>
           </p>

         </td>

       </tr>
     </table>
   </fieldset>
   " . getHistory("plt_pelatihan", "idPelatihan", "$par[idPelatihan]", "createBy", "createTime", "updateBy", "updateTime") . "
   ";
    $text .= "
   <br clear=\"all\">
   
   <h4>PELAKSANAAN</h4>
   <hr>
   <div class=\"notibar announcement\" style=\"background-color: #FFD863; border-color: #FFD863; color: #000; margin: 0 0 10px 0;\">
    <a class=\"close\"></a>
    <p><b>Informasi : </b>Daftar pelaksanaan pelatihan per hari, detail per sesi di kolom detail</p>
  </div>
  <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
    <thead>
     <tr>
      <th width=\"20\">No.</th>
      <th>Uraian</th>
      <th width=\"225\">Mulai</th>
      <th width=\"225\">Selesai</th>
      <th width=\"75\">Detail</th>
      <th width=\"75\">Control</th>
    </tr>
  </thead>
  <tbody>";

    $detail = is_array($detail) ? $detail : array();
    if (!empty($det[keteranganDetail]) && !empty($inp[save_detail])) {
        unset($detail["$inp[tempDetail]"]);
        if (!empty($det[tanggalMulai])) {
            $detail["$det[kodeDetail]"] = $det[keteranganDetail] . "\t" . $det[tanggalMulai] . "\t" . $det[waktuMulai] . "\t" . $det[tanggalSelesai] . "\t" . $det[waktuSelesai];
        }
    }
    unset($detail["$inp[delete_detail]"]);

    $no = 1;
    $dta = array();
    if (is_array($detail)) {
        ksort($detail);
        reset($detail);
        while (list($kodeDetail, $valDetail) = each($detail)) {
            $dta[$no] = $valDetail;
            ++$no;
        }
    }
    $detail = $dta;

    $no = 1;
    $_total = 0;
    if (is_array($detail)) {
        ksort($detail);
        reset($detail);
        while (list($kodeDetail, $valDetail) = each($detail)) {
            $_total++;
            list($keteranganDetail, $tanggalMulai, $waktuMulai, $tanggalSelesai, $waktuSelesai) = explode("\t", $valDetail);

            $text .= '<input type="hidden" id="detail[' . $kodeDetail . ']" name="detail[' . $kodeDetail . ']"  value="' . $valDetail . '">';
            if ($inp[edit_detail] == $kodeDetail) {
                $text .=
                    "<tr>
         <td><input type=\"hidden\" id=\"det[kodeDetail]\" name=\"det[kodeDetail]\"  value=\"$no\">$no.</td>
         <td><input type=\"text\" id=\"det[keteranganDetail]\" name=\"det[keteranganDetail]\"  value=\"$keteranganDetail\" class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
         <td align=\"center\">
          <input type=\"text\" id=\"tanggalMulai\" name=\"det[tanggalMulai]\" size=\"10\" maxlength=\"10\" value=\"" . $tanggalMulai . "\" class=\"vsmallinput hasDatePicker\"/>       
          <input type=\"text\" id=\"waktuMulai\" name=\"det[waktuMulai]\" size=\"10\" maxlength=\"5\" value=\"" . $waktuMulai . "\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
        </td>
        <td align=\"center\">
          <input type=\"text\" id=\"tanggalSelesai\" name=\"det[tanggalSelesai]\" size=\"10\" maxlength=\"10\" value=\"" . $tanggalSelesai . "\" class=\"vsmallinput hasDatePicker\"/>        
          <input type=\"text\" id=\"waktuSelesai\" name=\"det[waktuSelesai]\" size=\"10\" maxlength=\"5\" value=\"" . $waktuSelesai . "\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
        </td>
        <td align=\"center\">-</td>
        <td><input type=\"submit\" class=\"add\" name=\"inp[save_detail]\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>
      </tr>
      ";
            } else {
                $text .=

                    "<tr>
     <td>$no.</td>
     <td>$keteranganDetail</td>
     <td align=\"center\">$tanggalMulai&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$waktuMulai</td>
     <td align=\"center\">$tanggalSelesai&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$waktuSelesai</td>
     <td align=\"center\">";
                if (getField("SELECT idDetail FROM plt_pelatihan_detail WHERE idPelatihan = '$par[idPelatihan]' AND idDetail = '$kodeDetail'"))
                    $text .= "<a href=\"?c=20&p=79&m=933&s=933&par[mode]=det&par[tanggalJadwal]=" . setTanggal($tanggalMulai) . "&par[idPelatihan]=$par[idPelatihan]\" class=\"detail\" title=\"Detail Data\"><span>Detail Data</span></a>";
                else
                    $text .= "-";
                $text .= "</td>
      <td align=\"center\">
       <input type=\"submit\" class=\"edit\" name=\"inp[edit_detail]\" value=\"$kodeDetail\"/>
       <input type=\"submit\" class=\"delete\" name=\"inp[delete_detail]\" value=\"$kodeDetail\"/>
     </td>
   </tr>";
            }

            ++$no;
        }
    }

    if (empty($inp[edit_detail])) {
        $text .= "
  <tr>
    <td><input type=\"hidden\" id=\"det[kodeDetail]\" name=\"det[kodeDetail]\"  value=\"$no\">$no.</td>
    <td><input type=\"text\" id=\"det[keteranganDetail]\" name=\"det[keteranganDetail]\"  class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
    <td align=\"center\">
      <input type=\"text\" id=\"tanggalMulai\" name=\"det[tanggalMulai]\" size=\"10\" maxlength=\"10\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"waktuMulai\" name=\"det[waktuMulai]\" size=\"10\" maxlength=\"5\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
    </td>
    <td align=\"center\">
      <input type=\"text\" id=\"tanggalSelesai\" name=\"det[tanggalSelesai]\" size=\"10\" maxlength=\"10\" class=\"vsmallinput hasDatePicker\"/> <input type=\"text\" id=\"waktuSelesai\" name=\"det[waktuSelesai]\" size=\"10\" maxlength=\"5\" class=\"vsmallinput hasTimePicker\" style=\"background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\" readonly=\"readonly\"/>
    </td>
    <td align=\"center\">";
        if (getField("SELECT idDetail FROM plt_pelatihan_detail WHERE idPelatihan = '$par[idPelatihan]' and idDetail = '$kodeDetail'"))
            $text .= "<a href=\"?par[mode]=det&c=15&p=42&m=530&s=530&_p=0&_l=10&par[tanggalJadwal]=" . setTanggal($tanggalMulai) . "&par[idPelatihan]=$par[idPelatihan]\" class=\"detail\" title=\"Detail Data\"><span>Detail Data</span></a>";
        else
            $text .= "-";
        $text .= "</td>
      <td><input type=\"submit\" class=\"add\" name=\"inp[save_detail]\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>
    </tr>";
    }

    $text .= "</tbody>
</table>
<br clear=\"all\">
<h4 style='float:left; padding-top:10px;'>DOKUMEN</h4>
";

    if (!empty($par['idPelatihan'])) :
        $text .= "<a href=\"#\" id=\"\" class=\"btn btn1 btn_document\" style='float:right; margin-bottom:5px;' onclick=\"openBox('popup.php?par[mode]=addDokumen" . getPar($par, "mode") . "',850,300)\"><span>Tambah</span></a>";
    endif;

    $text .= "
<hr style='clear:both;'>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
  <thead>
   <tr>
    <th width=\"20\">No.</th>
    <th width=\"*\">Judul</th>
    <th width=\"75\">File</th>
    <th width=\"200\">User</th>
    <th width=\"75\">Control</th>
  </tr>
</thead>
<tbody>";
    $sql = db("SELECT * from perencanaan_dokumen where id_pelatihan = '$par[idPelatihan]'");
    while ($r = mysql_fetch_assoc($sql)) {
        $no2++;
        $text .= "
    <tr>
      <td>$no2</td>
      <td>$r[keterangan]</td>
      <td align='center'><img src='" . getIcon($r[file]) . "' style='width:20px;' onclick=\"openBox('view.php?doc=dokumen_pelatihan&par[id_pdokumen]=$r[id_pdokumen]" . getPar($par, "mode, id_pdokumen") . "',900,500);\"></td>
      <td>$r[created_by]</td>
      <td align='center'>
        <!--<a href='#' class='edit' onclick=\"openBox('popup.php?par[mode]=addDokumen&par[id_pdokumen]=$r[id_pdokumen]" . getPar($par, "mode,id_pdokumen") . "',850,300)\"></a>-->
        <a href='?par[mode]=delDokumen&par[id_pdokumen]=$r[id_pdokumen]" . getPar($par, "mode, id_pdokumen") . "' class='delete'></a>
      </td>
    </tr>
    ";
    }
    $text .= "
</tbody>
</table>
<p style=\"position:absolute;top:10px;right:5px\">
  <input type=\"hidden\" id=\"_submit\" name=\"_submit\"  value=\"\">
  <input type=\"hidden\" id=\"_total\" name=\"_total\"  value=\"$_total\">
  <input type=\"hidden\" id=\"inp[filePelatihan]\" name=\"inp[filePelatihan]\"  value=\"$r[filePelatihan]\">
  <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return chk('" . getPar($par, 'mode') . "');\"/>
  <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?" . getPar($par, 'mode,idPegawai,idPelatihan,tahunPelatihan') . "';\"/>
</p>
</form>";

    $res1 = db("SELECT `kodeInduk`, `kodeData`, `namaData`, `kodeMaster` FROM `mst_data` WHERE `kodeCategory` = 'PL02' ORDER BY `urutanData`");
    $res2 = db("SELECT `kodeInduk`, `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'LVL' ORDER BY `urutanData`");

    $trainings = [];
    $levels = [];

    while ($row = mysql_fetch_assoc($res1)) {

        $trainings[$row['kodeInduk']][$row['kodeData']] = [
            'name' => $row['namaData'],
            'number' => $row['kodeMaster']
        ];

    }

    while ($row = mysql_fetch_assoc($res2)) {
        $levels[$row['kodeInduk']][$row['kodeData']] = $row['namaData'];
    }

    $text .= "
    <script>
      
        var trainings = " . json_encode($trainings) . "
        var levels = " . json_encode($levels) . "
        
        console.log(levels)
      
        pelaksanaan('" . $r[idVendor] . "');
    
        jQuery(\"#inp\\\[pesertaPelatihan\\\]\").keyup(function() {
        
            let value = jQuery(this).val()
            
            if (Number(value) <= 25) {
              jQuery(\".fradio #false\").click().click()
            } else {
              jQuery(\".fradio #true\").click().click()
            }
        
        })
                
        jQuery('#inp\\\[idTraining\\\]').change(function () {
            
            category = jQuery(this).val()
            
            jQuery('#inp\\\[idKategori\\\]').empty()
//            jQuery('#inp\\\[idDepartemen\\\]').empty()
            
            for (let key in trainings[category]) {
                item = trainings[category][key]
                jQuery('#inp\\\[idKategori\\\]').append(`<option value='` + key + `'>` + item['name'] + `</option>`)
            }
            
//            for (let key in levels[category]) {
//                item = levels[category][key]
//                jQuery('#inp\\\[idDepartemen\\\]').append(`<option value='` + key + `'>` + item + `</option>`)
//            }

            jQuery('.chosen-select').trigger('chosen:updated')
            jQuery('#inp\\\[idKategori\\\]').change()
            
        })

        jQuery(\"#inp\\\[idKategori\\\]\").change(function() {
        
            category = jQuery('#inp\\\[idTraining\\\]').val()
            code = jQuery(this).val()
        
            jQuery(\"#inp\\\[kodePelatihan\\\]\").val(trainings[category][code]['number'])
 
            
        })
        
    </script>";

    return $text;
}

function detail_team()
{
    global $s, $inp, $par, $arrTitle, $menuAccess, $arrColor, $arrParameter;
    $sql = "SELECT * FROM plt_pelatihan WHERE idPelatihan = '$par[pelatihan]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $namaModul = getField("SELECT keterangan FROM app_site WHERE kodeSite='$r[id_modul]'");
    $namaKategori = getField("SELECT keterangan FROM app_menu WHERE kodeMenu='$r[id_kategori]'");

    if (empty($_submit) && empty($par[tahun])) $par[tahun] = date('Y');
    $text .= "
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread() . "
    <span class=\"pagedesc\">&nbsp;</span>
  </div> 

  <p style=\"position: absolute; right: 20px; top: 10px;\">
  </p>   

  <div id=\"contentwrapper\" class=\"contentwrapper\">
   <form action=\"\" method=\"post\" id=\"form2\" class=\"stdform\" onsubmit=\"return false;\">
     <div id=\"pos_l\" style=\"float:left;\">

     </div>

     <fieldset style=\"padding:10px; border-radius: 10px; margin-bottom: 20px;\">
      <legend>Pelatihan</legend>
      <p>
        <label class=\"l-input-small\" style=\"width:150px;\">Pelatihan</label>
        <span class=\"field\" style=\"margin-left:150px;\">$r[judulPelatihan]&nbsp;</span>
      </p>
      <table style=\"width:100%; margin-top:-5px; margin-bottom:-5px;\" cellpadding=\"0\" cellspacing=\"0\">
       <tr>
         <td style=\"width:50%\">
          <p>
           <label class=\"l-input-small\" style=\"width:150px;\">Mulai</label>
           <span class=\"field\" style=\"margin-left:150px;\">" . getTanggal($r[mulaiPelatihan]) . "&nbsp;</span>
         </p>
       </td>
       <td style=\"width:50%\">
        <p>
         <label class=\"l-input-small\" style=\"width:150px;\">Selesai</label>
         <span class=\"field\" style=\"margin-left:150px;\">" . getTanggal($r[selesaiPelatihan]) . "&nbsp;</span>
       </p>
     </td>
   </tr>
 </table>
 <p>
  <label class=\"l-input-small\" style=\"width:150px;\">Vendor</label>
  <span class=\"field\" style=\"margin-left:150px;\">" . getField("SELECT namaVendor FROM dta_vendor where kodeVendor = '$r[idVendor]'") . "&nbsp;</span>
</p>
<p>
  <label class=\"l-input-small\" style=\"width:150px;\">Lokasi</label>
  <span class=\"field\" style=\"margin-left:150px;\">$r[lokasiPelatihan]&nbsp;</span>
</p>

</fieldset>
</form>
<div class=\"widgetbox\">

  <div class=\"title\" style=\"margin-bottom:0px;\">
    <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=tambahTeam&par[pelatihan]=$par[pelatihan]" . getPar($par, "mode,id_pelatihan") . "',725,300);\" class=\"btn btn1 btn_document\" style=\"float:right;margin-left:10px;\"><span>Tambah Data</span></a><h3 style=\"float:left;margin-top: 15px;\"> Team </h3></div>
  </div>
  <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">

    <thead>

      <tr>
        <th style=\"width: 10px;\">No.</th>
        <th width=\"190\">Nama</th>
        <th width=\"120\">Tugas</th>
        <th width=\"180\">Keterangan</th>
        <th width=\"60\">Kontrol</th>
      </tr>
    </thead>

    <tbody>
      ";
    $sql = "select * from plt_team where id_pelatihan_team = '$par[pelatihan]'";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        $r[status] = $r[status] == "t" ? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/f.png\" title=\"Ditolak\">";
        $text .= "<tr>
        <td>$no.</td>
        <td>" . getField("SELECT name from emp where id='$r[id_pegawai_team]'") . "</td>
        <td>" . getField("SELECT namaData from mst_data where kodeData='$r[id_jabatan_team]'") . "</td>
        <td>$r[keterangan_team]</td>

        <td align=\"center\">
          <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=editTeam&par[id_pteam]=$r[id_pteam]" . getPar($par, "mode,id_pteam") . "',725,300);\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>
          <a href=\"?par[mode]=delTeam&par[id_pteam]=$r[id_pteam]" . getPar($par, "mode,id_pteam") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";

        $text .= "</tr>";
    }
    $text .= "</tbody>
      </table>
    </form> 
  </div>
  ";
    return $text;
}

function form_team()
{
    global $s, $inp, $par, $menuAccess, $fManual, $cUsername, $arrTitle, $p;

    $sql = "SELECT * FROM plt_team WHERE id_pteam='$par[id_pteam]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $text .= "  <style>
            #inp_id_pegawai_team__chosen{
  min-width:230px;
}

        #inp_id_jabatan_team__chosen{
min-width:230px;
}    
</style>

<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread(ucwords($par[mode] . " data")) . "
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">   
      <p style=\"position:absolute;right:5px;top:5px;\">
        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
        <input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
      </p>
    </br>
    <fieldset>
      <p>
        <label class=\"l-input-small\">Nama</label>
        <div class=\"fiel\">
          " . comboData("SELECT id,name FROM emp", "id", "name", "inp[id_pegawai_team]", "", $r[id_pegawai_team], "", "230px", "chosen-select") . "
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Jabatan Team</label>
        <div class=\"fiel\">
          " . comboData("SELECT kodeData,namaData FROM mst_data where kodeCategory='JT' AND statusData='t'", "kodeData", "namaData", "inp[id_jabatan_team]", "", $r[id_jabatan_team], "", "230px", "chosen-select") . "
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Keterangan</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[keterangan_team]\" name=\"inp[keterangan_team]\"  value=\"$r[keterangan_team]\" class=\"smallinput\" style=\"width:270px;\"/>
        </div>
      </p>
    </fieldset>
  </form> 
</div>";
    return $text;
}

function add_team()
{
    global $s, $inp, $par, $cUsername, $arrParam, $dta_;
    $id = getField("select id_pteam from plt_team order by id_pteam desc limit 1") + 1;
    $sql = "insert into plt_team (id_pteam, id_pelatihan_team, id_pegawai_team, id_jabatan_team, keterangan_team, create_by, create_date) values ('$id', '$par[pelatihan]','$inp[id_pegawai_team]','$inp[id_jabatan_team]', '$inp[keterangan_team]','$cUsername', now())";

    db($sql);
    echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
}

function ubah_team()
{
    global $s, $inp, $par, $cUsername, $arrParam, $dta_;

    $sql = "UPDATE plt_team SET id_pegawai_team = '$inp[id_pegawai_team]',id_jabatan_team = '$inp[id_jabatan_team]',keterangan_team = '$inp[keterangan_team]',update_by = '$cUsername', update_date = now() WHERE id_pteam = '$par[id_pteam]'";

    db($sql);
    echo "<script>alert('TAMBAH DATA BERHASIL');closeBox();reloadPage();</script>";
}

function hapus_team()
{
    global $s, $inp, $par, $fRencana, $cUsername;
    $sql = "delete from plt_team where id_pteam='$par[id_pteam]'";
    db($sql);
    echo "<script>window.location='?par[mode]=team" . getPar($par, "mode,id_pteam") . "';</script>";
}

function insertByImport()
{

    global $inp, $file_log, $cID;

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
    $vendor_number = $data[1];
    $trainer_number = $data[2];
    $name = $data[3];
    $date_start = $data[4];
    $date_end = $data[5];
    $training_number = $data[6];
    $type_id = $data[7];
    $level_id = $data[8];
    $participant = $data[9];
    $address = $data[10];
    $emp_number = $data[11];

    $vendor_id = getField("SELECT `kodeVendor` FROM `dta_vendor` WHERE `nomorVendor` = '$vendor_number'");
    $training_id = getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PL02' AND `kodeMaster` = '$training_number'");

    $trainer_id = getField("SELECT `idTrainer` FROM `dta_trainer` WHERE `noregister_trainer` = '$trainer_number'");
    $emp_id = getField("SELECT `id` FROM `emp` WHERE `reg_no` = '$emp_number'");

    $sql = "INSERT INTO `plt_pelatihan` SET
        `idVendor` = '$vendor_id',
        `idPegawai` = '$emp_id',
        `idKategori` = '" . ($training_id ?: 0) . "',
        `idTraining` = '$type_id',
        `judulPelatihan` = '$name',
        `subPelatihan` = '',
        `idDepartemen` = '$level_id',
        `idTrainer` = '$trainer_id',
        `mulaiPelatihan` = '$date_start',
        `selesaiPelatihan` = '$date_end',
        `kodePelatihan` = '$training_number',
        `pesertaPelatihan` = '$participant',
        `pelaksanaanPelatihan` = '" . ($participant < 25 ? "e" : "i") . "',
        `lokasiPelatihan` = '$address',
        `keteranganPelatihan` = 'upload',
        `statusPelatihan` = 't',
        `createTime` = '" . date('Y-m-d H:i:s') . "',
        `createBy` = '$cID'
    ";

    if (db($sql)) {

        fwrite($log, "INSERT \t\t: " . date("d/m/Y H:i:s") . " \tNo: $number\n");
        fclose($log);

        return;
    }

    fwrite($log, "FAILED \t\t: " . date("d/m/Y H:i:s") . " \tNo: $number\n");
    fclose($log);
}


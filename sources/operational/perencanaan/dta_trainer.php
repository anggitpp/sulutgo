<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile2 = "files/export/";

$path_import = "files/imports/";
$file_log = "files/logs/data_trainer.log";
$file_template = "files/templates/template-trainer.xlsx";

function getContent($par)
{

    global $s, $_submit, $menuAccess;

    switch ($par['mode']) {

        case "kta":
            $text = kota();
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

        case "import":
            formImport("Import Trainer");
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
    global $s, $par, $arrTitle, $menuAccess;

    $text = "<div class=\"pageheader\">
      <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
      " . getBread() . "
    </div>
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
      <div style=\"padding-bottom:10px;\">
      </div>
      <form action=\"\" method=\"post\" class=\"stdform\">
        <div id=\"pos_l\" style=\"float:left;\">
          <table>
            <tr>   
              <td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder='Search'/></td>        
              <td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
            </tr>
          </table>
        </div>
        <div id=\"pos_r\">";

    if (isset($menuAccess[$s]["add"]))
        $text .= "<a class=\"btn btn1 btn_inboxi\" id=\"import\">
                    <span>Import</span>
                </a>&nbsp;";

    $text .= "<a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>&emsp;";

    if (isset($menuAccess[$s]["add"]))
        $text .= "<a href=\"?par[mode]=add" . getPar($par, "mode, idTrainer") . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";

    $text .= "
            </div>
        </form>
        <br clear=\"all\" />
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
          <thead>
            <tr>
              <th width=\"20\">No.</th>         
              <th width=\"100\">No. Register</th>
              <th>Nama</th>
              <th>Vendor</th>
              <th style=\"width:150px;\">No. HP</th>
              <th style=\"width:150px;\">Email</th>
              <th style=\"width:50px;\">Status</th>";

    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
        $text .= "<th width=\"50\">Kontrol</th>";

    $text .= "</tr>
            </thead>
            <tbody>";

    if (!empty($par[filter]))
        $filter = " and (
              lower(t1.namaTrainer) like '%" . strtolower($par[filter]) . "%'
              or lower(t2.namaVendor) like '%" . strtolower($par[filter]) . "%'
              or lower(t1.handphoneTrainer) like '%" . strtolower($par[filter]) . "%'
              or lower(t1.emailTrainer) like '%" . strtolower($par[filter]) . "%'
              )";

    $sql = "select t1.*, t2.namaVendor from dta_trainer t1 join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.namaTrainer";
    $res = db($sql);

    while ($r = mysql_fetch_array($res)) {

        $no++;

        $statusTrainer = $r[statusTrainer] == "t" ?
            "<img src=\"styles/images/t.png\" title='Active'>" :
            "<img src=\"styles/images/f.png\" title='Not Active'>";

        $text .= "<tr>
                <td>$no.</td>     
                <td>$r[noregister_trainer]</td>
                <td>$r[namaTrainer]</td>
                <td>$r[namaVendor]</td>
                <td>$r[handphoneTrainer]</td>
                <td>$r[emailTrainer]</td>
                <td align=\"center\">$statusTrainer</td>";

        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"?par[mode]=edit&par[idTrainer]=$r[idTrainer]" . getPar($par, "mode,idTrainer") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";

            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=del&par[idTrainer]=$r[idTrainer]" . getPar($par, "mode,idTrainer") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }

        $text .= "</tr>";
    }

    $text .= "</tbody>
            </table>
          </div>
          <script>
          
            jQuery(\"#btnExport\").live('click', function(e){
              e.preventDefault();
              window.location.href=\"?par[mode]=xls\"+\"" . getPar($par, "mode") . "\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
            });
            
            jQuery(\"#import\").click(function () {
                openBox(`popup.php?par[mode]=import" . getPar($par, "mode") . "`, 700, 250)
            })
            
          </script>
          ";

    if ($par[mode] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    return $text;
}

function kota()
{
    global $db, $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[idPropinsi]' and kodeCategory='" . $arrParameter[4] . "' order by namaData");
    return implode("\n", $data);
}

function hapus()
{
    global $db, $s, $inp, $par, $cUsername;
    $sql = "delete from dta_trainer where idTrainer='$par[idTrainer]'";
    db($sql);
    echo "<script>window.location='?" . getPar($par, "mode,idTrainer") . "';</script>";
}

function ubah()
{
    global $db, $s, $inp, $par, $cUsername;
    repField(array("keahlianTrainer", "kerjaTrainer", "pendidikanTrainer"));

    $sql = "update dta_trainer set idVendor='$inp[idVendor]', idPropinsi='$inp[idPropinsi]', noregister_trainer = '$inp[noregister_trainer]', expertise_trainer = '$inp[expertise_trainer]', idKota='$inp[idKota]', idTipe='$inp[idTipe]', namaTrainer='$inp[namaTrainer]', jabatanTrainer='$inp[jabatanTrainer]', alamatTrainer='$inp[alamatTrainer]', teleponTrainer='$inp[teleponTrainer]', handphoneTrainer='$inp[handphoneTrainer]', emailTrainer='$inp[emailTrainer]', keahlianTrainer='$inp[keahlianTrainer]', kerjaTrainer='$inp[kerjaTrainer]', pendidikanTrainer='$inp[pendidikanTrainer]', keteranganTrainer='$inp[keteranganTrainer]', statusTrainer='$inp[statusTrainer]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where idTrainer='$par[idTrainer]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idTrainer") . "';</script>";
}

function tambah()
{
    global $db, $s, $inp, $par, $cUsername;
    repField(array("keahlianTrainer", "kerjaTrainer", "pendidikanTrainer"));
    $idTrainer = getField("select idTrainer from dta_trainer order by idTrainer desc limit 1") + 1;

    $sql = "insert into dta_trainer (idTrainer, idVendor, idPropinsi, idKota, idTipe, namaTrainer, jabatanTrainer, expertise_trainer, noregister_trainer, alamatTrainer, teleponTrainer, handphoneTrainer, emailTrainer, keahlianTrainer, kerjaTrainer, pendidikanTrainer, keteranganTrainer, statusTrainer, createBy, createTime) values ('$idTrainer', '$inp[idVendor]', '$inp[idPropinsi]', '$inp[idKota]', '$inp[idTipe]', '$inp[namaTrainer]', '$inp[jabatanTrainer]', '$inp[expertise_trainer]', '$inp[noregister_trainer]', '$inp[alamatTrainer]', '$inp[teleponTrainer]', '$inp[handphoneTrainer]', '$inp[emailTrainer]', '$inp[keahlianTrainer]', '$inp[kerjaTrainer]', '$inp[pendidikanTrainer]', '$inp[keteranganTrainer]', '$inp[statusTrainer]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,idTrainer") . "';</script>";
}

function form()
{

    global $s, $par, $arrTitle, $arrParameter;

    include "plugins/mces.jsp";

    $sql = "select * from dta_trainer where idTrainer='$par[idTrainer]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $false = $r[statusTrainer] == "f" ? "checked=\"checked\"" : "";
    $true = empty($false) ? "checked=\"checked\"" : "";

    setValidation("is_null", "inp[namaTrainer]", "anda harus mengisi nama");
    setValidation("is_null", "inp[idVendor]", "anda harus mengisi vendor");
    $text = getValidation();

    $text .= "<div class=\"pageheader\">
  <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
  " . getBread(ucwords($par[mode] . " data")) . "               
</div>
<div class=\"contentwrapper\">
  <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\"> 
    <div id=\"general\" style=\"margin-top:20px;\">
      <fieldset>
        <legend>Form Trainer</legend>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td width=\"55%\">
              <p>
                <label class=\"l-input-small\">Nama</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[namaTrainer]\" name=\"inp[namaTrainer]\"  value=\"$r[namaTrainer]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Jabatan</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[jabatanTrainer]\" name=\"inp[jabatanTrainer]\"  value=\"$r[jabatanTrainer]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Expertise</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[expertise_trainer]\" name=\"inp[expertise_trainer]\"  value=\"$r[expertise_trainer]\" class=\"mediuminput\" style=\"width:390px;\" maxlength=\"250\" />
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">No. Register</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[noregister_trainer]\" name=\"inp[noregister_trainer]\"  value=\"$r[noregister_trainer]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"250\" />
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Alamat</label>
                <div class=\"field\">               
                  <textarea id=\"inp[alamatTrainer]\" name=\"inp[alamatTrainer]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[alamatTrainer]</textarea>
                </div>
              </p>
            </td>
            <td width=\"45%\">
              &nbsp;
            </td>
          </tr>
        </table>  
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td width=\"55%\">
              <p>
                <label class=\"l-input-small\">Propinsi</label>
                <div class=\"field\">               
                  " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[3] . "' order by namaData", "kodeData", "namaData", "inp[idPropinsi]", " ", $r[idPropinsi], "onchange=\"getKota('" . getPar($par, "mode,idPropinsi") . "');\"", "260px", "chosen-select") . "
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Telepon</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[teleponTrainer]\" name=\"inp[teleponTrainer]\"  value=\"$r[teleponTrainer]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\" />
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Email</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[emailTrainer]\" name=\"inp[emailTrainer]\"  value=\"$r[emailTrainer]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\" />
                </div>
              </p>
            </td>
            <td width=\"45%\">
              <p>
                <label class=\"l-input-small\">Kota</label>
                <div class=\"field\">               
                  " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[4] . "' and kodeInduk='$r[idPropinsi]' order by namaData", "kodeData", "namaData", "inp[idKota]", " ", $r[idKota], "", "260px", "chosen-select") . "
                </div>
              </p>
              <p>
                <label class=\"l-input-small\">Handphone</label>
                <div class=\"field\">               
                  <input type=\"text\" id=\"inp[handphoneTrainer]\" name=\"inp[handphoneTrainer]\"  value=\"$r[handphoneTrainer]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\" />
                </div>
              </p>
            </td>
          </tr>
        </table>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td width=\"55%\">
              <p>
                <label class=\"l-input-small\">Tipe</label>
                <div class=\"field\">               
                  " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[44] . "' order by urutanData", "kodeData", "namaData", "inp[idTipe]", " ", $r[idTipe], "", "260px", "chosen-select") . "
                </div>
              </p>
            </td>
            <td width=\"45%\">
              <p>
                <label class=\"l-input-small\">Vendor</label>
                <div class=\"field\">               
                  " . comboData("select * from dta_vendor where statusVendor='t' order by namaVendor", "kodeVendor", "namaVendor", "inp[idVendor]", " ", $r[idVendor], "", "260px", "chosen-select") . "
                </div>
              </p>
            </tr>
          </table>
        </fieldset>
        <br clear=\"all\">
        <ul class=\"hornav\">
          <li class=\"current\"><a href=\"#keahlian\">Keahlian</a></li>
          <li><a href=\"#kerja\">Riwayat Kerja</a></li>
          <li><a href=\"#pendidikan\">Riwayat Pendidikan</a></li>
        </ul>
        <div id=\"keahlian\" class=\"subcontent\" >
          <textarea id=\"mce1\" name=\"inp[keahlianTrainer]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[keahlianTrainer]</textarea>
        </div>
        <div id=\"kerja\" class=\"subcontent\" style=\"display:none\" >
          <textarea id=\"mce2\" name=\"inp[kerjaTrainer]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[kerjaTrainer]</textarea>
        </div>
        <div id=\"pendidikan\" class=\"subcontent\" style=\"display:none\" >
          <textarea id=\"mce3\" name=\"inp[pendidikanTrainer]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">$r[pendidikanTrainer]</textarea>
        </div>
        <br clear=\"all\">
        <fieldset>
          <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
            <tr>
              <td width=\"55%\">
                <p>
                  <label class=\"l-input-small\">Keterangan</label>
                  <div class=\"field\">               
                    <textarea id=\"inp[keteranganTrainer]\" name=\"inp[keteranganTrainer]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganTrainer]</textarea>
                  </div>
                </p>
                <p>
                  <label class=\"l-input-small\">Status</label>
                  <div class=\"fradio\">
                    <input type=\"radio\" id=\"true\" name=\"inp[statusTrainer]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
                    <input type=\"radio\" id=\"false\" name=\"inp[statusTrainer]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
                  </div>
                </p>
              </td>
              <td width=\"45%\">
                &nbsp;
              </tr>
            </table>
          </fieldset>";

    $text .= $par['idTrainer'] ? getHistory("dta_trainer", "idTrainer", "$par[idTrainer]", "createBy", "createTime", "updateBy", "updateTime") : "";

    $text .= "</div>
        <p style=\"position:absolute;top:10px;right:5px\">          
          <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
          <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?" . getPar($par, "mode,idPegawai") . "';\"/>         
        </p>
      </form>";
    return $text;
}

function xls()
{
    global $s, $par, $arrTitle, $fFile2;

    $direktori = $fFile2;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";
    $field = array("no", "nama", "vendor", "no hp", "email", "status");

    if (!empty($par[filter]))
        $filter .= " and (
          lower(t1.namaTrainer) like '%" . strtolower($par[filter]) . "%'
          or lower(t2.namaVendor) like '%" . strtolower($par[filter]) . "%'
          or lower(t1.handphoneTrainer) like '%" . strtolower($par[filter]) . "%'
          or lower(t1.emailTrainer) like '%" . strtolower($par[filter]) . "%'
          )";

    $sql = "select t1.*, t2.namaVendor from dta_trainer t1 join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.namaTrainer";

    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_array($res)) {
        $statusTrainer = $r[statusTrainer] == "t" ?
            "Active" :
            "Not Active";
        $no++;

        $data[] = array($no . "\t center",
            $r[namaTrainer] . "\t left",
            $r[namaVendor] . "\t left",
            $r[handphoneTrainer] . "\t left",
            $r[emailTrainer] . "\t left",
            $statusTrainer . "\t center");
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
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
    $name = $data[2];
    $position = $data[3];
    $expertise = $data[4];
    $register_number = $data[5];
    $address = $data[6];
    $province_id = $data[7];
    $city_id = $data[8];
    $telephone = $data[9];
    $phone = $data[10];
    $email = $data[11];
    $type = $data[12];
    $skill = $data[13];
    $history = $data[14];
    $education = $data[15];

    $vendor_id = getField("SELECT `kodeVendor` FROM `dta_vendor` WHERE `nomorVendor` = '$vendor_number'");

    $sql = "INSERT INTO `dta_trainer` SET
        `idVendor` = '$vendor_id',
        `idPropinsi` = '$province_id',
        `idKota` = '$city_id',
        `idTipe` = '$type',
        `namaTrainer` = '$name',
        `jabatanTrainer` = '$position',
        `expertise_trainer` = '$expertise',
        `noregister_trainer` = '$register_number',
        `alamatTrainer` = '$address',
        `teleponTrainer` = '$telephone',
        `handphoneTrainer` = '$phone',
        `emailTrainer` = '$email',
        `keahlianTrainer` = '$skill',
        `kerjaTrainer` = '$history',
        `pendidikanTrainer` = '$education',
        `keteranganTrainer` = 'upload',
        `statusTrainer` = 't',
        `createTime` = '". date('Y-m-d H:i:s') ."',
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
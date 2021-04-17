<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$path = "files/penilaian/setting/konversi/";

$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "sync":
            isset($_submit) ? insertSync() : formSync();
            break;

        case "add":
            if (isset($menuAccess[$s]['add']))
                isset($_submit) ? insert() : form();
            else
                echo "Tidak ada akses";
            break;

        case "edit":
            if (isset($menuAccess[$s]['edit']))
                isset($_submit) ? update() : form();
            else
                echo "Tidak ada akses";
            break;

        case "del":
            if (isset($menuAccess[$s]['delete']))
                delete();
            else
                index();
            break;

        case "remove":
            remove();
            break;

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $menuAccess, $par, $json, $arr_status_image;

    $par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory`= 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");

    $never_sync = getField("SELECT COUNT(*) FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[period]'") == 0;

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[period]'");

        while ($r = mysql_fetch_assoc($res)) {

            $r['nilaiMin'] = (strlen($r['nilaiMin']) == 1) ? $r['nilaiMin'] . ".00" : $r['nilaiMin'];
            $r['nilaiMax'] = (strlen($r['nilaiMax']) == 1) ? $r['nilaiMax'] . ".00" : $r['nilaiMax'];

            $r['nilai'] = $r['nilaiMin'] . " - " . $r['nilaiMax'];

            $r['wom'] = "<div style='background-color: $r[warnaKonversi]'>&emsp;</div>";
            $r['status'] = $arr_status_image[$r['statusKonversi']];

            if (isset($menuAccess[$s]['edit'])) :
                $r['control'] .= "<a class='edit' title='Ubah Data' onclick='openBox(`popup.php?" . getPar($par, "mode") . "&par[mode]=edit&par[idKonversi]=$r[idKonversi]`, 700, 500)'><span>Edit</span></a>";
            endif;

            if (isset($menuAccess[$s]['delete'])) :
                $r['control'] .= "<a class='delete' title='Hapus Data' href='?" . getPar($par, "mode") . "&par[mode]=del&par[idKonversi]=$r[idKonversi]' onclick='return confirm(`Konfirmasi hapus data`);'><span>Delete</span></a>";
            endif;

            $ret[] = $r;
        }

        echo json_encode([
            "sEcho" => 1,
            "aaData" => $ret
        ]);
        exit();
    }
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="100">Nilai</th>
                <th width="50">Kode</th>
                <th>Ppredikat Yudicium</th>
                <th width="50">WOM</th>
                <th width="50">Status</th>
                <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    <th width="50" style="vertical-align: middle">Kontrol</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">

        jQuery(document).ready(function () {

            ot = jQuery('#datatable').dataTable({
                "sScrollY": "100%",
                "bSort": true,
                "bFilter": true,
                "iDisplayStart": 0,
                "sPaginationType": "full_numbers",
                "sAjaxSource": "ajax.php?<?= getPar($par) ?>&json=1",
                "aoColumns": [
                    {"mData": null, "bSortable": false, "sClass": "alignCenter"},
                    {"mData": "nilai", "bSortable": true, "sClass": "alignCenter"},
                    {"mData": "uraianKonversi", "bSortable": true, "sClass": "alignCenter"},
                    {"mData": "penjelasanKonversi", "bSortable": false},
                    {"mData": "wom", "bSortable": false, "sClass": "alignCenter"},
                    {"mData": "status", "bSortable": false, "sClass": "alignCenter"},
                    <?php if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    {"mData": "control", "bSortable": false, "sClass": "alignCenter"},
                    <?php endif; ?>
                ],
                "aaSorting": [[0, "asc"]],
                "fnInitComplete": function (oSettings) {
                    oSettings.oLanguage.sZeroRecords = "No data available"
                }, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    jQuery("td:first", nRow).html((iDisplayIndexFull + 1))
                    return nRow
                },
                "bProcessing": true,
                "oLanguage": {
                    "sProcessing": "<img src=\"<?= APP_URL ?>/styles/images/loader.gif\" />"
                }
            })

            jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px")
            jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px")

            jQuery("#datatable_wrapper #datatable_filter")
                .append(`<?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "period", "", $par['period'], "", "200px", ""); ?>`)

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if (isset($menuAccess[$s]['add'])) : ?>
            <?php if ($never_sync) : ?>
            jQuery("#datatable_wrapper #right_panel").append(`<a class='btn btn1 btn_inboxi' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync', 700, 270)"><span>Ambil Data</span></a>`)
            <?php endif; ?>
            jQuery("#datatable_wrapper #right_panel").append(`&nbsp;<a class='btn btn1 btn_document' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add', 700, 500)"><span>Tambah Data</span></a>`)
            <?php endif; ?>

            jQuery("#period").change(function () {
                window.location = '?<?= getPar($par, "period") ?>&par[period]=' + jQuery(this).val()
            })

        })

    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_status;

    $res = db("SELECT * FROM `pen_setting_konversi` WHERE `idKonversi` = '$par[idKonversi]'");
    $r = mysql_fetch_assoc($res);

    $r['idPeriode'] = $r['idPeriode'] ?: $par['period'];

    $r['statusKonversi'] = $r['statusKonversi'] ?: "t";
    $r['warnaKonversi'] = $r['warnaKonversi'] ?: "#000000";

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <div style="margin-top: 10px">
            <?= getBread(ucwords($par['mode'] . " data")) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return validation(document.form);" enctype="multipart/form-data">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Konversi&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Periode</label>
                <div class="field">
                    <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `kodeData` ASC", "kodeData", "namaData", "inp[idPeriode]", "", $r['idPeriode'], "", "220px"); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Nilai</label>
                <div class="field">
                    <input type="text" name="inp[nilaiMin]" id="inp[nilaiMin]" class="mediuminput" value="<?= $r['nilaiMin'] ?>" style="width: 70px; text-align: right;" placeholder="( min )"/> &nbsp;
                    s/d &nbsp; <input type="text" name="inp[nilaiMax]" id="inp[nilaiMax]" class="mediuminput" value="<?= $r[nilaiMax] ?>" style="width: 70px; text-align: right;"
                                      placeholder="( max )"/>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Kode</label>
                <div class="field">
                    <input type="text" name="inp[uraianKonversi]" id="inp[uraianKonversi]" class="mediuminput" value="<?= $r['uraianKonversi'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Predikat Yudicium</label>
                <div class="field">
                    <textarea name="inp[penjelasanKonversi]" id="inp[penjelasanKonversi]" class="mediuminput" style="height: 50px"><?= $r['penjelasanKonversi'] ?></textarea>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Warna</label>
                <div class="field">
                    <input type="text" name="inp[warnaKonversi]" id="isiWarna" class="width100" value="<?= $r['warnaKonversi'] ?>" readonly/>
                    <span id="colorSelector" class="colorselector">
                        <span></span>
                    </span>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Status</label>
                <div class="field fradio">
                    <?php foreach ($arr_status as $key => $value) : $checked = $r['statusKonversi'] == $key ? "checked" : ""; ?>
                        <input type="radio" <?= $checked ?> name="inp[statusKonversi]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach; ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">File SK</label>
                <div class="field">
                    <?php if (empty($r['skKonversi'])) : ?>
                        <input type="file" name="file" style="margin-top: 5px;"/>
                    <?php else : ?>
                        <a href="download.php?d=pen_setting_konversi&f=<?= $r['skKonversi'] ?>" target="_blank" title="Download File">
                            <img style="height: 20px;" src="<?= getIcon($r['skKonversi']) ?>">
                        </a>
                        &nbsp;
                        <a href="?<?= getPar($par, "mode"); ?>&par[mode]=remove" onclick="return confirm('Konfirmasi hapus file')">Delete</a>
                    <?php endif; ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>

    <script type="text/javascript">

        function warna(hex) {
            jQuery('#colorSelector span').css('backgroundColor', hex)
        }

        jQuery(document).ready(function ($) {
            warna('<?= $r['warnaKonversi'] ?>')
        })
    </script>
    <?php

}

function formSync()
{
    global $par;

    ?>

    <div class="pageheader">
        <h1 class="pagetitle">Ambil Data</h1>
        <div style="margin-top: 10px">
            <?= getBread($par['mode']) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Ambil Data&emsp;</legend>

                <p style="position: absolute; top: .5rem; right: 1rem;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Konversi Baru</label>
                    <span class="field">
                        &nbsp;
                        <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[period]'"); ?>
                    </span>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Ambil dari</label>
                <div class="field">
                    <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' AND `kodeData` != '$par[period]' ORDER BY `kodeData` ASC", "kodeData", "namaData", "inp[from]", "", "", "", "300px"); ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>
    <?php
}

function remove()
{
    global $par, $path;

    $file = getField("SELECT `skKonversi` FROM `pen_setting_konversi` WHERE `idKode` = '$par[idKonversi]'");

    if (!empty($file) && file_exists($path . $file))
        unlink($path . $file);

    db("UPDATE `pen_setting_konversi` SET `skKonversi` = '' WHERE `idKonversi` = '$par[idKonversi]'");

    echo "<script>window.location='?" . getPar($par, "mode") . "&par[mode]=edit';</script>";
}

function insert()
{
    global $inp, $par, $path, $cUsername;

    repField();

    $nextId = getField("SELECT `idKonversi` FROM `pen_setting_konversi` ORDER BY `idKonversi` DESC LIMIT 1") + 1;

    $file = customeUploadFile($_FILES['file'], date($nextId . '-Ymd-His'), $path, "");

    $sql = "INSERT INTO `pen_setting_konversi` SET

    `idKonversi` = '$nextId',
    `idPeriode` = '$inp[idPeriode]',
    `nilaiMin` = '$inp[nilaiMin]',
    `nilaiMax` = '$inp[nilaiMax]',
    `uraianKonversi` = '$inp[uraianKonversi]',
    `penjelasanKonversi` = '$inp[penjelasanKonversi]',
    `warnaKonversi` = '$inp[warnaKonversi]',
    `skKonversi` = '$file',
    `statusKonversi` = '$inp[statusKonversi]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idKonversi") . "';</script>";
}

function insertSync()
{
    global $inp, $par, $cUsername;

    repField();

    $res = db("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$inp[from]'");

    while ($row = mysql_fetch_assoc($res)) {

        $nextId = getField("SELECT `idKonversi` FROM `pen_setting_konversi` ORDER BY `idKonversi` DESC LIMIT 1") + 1;

        $sql = "INSERT INTO `pen_setting_konversi` SET 

        `idKonversi` = '$nextId',
        `idPeriode` = '$par[period]',
        `nilaiMin` = '$row[nilaiMin]',
        `nilaiMax` = '$row[nilaiMax]',
        `uraianKonversi` = '$row[uraianKonversi]',
        `penjelasanKonversi` = '$row[penjelasanKonversi]',
        `warnaKonversi` = '$row[warnaKonversi]',
        `skKonversi` = '$row[skKonversi]',
        `statusKonversi` = '$row[statusKonversi]',
        `createDate` = '" . date("Y-m-d H:i:s") . "',
        `createBy` = '$cUsername'
        ";

        db($sql);

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "';</script>";
}

function update()
{
    global $inp, $par, $cUsername, $path;

    repField();

    $last_file = getField("SELECT `skKonversi` FROM `pen_setting_konversi` WHERE `idKonversi` = '$par[idKonversi]'");

    $file = customeUploadFile($_FILES['file'], date($par['idKonversi'] . '-Ymd-His'), $path, $last_file);

    $sql = "UPDATE `pen_setting_konversi` SET 

    `idPeriode` = '$inp[idPeriode]',
    `nilaiMin` = '$inp[nilaiMin]',
    `nilaiMax` = '$inp[nilaiMax]',
    `uraianKonversi` = '$inp[uraianKonversi]',
    `penjelasanKonversi` = '$inp[penjelasanKonversi]',
    `warnaKonversi` = '$inp[warnaKonversi]',
    `skKonversi` = '$file',
    `statusKonversi` = '$inp[statusKonversi]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'
     
     WHERE
      
    `idKonversi` = '$par[idKonversi]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idKonversi") . "';</script>";
}

function delete()
{
    global $par, $path;

    $file = getField("SELECT `skKonversi` FROM `pen_setting_konversi` WHERE `idKonversi` = '$par[idKonversi]'");

    if (!empty($file) && file_exists($path . $file))
        unlink($path . $file);

    db("DELETE FROM `pen_setting_konversi` WHERE `idKonversi` = '$par[idKonversi]'");

    echo "<script>window.location='?" . getPar($par, "mode, idKonversi") . "';</script>";
}

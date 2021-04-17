<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$path = "files/penilaian/setting/kode/";

$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "check":
            check();
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

    $par['type'] = $par['type'] ?: getField("SELECT `kodeTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC LIMIT 1");

    if ($json == 1) {

        header("Content-type: application/json");

        $q_filter = "";
        $q_filter .= "AND `kodeTipe` = '$par[type]'";

        $ret = [];
        $res = db("SELECT * FROM `pen_setting_kode` WHERE 1 = 1 $q_filter");

        while ($r = mysql_fetch_assoc($res)) {

            $r['status'] = $arr_status_image[$r['statusKode']];

            if (isset($menuAccess[$s]['edit'])) :
                $r['control'] .= "<a href='?" . getPar($par, "mode") . "&par[mode]=edit&par[idKode]=$r[idKode]' class='edit' title='Ubah Data'><span>Edit</span></a>";
            endif;

            if (isset($menuAccess[$s]['delete'])) :
                $r['control'] .= "<a class='delete' title='Hapus Data' onclick='return del(`$r[idKode]`);'><span>Hapus</span></a>";
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
                <th width="150">Kode</th>
                <th>Penilaian</th>
                <th width="20">Status</th>
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
                    {"mData": null, "sClass": "alignCenter", "bSortable": false},
                    {"mData": "namaKode", "bSortable": true},
                    {"mData": "subKode", "bSortable": true},
                    {"mData": "status", "sClass": "alignCenter", "bSortable": false},
                    <?php if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    {"mData": "control", "sClass": "alignCenter", "bSortable": false},
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
                .append(`<?= comboData("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC", "kodeTipe", "namaTipe", "type", "", $par['type'], "", "200px", ""); ?>`)

            jQuery("#datatable_wrapper .top")
                .append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if(isset($menuAccess[$s]['add'])) : ?>
            jQuery("#datatable_wrapper #right_panel")
                .append(`<a href='?<?= getPar($par, 'mode') ?>&par[mode]=add' class='btn btn1 btn_document'><span>Tambah Data</span></a>`)
            <?php endif; ?>

            jQuery("#type").change(function () {
                window.location = '?<?= getPar($par, "type") ?>&par[type]=' + jQuery(this).val()
            })

        })

        function del(idKode) {

            jQuery.ajax({
                url: 'ajax.php?<?= getPar($par, "mode") ?>&par[mode]=check&par[idKode]=' + idKode,
                type: 'GET',
                dataType: 'text'
            }).done(function (response) {

                if (response) {
                    alert(response)
                    return
                }

                if (confirm('Konfirmasi hapus data')) {
                    window.location = "?<?= getPar($par, "mode") ?>&par[mode]=del&par[idKode]=" + idKode
                }

            })

            return false
        }

    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_status;

    $res = db("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");
    $r = mysql_fetch_assoc($res);

    $r['kodeTipe'] = $r['kodeTipe'] ?: $par['type'];
    $r['statusTipe'] = $r['statusTipe'] ?: "t";

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

                <legend>&emsp;Setting&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                    <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, "mode, idKode") ?>';"/>
                </p>

                <p>
                    <label class="l-input-small">Tipe Penilaian</label>
                <div class="field">
                    <?= comboData("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe`", "kodeTipe", "namaTipe", "inp[kodeTipe]", "", $r['kodeTipe'], "", "220px"); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Sub</label>
                <div class="field">
                    <input type="text" name="inp[subKode]" class="mediuminput" id="inp[subKode]" value="<?= $r['subKode'] ?>" style="width: 375px;">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Kode</label>
                <div class="field">
                    <input type="text" name="inp[namaKode]" id="inp[namaKode]" class="mediuminput" value="<?= $r['namaKode'] ?>" style="width: 375px;">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Keterangan</label>
                <div class="field">
                    <textarea name="inp[keteranganKode]" id="inp[keteranganKode]" class="mediuminput" style="width: 375px; height: 50px"><?= $r['keteranganKode'] ?></textarea>
                </div>
                </p>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Status</label>
                        <div class="field fradio">
                            <?php foreach ($arr_status as $key => $value) : $checked = $r['statusTipe'] == $key ? "checked" : ""; ?>
                                <input type="radio" <?= $checked ?> name="inp[statusKode]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small">Kode</label>
                        <div class="field">
                            <input type="text" id="inp[kodeKode]" name="inp[kodeKode]" style="width: 100px" class="mediuminput" value="<?= $r[kodeKode] ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Berlaku Sejak</label>
                        <div class="field">
                            <input type="text" id="inp[tanggalMulai]" name="inp[tanggalMulai]" style="width: 100px" class="mediuminput hasDatePicker" value="<?= getTanggal($r[tanggalMulai]) ?>">
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small">Berakhir</label>
                        <div class="field">
                            <input type="text" id="inp[tanggalSelesai]" name="inp[tanggalSelesai]" style="width: 100px" class="mediuminput hasDatePicker"
                                   value="<?= getTanggal($r[tanggalSelesai]) ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <p>
                    <label class="l-input-small">File SK</label>
                <div class="field">
                    <?php if (empty($r['skKode'])) : ?>
                        <input type="file" name="file" style="margin-top: 5px;"/>
                    <?php else : ?>
                        <a href="#Preview" title="Preview File" onclick="openBox('view.php?&par[tipe]=file_pen_kode&par[idKode]=<?= $r['idKode'] ?>',900,500);">
                            <img style="height: 20px;" src="<?= getIcon($r['skKode']) ?>">
                        </a>
                        &nbsp;
                        <a href="?<?= getPar($par, "mode"); ?>&par[mode]=remove" onclick="return confirm('Konfirmasi hapus file')">Delete</a>
                    <?php endif; ?>
                </div>
                </p>

            </fieldset>

            <br>

            <fieldset style="display: none; padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Acuan Penilaian&emsp;</legend>

                <p>
                    <label class="l-input-small">Konversi Nilai</label>
                <div class="field">
                    <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN03' AND statusData='t'", "kodeData", "namaData", "inp[kodeKonversi]", " ", $r['kodeKonversi'], "", "275px"); ?>
                </div>
                </p>
                <p>
                    <label class="l-input-small">Aspek Penilaian</label>
                <div class="field">
                    <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN04' AND statusData='t'", "kodeData", "namaData", "inp[kodeAspek]", " ", $r['kodeAspek'], "", "275px"); ?>
                </div>
                </p>
                <p>
                    <label class="l-input-small">Prespektif</label>
                <div class="field">
                    <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'PN05' AND statusData='t'", "kodeData", "namaData", "inp[kodePrespektif]", " ", $r['kodePrespektif'], "", "275px"); ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>
    <?php

}

function check()
{
    global $par;

    $resKode = getField("SELECT CONCAT(`kodeKonversi`, '~', `kodeAspek`, '~', `kodePrespektif`) FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");

    list($kodeKonversi, $kodeAspek, $kodePrespektif) = explode("~", $resKode);

    $resKonversi = getField("SELECT COUNT(*) FROM `pen_setting_konversi` WHERE `kodeKonversi` = '$kodeKonversi'");
    $resAspek = getField("SELECT COUNT(*) FROM `pen_setting_aspek` WHERE `kodeAspek` = '$kodeAspek'");
    $resPrespektif = getField("SELECT COUNT(*) FROM `pen_setting_prespektif` WHERE `kodePrespektif` = '$kodePrespektif'");

    if ($resKonversi > 0 || $resAspek > 0 || $resPrespektif > 0) {
        echo "sorry, data has been used";
    }

}

function remove()
{
    global $par, $path;

    $file = getField("SELECT `skKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");

    if (!empty($file) && file_exists($path . $file))
        unlink($path . $file);

    db("UPDATE `pen_setting_kode` SET `skKode` = '' WHERE `idKode` = '$par[idKode]'");

    echo "<script>window.location='?" . getPar($par, "mode") . "&par[mode]=edit';</script>";
}

function insert()
{
    global $inp, $par, $path, $cUsername;

    repField();

    $nextId = getField("SELECT `idKode` FROM `pen_setting_kode` ORDER BY `idKode` DESC LIMIT 1") + 1;

    $file = customeUploadFile($_FILES['file'], date($nextId . '-Ymd-His'), $path, "");

    $sql = "INSERT INTO `pen_setting_kode` SET

    `idKode` = '$nextId',
    `namaKode` = '$inp[namaKode]',
    `subKode` = '$inp[subKode]',
    `keteranganKode` = '$inp[keteranganKode]',
    `kodeKode` = '$inp[kodeKode]',
    `kodeTipe` = '$inp[kodeTipe]',
    `tanggalMulai` = '" . setTanggal($inp['tanggalMulai']) . "',
    `tanggalSelesai` = '" . setTanggal($inp['tanggalSelesai']) . "',
    `skKode` = '$file',
    `statusKode` = '$inp[statusKode]',
    `kodeKonversi` = '$inp[kodeKonversi]',
    `kodeAspek` = '$inp[kodeAspek]',
    `kodePrespektif` = '$inp[kodePrespektif]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>window.location='?" . getPar($par, "mode, idKode") . "';</script>";
}

function update()
{
    global $inp, $par, $cUsername, $path;

    repField();

    $last_file = getField("SELECT `skKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");

    $file = customeUploadFile($_FILES['file'], date($par['idKode'] . '-Ymd-His'), $path, $last_file);

    $sql = "UPDATE `pen_setting_kode` SET 

    `namaKode` = '$inp[namaKode]',
    `subKode` = '$inp[subKode]',
    `keteranganKode` = '$inp[keteranganKode]',
    `kodeKode` = '$inp[kodeKode]',
    `kodeTipe` = '$inp[kodeTipe]',
    `tanggalMulai` = '" . setTanggal($inp['tanggalMulai']) . "',
    `tanggalSelesai` = '" . setTanggal($inp['tanggalSelesai']) . "',
    `skKode` = '$file',
    `statusKode` = '$inp[statusKode]',
    `kodeKonversi` = '$inp[kodeKonversi]',
    `kodeAspek` = '$inp[kodeAspek]',
    `kodePrespektif` = '$inp[kodePrespektif]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'
     
     WHERE
      
    `idKode` = '$par[idKode]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>window.location='?" . getPar($par, "mode, idKode") . "';</script>";
}

function delete()
{
    global $par, $path;

    $file = getField("SELECT `skKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");

    if (!empty($file) && file_exists($path . $file))
        unlink($path . $file);

    db("DELETE FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");

    echo "<script>window.location='?" . getPar($par, "mode, idKode") . "';</script>";
}

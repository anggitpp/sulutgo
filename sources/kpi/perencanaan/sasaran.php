<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "sync":
            isset($_submit) ? insertSync() : formSync();
            break;

        case "target":
            isset($_submit) ? insertTarget() : formTarget();
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

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $menuAccess, $par, $json, $arr_status_image;

    $par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory`= 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");

    $never_sync = getField("SELECT COUNT(*) FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]'") == 0;

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");

        while ($r = mysql_fetch_assoc($res)) {

            $r['bobotAspek'] = $r['bobotAspek'] . "%";
            $r['targetAspek'] = "<a onclick='openBox(`popup.php?" . getPar($par, "mode, kodeAspek") . "&par[mode]=target&par[kodeAspek]=$r[kodeAspek]&par[idAspek]=$r[idAspek]`, 800, 500);'>$r[targetAspek]</a>";

            $r['status'] = $arr_status_image[$r['statusAspek']];

            if (isset($menuAccess[$s]['edit'])) :
                $r['control'] .= "<a class='edit' title='Edit Data' onclick='openBox(`popup.php?" . getPar($par, "mode") . "&par[mode]=edit&par[idAspek]=$r[idAspek]`, 700, 500)'><span>Edit</span></a>";
            endif;

            if (isset($menuAccess[$s]['delete'])) :
                $r['control'] .= "<a class='delete' title='Hapus Data' href='?" . getPar($par, "mode") . "&par[mode]=del&par[idAspek]=$r[idAspek]' onclick='return confirm(`Konfirmasi hapus data`);'><span>Delete</span></a>";
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
                <th>Aspek</th>
                <th>Penilaian</th>
                <th width="50">Bobot</th>
                <th width="50">Urutan</th>
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
                    {"mData": "namaAspek", "bSortable": false},
                    {"mData": "penilaianAspek", "bSortable": false, "sClass": "alignCenter"},
                    {"mData": "bobotAspek", "bSortable": true, "sClass": "alignCenter"},
                    {"mData": "urutanAspek", "bSortable": true, "sClass": "alignCenter"},
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

            jQuery("#datatable_wrapper .top")
                .append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if(isset($menuAccess[$s]['add'])) : ?>
            <?php if ($never_sync && false) : ?>
            jQuery("#datatable_wrapper #right_panel")
                .append(`<a class='btn btn1 btn_inboxi' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync', 700, 270)"><span>Ambil Data</span></a>`)
            <?php endif; ?>
            jQuery("#datatable_wrapper #right_panel")
                .append(`&nbsp;<a class='btn btn1 btn_document' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add', 700, 500)"><span>Tambah Data</span></a>`)
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

    $res = db("SELECT * FROM `pen_setting_aspek` WHERE `idAspek` = '$par[idAspek]'");
    $r = mysql_fetch_assoc($res);

    $r['urutanAspek'] = $r['urutanAspek'] ?: getField("SELECT `urutanAspek` FROM `pen_setting_aspek` ORDER BY `urutanAspek` DESC LIMIT 1") + 1;

    $r['bobotAspek'] = $r['bobotAspek'] ?: 0;
    $r['statusAspek'] = $r['statusAspek'] ?: "t";

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <div style="margin-top: 10px">
            <?= getBread(ucwords($par['mode'] . " data")) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Aspek Penilaian&emsp;</legend>

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
                    <label class="l-input-small">Kode</label>
                <div class="field">
                    <input type="text" name="inp[aspekKode]" id="inp[aspekKode]" class="smallinput" value="<?= $r['aspekKode'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Aspek</label>
                <div class="field">
                    <input type="text" name="inp[namaAspek]" id="inp[namaAspek]" class="mediuminput" value="<?= $r['namaAspek'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Penilaian</label>
                <div class="field">
                    <input type="text" name="inp[penilaianAspek]" id="inp[penilaianAspek]" class="mediuminput" value="<?= $r['penilaianAspek'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">keterangan</label>
                <div class="field">
                    <textarea name="inp[keteranganAspek]" id="inp[keteranganAspek]" class="mediuminput" style="height: 50px"><?= $r['keteranganAspek'] ?></textarea>
                </div>
                </p>

                <div style="display: flex;">
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Bobot</label>
                        <div class="field">
                            <input type="text" name="inp[bobotAspek]" id="inp[bobotAspek]" class="vsmallinput" value="<?= $r['bobotAspek'] ?>"> %
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small">Urutan</label>
                        <div class="field">
                            <input type="text" name="inp[urutanAspek]" id="inp[urutanAspek]" class="vsmallinput" value="<?= $r['urutanAspek'] ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <p>
                    <label class="l-input-small">Status</label>
                <div class="field fradio">
                    <?php foreach ($arr_status as $key => $value) : $checked = $r['statusAspek'] == $key ? "checked" : ""; ?>
                        <input type="radio" <?= $checked ?> name="inp[statusAspek]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach; ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>

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
                    <label class="l-input-small" style="width: 150px">Aspek Baru</label>
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

function formTarget()
{
    global $s, $arrTitle, $par;

    $infoAspek = getField("SELECT CONCAT(`namaAspek`, '~', `penilaianAspek`) FROM `pen_setting_aspek` WHERE `idAspek` = '$par[idAspek]'");
    list($namaAspek, $penilaianAspek) = explode("~", $infoAspek);

    $getKode = getField("SELECT `kodeAspek` FROM `pen_setting_aspek` WHERE `idAspek` = '$par[idAspek]'");
    $kodeTIpe = getField("SELECT kodeTipe FROM `pen_setting_kode` WHERE `idKode` = $getKode");

    $res = db("SELECT t1.`kodeTipe`, t1.`namaTipe`, COALESCE(t2.`nilaiDetail`, 0) AS `nilaiDetail` FROM `pen_tipe` t1 LEFT JOIN `pen_setting_aspek_detail` t2 ON (t2.`kodeTipe` = t1.`kodeTipe` AND t2.`idAspek` = '$par[idAspek]') WHERE t1.`statusTipe` = 't' and t1.`kodeTipe` = '$kodeTIpe' ORDER BY t1.`namaTipe`");
    $no = 0;
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> &raquo; Ambil Data</h1>
        <div style="margin-top: 10px">
            <?= getBread($par['mode']) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return false;">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Aspek Penilaian&emsp;</legend>

                <p style="position: absolute; top: .5rem; right: 1rem;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Aspek</label>
                    <span class="field">
					<?= $namaAspek ?> &nbsp;
				</span>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Penilaian</label>
                    <span class="field">
					<?= nl2br($penilaianAspek) ?> &nbsp;
				</span>
                </p>

            </fieldset>

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Setting Nilai&emsp;</legend>

                <?php while ($r = mysql_fetch_array($res)) : ?>
                    <input type="hidden" id="dtlAspekIds_<?= $no ?>" name="dtlAspekIds[]" value="<?= $no ?>">
                    <input type="hidden" id="dtlAspekKodeTipe_<?= $no ?>" name="dtlAspekKodeTipe[]" value="<?= $r['kodeTipe'] ?>">

                    <p>
                        <label class="l-input-small" style="width: 150px"><?= $r['namaTipe'] ?>  </label>
                    <div class="field">
                        <input type="text" class="mediuminput" id="dtlAspekNilaiDetail_<?= $no ?>" name="dtlAspekNilaiDetail[]" value="<?= $r['nilaiDetail'] ?>" style="width: 70px; text-align: right;"
                               onkeyup="cekAngka(this);">
                    </div>
                    </p>
                <?php endwhile; ?>

            </fieldset>

        </form>
    </div>
    <?php
}

function insert()
{
    global $inp, $par, $cUsername;

    repField();

    $nextId = getField("SELECT `idAspek` FROM `pen_setting_aspek` ORDER BY `idAspek` DESC LIMIT 1") + 1;

    $sql = "INSERT INTO `pen_setting_aspek` SET

    `idAspek` = '$nextId',
    `idPeriode` = '$inp[idPeriode]',
    `namaAspek` = '$inp[namaAspek]',
    `penilaianAspek` = '$inp[penilaianAspek]',
    `keteranganAspek` = '$inp[keteranganAspek]',
    `aspekKode` = '$inp[aspekKode]',
    `urutanAspek` = '$inp[urutanAspek]',
    `targetAspek` = '$inp[targetAspek]',
    `bobotAspek` = '$inp[bobotAspek]',
    `statusAspek` = '$inp[statusAspek]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idAspek") . "';</script>";
}

function insertTarget()
{
    global $inp, $par, $cUsername;

//    repField();
//
//    db("DELETE FROM pen_setting_aspek_detail WHERE idAspek = '$par[idAspek]'");
//
//    $cm = 0;
//
//    $dtlAspekIds = $_POST['dtlAspekIds'];
//
//    foreach ($dtlAspekIds as $dtlAspekId) {
//
//        $dtlAspekKodeTipe = $_POST['dtlAspekKodeTipe'][$cm];
//        $dtlAspekNilaiDetail = $_POST['dtlAspekNilaiDetail'][$cm];
//
//        db("INSERT INTO pen_setting_aspek_detail(idAspek, kodeDetail, kodeTipe, nilaiDetail, createBy, createDate) VALUES ('$par[idAspek]', '" . ($cm + 1) . "', '$dtlAspekKodeTipe', '" . setAngka($dtlAspekNilaiDetail) . "', '$cUsername', '" . date("Y-m-d H:i:s") . "')");
//
//        $cm++;
//    }
//
//    db("UPDATE pen_setting_aspek SET targetAspek=(SELECT AVG(nilaiDetail) FROM pen_setting_aspek_detail WHERE idAspek = '$par[idAspek]') WHERE idAspek = '$par[idAspek]'");
//
//    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idAspek, kodeAspek") . "';</script>";
}

function insertSync()
{
    global $inp, $par, $cUsername;

    repField();

    $res = db("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$inp[from]'");

    while ($row = mysql_fetch_assoc($res)) {

        $nextId = getField("SELECT `idAspek` FROM `pen_setting_aspek` ORDER BY `idAspek` DESC LIMIT 1") + 1;

        $sql = "INSERT INTO `pen_setting_aspek` SET

        `idAspek` = '$nextId',
        `idPeriode` = '$par[period]',
        `namaAspek` = '$row[namaAspek]',
        `penilaianAspek` = '$row[penilaianAspek]',
        `keteranganAspek` = '$row[keteranganAspek]',
        `aspekKode` = '$row[aspekKode]',
        `urutanAspek` = '$row[urutanAspek]',
        `targetAspek` = '$row[targetAspek]',
        `bobotAspek` = '$row[bobotAspek]',
        `statusAspek` = '$row[statusAspek]',
        `createDate` = '" . date("Y-m-d H:i:s") . "',
        `createBy` = '$cUsername'
        ";

        db($sql);

    }

    echo "<script>alert('Data berhasil disalin');</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "';</script>";
}

function update()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "UPDATE `pen_setting_aspek` SET

    `idPeriode` = '$inp[idPeriode]',
    `namaAspek` = '$inp[namaAspek]',
    `penilaianAspek` = '$inp[penilaianAspek]',
    `keteranganAspek` = '$inp[keteranganAspek]',
    `aspekKode` = '$inp[aspekKode]',
    `urutanAspek` = '$inp[urutanAspek]',
    `targetAspek` = '$inp[targetAspek]',
    `bobotAspek` = '$inp[bobotAspek]',
    `statusAspek` = '$inp[statusAspek]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'

     WHERE

    `idAspek` = '$par[idAspek]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idAspek") . "';</script>";
}

function delete()
{
    global $par;

    db("DELETE FROM `pen_setting_aspek` WHERE `idAspek` = '$par[idAspek]'");

    echo "<script>window.location='?" . getPar($par, "mode, idAspek") . "';</script>";
}

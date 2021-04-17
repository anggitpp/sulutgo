<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$arr_status = [1 => "Aktif", 0 => "Tidak Aktif"];
$arr_status_image = [
    1 => "<img src='styles/images/t.png' title='Aktif' />",
    0 => "<img src='styles/images/f.png' title='Tidak Aktif' />"
];

$arr_type = [
    "Pertumbuhan",
    "Efisiensi"
];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

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
    global $s, $arrTitle, $menuAccess, $par, $json, $arr_status_image, $arr_type;

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `master_kpi` ORDER BY `urut`");

        while ($r = mysql_fetch_assoc($res)) {

            $r['tipe'] = $arr_type[$r['tipe']];
            $r['status'] = $arr_status_image[$r['status']];

            if (isset($menuAccess[$s]['edit'])) :
                $r['control'] .= "<a class='edit' title='Edit Data' onclick='openBox(`popup.php?" . getPar($par, "mode") . "&par[mode]=edit&par[id]=$r[id]`, 700, 380)'><span>Edit</span></a>";
            endif;

            if (isset($menuAccess[$s]['delete'])) :
                $r['control'] .= "<a class='delete' title='Hapus Data' href='?" . getPar($par, "mode") . "&par[mode]=del&par[id]=$r[id]' onclick='return confirm(`Konfirmasi hapus data`);'><span>Delete</span></a>";
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
                <th>Komponen</th>
                <th width="150">Jenis Sasaran</th>
                <th width="50">Urut</th>
                <th width="50">Status</th>
                <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    <th width="80" style="vertical-align: middle">Kontrol</th>
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
                    {"mData": "komponen", "bSortable": false},
                    {"mData": "tipe", "bSortable": false},
                    {"mData": "urut", "bSortable": true, "sClass": "alignCenter"},
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

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if(isset($menuAccess[$s]['add'])) : ?>
            jQuery("#datatable_wrapper #right_panel")
                .append(`&nbsp;<a class='btn btn1 btn_document' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add', 700, 380)"><span>Tambah Data</span></a>`)
            <?php endif; ?>

        })

    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_status, $arr_type;

    $res = db("SELECT * FROM `master_kpi` WHERE `id` = '$par[id]'");
    $r = mysql_fetch_assoc($res);

    $r['urut'] = $r['urut'] ?: getField("SELECT `urut` FROM `master_kpi` ORDER BY `urut` DESC LIMIT 1") + 1;
    $r['status'] = isset($r['status']) ? $r['status'] : 1;

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

                <legend>&emsp;Komponen&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Komponen</label>
                <div class="field">
                    <input type="text" name="inp[komponen]" id="inp[komponen]" class="mediuminput" value="<?= $r['komponen'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">tipe</label>
                <div class="field">
                    <?= comboKey("inp[tipe]", $arr_type, $r['tipe'], "", "63%"); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Keterangan</label>
                <div class="field">
                    <textarea id="inp[keterangan]" name="inp[keterangan]" class="mediuminput" style="width:350px; height: 50px"><?= $r['keterangan'] ?></textarea>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Urut</label>
                <div class="field">
                    <input type="text" name="inp[urut]" id="inp[urut]" class="vsmallinput" value="<?= $r['urut'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Status</label>
                <div class="field fradio">
                    <?php foreach ($arr_status as $key => $value) : $checked = $r['status'] == $key ? "checked" : ""; ?>
                        <input type="radio" <?= $checked ?> name="inp[status]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach; ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>

    <?php
}

function insert()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "INSERT INTO `master_kpi` SET

    `komponen` = '$inp[komponen]',
    `tipe` = '$inp[tipe]',
    `keterangan` = '$inp[keterangan]',
    `urut` = '$inp[urut]',
    `status` = '$inp[status]',
    `created_at` = '" . date("Y-m-d H:i:s") . "',
    `created_by` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function update()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "UPDATE `master_kpi` SET

    `komponen` = '$inp[komponen]',
    `tipe` = '$inp[tipe]',
    `keterangan` = '$inp[keterangan]',
    `urut` = '$inp[urut]',
    `status` = '$inp[status]',
    `updated_at` ='" . date('Y-m-d H:i:s') . "',
    `updated_by` = '$cUsername'

     WHERE

    `id` = '$par[id]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function delete()
{
    global $par;

    db("DELETE FROM `master_kpi` WHERE `id` = '$par[id]'");

    echo "<script>window.location='?" . getPar($par, "mode, id") . "';</script>";
}

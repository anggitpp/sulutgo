<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

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
    global $s, $arrTitle, $menuAccess, $par, $json, $arr_status_image;

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `pen_tipe` ORDER BY `urutanTipe`");

        while ($r = mysql_fetch_assoc($res)) {

            $r['status'] = $arr_status_image[$r['statusTipe']];

            if (isset($menuAccess[$s]['edit'])) :
                $r['control'] .= "<a href='?" . getPar($par, "mode") . "&par[mode]=edit&par[kodeTipe]=$r[kodeTipe]' class='edit' title='Edit Data'><span>Edit Data</span></a>";
            endif;

            if (isset($menuAccess[$s]['delete'])) :
                $r['control'] .= "<a href='?" . getPar($par, "mode") . "&par[mode]=del&par[kodeTipe]=$r[kodeTipe]' class='delete' title='Delete Data' onclick='return confirm(`Konfirmasi hapus data`);'><span>Hapus Data</span></a>";
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
                <th>Tipe</th>
                <th width="200">Atasa</th>
                <th width="200">Bawahan</th>
                <th width="50">Urutan</th>
                <th width="20">Status</th>
                <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    <th width="50">Kontrol</th>
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
                    {"mData": "namaTipe", "bSortable": true},
                    {"mData": "atasanTipe", "bSortable": false},
                    {"mData": "bawahanTipe", "bSortable": false},
                    {"mData": "urutanTipe", "sClass": "alignCenter", "bSortable": true},
                    {"mData": "status", "sClass": "alignCenter", "bSortable": false},
                    <?php if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    {"mData": "control", "sClass": "alignCenter", "bSortable": false},
                    <?php endif; ?>
                ],
                "aaSorting": [[0, "asc"]],
                "fnInitComplete": function (oSettings) {
                    oSettings.oLanguage.sZeroRecords = "No data available";
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

            jQuery("#datatable_wrapper .top").append("<div id='right_panel' class='dataTables_filter' style='float: right; top: 0px; right: 0px'></div>")

            <?php if(isset($menuAccess[$s]['add'])) : ?>
            jQuery("#datatable_wrapper #right_panel").append("<a href='?<?= getPar($par, 'mode') ?>&par[mode]=add' class='btn btn1 btn_document'><span>Tambah Data</span></a>")
            <?php endif; ?>

        });
    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_status;

    $res = db("SELECT * FROM `pen_tipe` WHERE `kodeTipe` = '$par[kodeTipe]'");
    $r = mysql_fetch_assoc($res);

    $r['statusTipe'] = $r['statusTipe'] ?: 't';
    $r['urutanTipe'] = $r['urutanTipe'] ?: getField("SELECT `urutanTipe` FROM `pen_tipe` ORDER BY `urutanTipe` DESC LIMIT 1") + 1;

    setValidation("is_null", "inp[namaTipe]", "anda harus mengisi tipe");
    echo getValidation();

    ?>

    <div class="contentpopup" style="margin-left: 0px">

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

                    <legend>&emsp; PENILAIAN &emsp;</legend>

                    <p style="position: absolute; top:10px; right: 20px;">
                        <input type="submit" class="submit radius2" value="Simpan"/>
                        <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, "mode, kodeTipe") ?>';"/>
                    </p>

                    <p>
                        <label class="l-input-small">Tipe</label>
                    <div class="field">
                        <input type="text" id="inp[namaTipe]" name="inp[namaTipe]" value="<?= $r['namaTipe'] ?>" class="mediuminput" style="width:350px;"/>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Keterangan</label>
                    <div class="field">
                        <textarea id="inp[keteranganTipe]" name="inp[keteranganTipe]" class="mediuminput" style="width:350px; height: 50px"><?= $r['keteranganTipe'] ?></textarea>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Atasan</label>
                    <div class="field">
                        <input type="text" id="inp[atasanTipe]" name="inp[atasanTipe]" value="<?= $r['atasanTipe'] ?>" class="mediuminput" style="width:350px;"/>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Bawahan</label>
                    <div class="field">
                        <input type="text" id="inp[bawahanTipe]" name="inp[bawahanTipe]" value="<?= $r['bawahanTipe'] ?>" class="mediuminput" style="width:350px;"/>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Status</label>
                    <div class="field fradio">
                        <?php foreach ($arr_status as $key => $value) : $checked = $r['statusTipe'] == $key ? "checked" : ""; ?>
                            <input type="radio" <?= $checked ?> name="inp[statusTipe]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endforeach; ?>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Urutan</label>
                    <div class="field">
                        <input type="text" id="inp[urutanTipe]" name="inp[urutanTipe]" value="<?= $r['urutanTipe'] ?>" class="mediuminput" style="text-align: right; width:70px;"
                               onkeyup="cekAngka(this);"/>
                    </div>
                    </p>

                </fieldset>

                <br>

                <ul class="hornav" style="margin:10px 0px !important;">
                    <li class="current"><a href="#fungsi">Fungsi</a></li>
                    <li><a href="#tugas">Tugas</a></li>
                    <li><a href="#kinerja">Kinerja</a></li>
                </ul>

                <div class="subcontent" id="fungsi" style="border-radius:0; display: block;">
                    <textarea id="inp[fungsiTipe]" name="inp[fungsiTipe]" class="mediuminput" style="width:98%; height: 70px"><?= $r['fungsiTipe'] ?></textarea>
                </div>

                <div class="subcontent" id="tugas" style="border-radius:0; display: none;">
                    <textarea id="inp[tugasTipe]" name="inp[tugasTipe]" class="mediuminput" style="width:98%; height: 70px"><?= $r['tugasTipe'] ?></textarea>
                </div>

                <div class="subcontent" id="kinerja" style="border-radius:0; display: none;">
                    <textarea id="inp[kinerjaTipe]" name="inp[kinerjaTipe]" class="mediuminput" style="width:98%; height: 70px"><?= $r['kinerjaTipe'] ?></textarea>
                </div>

            </form>
        </div>
    </div>
    <?php
}

function insert()
{
    global $inp, $par, $cUsername;

    repField();

    $nextId = getField("SELECT `kodeTipe` FROM `pen_tipe` ORDER BY `kodeTipe` DESC LIMIT 1") + 1;

    $sql = "INSERT INTO `pen_tipe` SET

    `kodeTipe` = '$nextId',
    `namaTipe` = '$inp[namaTipe]',
    `keteranganTipe` = '$inp[keteranganTipe]',
    `atasanTipe` = '$inp[atasanTipe]',
    `bawahanTipe` = '$inp[bawahanTipe]',
    `fungsiTipe` = '$inp[fungsiTipe]',
    `tugasTipe` = '$inp[tugasTipe]',
    `kinerjaTipe` = '$inp[kinerjaTipe]',
    `urutanTipe` = '$inp[urutanTipe]',
    `statusTipe` = '$inp[statusTipe]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>window.location='?" . getPar($par, "mode, kodeTipe") . "';</script>";
}

function update()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "update `pen_tipe` SET 

    `namaTipe` = '$inp[namaTipe]',
    `keteranganTipe` = '$inp[keteranganTipe]',
    `atasanTipe` = '$inp[atasanTipe]',
    `bawahanTipe` = '$inp[bawahanTipe]',
    `fungsiTipe` = '$inp[fungsiTipe]',
    `tugasTipe` = '$inp[tugasTipe]',
    `kinerjaTipe` = '$inp[kinerjaTipe]',
    `urutanTipe` = '$inp[urutanTipe]',
    `statusTipe` = '$inp[statusTipe]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'
     
     WHERE
      
    `kodeTipe` = '$par[kodeTipe]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>window.location='?" . getPar($par, "mode, kodeTipe") . "';</script>";
}

function delete()
{
    global $par;

    db("DELETE FROM `pen_tipe` WHERE `kodeTipe` = '$par[kodeTipe]'");

    echo "<script>window.location = '?" . getPar($par, "mode, kodeTipe") . "';</script>";
}
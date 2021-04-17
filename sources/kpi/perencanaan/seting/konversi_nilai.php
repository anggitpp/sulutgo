<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

use Illuminate\Support\Facades\DB;

$arr_status = [1 => "Aktif", 0 => "Tidak Aktif"];
$arr_status_image = [1 => "<img src='styles/images/t.png' title='Aktif' />", 0 => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

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
                echo "Tidak ada akses";
            break;

        // detail
        case "detail":
            indexDetail();
            break;

        case "add_detail":
            if (isset($menuAccess[$s]['add']))
                isset($_submit) ? insertDetail() : formDetail();
            else
                echo "Tidak ada akses";
            break;

        case "edit_detail":
            if (isset($menuAccess[$s]['edit']))
                isset($_submit) ? updateDetail() : formDetail();
            else
                echo "Tidak ada akses";
            break;

        case "del_detail":
            if (isset($menuAccess[$s]['delete']))
                deleteDetail();
            else
                echo "Tidak ada akses";
            break;

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $menuAccess, $par, $json;

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->orderBy('urutanData', 'DESC')->get();

    $arr_tipe = KPIMasterNilaiTipe::where('periode_id', $par['periode_id'])->get();
    $never_sync = $arr_tipe->count() == 0;

    $par['periode_id'] = $par['periode_id'] ?: $periods[0]['kodeData'];

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];

        foreach ($arr_tipe as $data) {

            $data['control'] .= "<a class='detail' title='Lihat Data' href='?" . getPar($par, "mode, parent_id") . "&par[mode]=detail&par[tipe_id]=$data[id]'><span>View</span></a>";

            if (isset($menuAccess[$s]['edit'])) {
                $data['control'] .= "<a class='edit' title='Ubah Data' onclick='openBox(`popup.php?" . getPar($par, "mode, parent_id") . "&par[mode]=edit&par[id]=$data[id]`, 700, 300)'><span>Edit</span></a>";
            }

            if (isset($menuAccess[$s]['delete'])) {
                $data['control'] .= "<a class='delete' title='Hapus Data' href='?" . getPar($par, "mode, parent_id") . "&par[mode]=del&par[id]=$data[id]' onclick='return confirm(`Konfirmasi hapus tipe`);'><span>Delete</span></a>";
            }

            $ret[] = $data;
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
                <th width="300">Nama</th>
                <th>Keterangan</th>
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
                    {"mData": "nama", "bSortable": true},
                    {"mData": "keterangan", "bSortable": false},
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

            jQuery("#datatable_wrapper #datatable_filter").append(`<?= selectArray("periode_id", "", $periods, "kodeData", "namaData", $par['periode_id'], "", "", "200px", ""); ?>`)

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if (isset($menuAccess[$s]['add'])) : ?>
            <?php if ($never_sync && false) : ?>
            jQuery("#datatable_wrapper #right_panel").append(`<a class='btn btn1 btn_inboxi' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync', 700, 220)"><span>Ambil Data</span></a>`)
            <?php endif; ?>
            jQuery("#datatable_wrapper #right_panel").append(`&nbsp;<a class='btn btn1 btn_document' onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add', 700, 300)"><span>Tambah Data</span></a>`)
            <?php endif; ?>

            jQuery("#periode_id").change(function () {
                window.location = '?<?= getPar($par, "periode_id") ?>&par[periode_id]=' + jQuery(this).val()
            })

        })

    </script>
    <?php
}

function indexDetail()
{
    global $s, $arrTitle, $menuAccess, $par, $json, $arr_status_image;

    $type = KPIMasterNilaiTipe::find($par['tipe_id']);

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->orderBy('urutanData', 'DESC')->get();
    $types = KPIMasterNilaiTipe::where('periode_id', $type['periode_id'])->orderBy('nama')->get();

    $kpi_datas = $type->detil()->orderBy('nilai', 'DESC')->get();
    $never_sync = $kpi_datas->count() == 0;

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];

        foreach ($kpi_datas as $data) {

            $data['range'] = $data['minimal'] . " - " . $data['maksimal'];

            $data['wom'] = "<div style='background-color: $data[warna]'>&emsp;</div>";
            $data['status'] = $arr_status_image[$data['status']];

            if (isset($menuAccess[$s]['edit'])) {
                $data['control'] .= "<a class='edit' title='Ubah Data' onclick='openBox(`popup.php?" . getPar($par, "mode") . "&par[mode]=edit_detail&par[id]=$data[id]`, 700, 450)'><span>Edit</span></a>";
            }

            if (isset($menuAccess[$s]['delete'])) {
                $data['control'] .= "<a class='delete' title='Hapus Data' href='?" . getPar($par, "mode") . "&par[mode]=del_detail&par[id]=$data[id]' onclick='return confirm(`Konfirmasi hapus data`);'><span>Delete</span></a>";
            }

            $ret[] = $data;
        }

        echo json_encode([
            "sEcho" => 1,
            "aaData" => $ret
        ]);
        exit();
    }
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?> - Konversi</h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="50">Nilai</th>
                <th width="50">Kode</th>
                <th>Persentase</th>
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
                    {"mData": "kode", "bSortable": true, "sClass": "alignCenter"},
                    {"mData": "range", "bSortable": true, "sClass": "alignCenter"},
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

            jQuery("#datatable_wrapper #datatable_filter").append(`<?= selectArray("periode_id", "", $periods, "kodeData", "namaData", $type['periode_id'], "", "", "200px", "disabled"); ?>`)
            jQuery("#datatable_wrapper #datatable_filter").append(`<?= selectArray("tipe_id", "", $types, "id", "nama", $type['id'], "", "", "200px", "disabled"); ?>`)

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'></div>`)

            <?php if (isset($menuAccess[$s]['add'])) : ?>
            jQuery("#datatable_wrapper #right_panel").append(`&nbsp;<a class="btn btn1 btn_document" onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add_detail', 700, 450)"><span>Tambah Data</span></a>`)
            <?php endif; ?>

            jQuery("#datatable_wrapper #right_panel").append(`&nbsp;<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, tipe_id") ?>'">`)

            jQuery("#periode_id").change(function () {
                window.location = '?<?= getPar($par, "periode_id") ?>&par[periode_id]=' + jQuery(this).val()
            })

        })

    </script>
    <?php
}

function form()
{
    global $par;

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->orderBy('urutanData', 'DESC')->get();

    $type = KPIMasterNilaiTipe::find($par['id']);

    $type['periode_id'] = $type['periode_id'] ?: $par['periode_id'];

    ?>

    <div class="pageheader">
        <h1 class="pagetitle">Tipe</h1>
        <div style="margin-top: 10px">
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return validation(document.form);" enctype="multipart/form-data">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Periode</label>
                <div class="field">
                    <?= selectArray("inp[periode_id]", "", $periods, "kodeData", "namaData", $type['periode_id'], "", "", "200px", "disabled"); ?>
                </div>
                </p>

                <br>

                <p>
                    <label class="l-input-small">Nama</label>
                <div class="field">
                    <input type="text" name="inp[nama]" id="inp[nama]" class="mediuminput" value="<?= $type['nama'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Keterangan</label>
                <div class="field">
                    <textarea name="inp[keterangan]" id="inp[keterangan]" class="mediuminput" style="height: 50px"><?= $type['keterangan'] ?></textarea>
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

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->where('kodeData', '!=', $par['periode_id'])->orderBy('urutanData', 'DESC')->get();

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Ambil Data</h1>
        <div style="margin-top: 10px">
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
                        <?= Master::find($par['periode_id'])->namaData; ?>
                    </span>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Ambil dari</label>
                <div class="field">
                    <?= selectArray("inp[periode_id]", "", $periods, "kodeData", "namaData", "", "", "", "200px", ""); ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>
    <?php
}

function formDetail()
{
    global $par, $arr_status;

    $values = [0, 10, 20, 30, 40, 50];
    $codes = ["E", "D", "C", "B", "A"];

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->orderBy('urutanData', 'DESC')->get();
    $types = KPIMasterNilaiTipe::where('periode_id', $par['periode_id'])->orderBy('nama')->get();

    $type = KPIMasterNilaiTipe::find($par['tipe_id']);
    $data = $type->detil()->find($par['id']);

    $colors = [
        '#00bd20' => 'Warna 1',
        '#7cd92b' => 'Warna 2',
        '#eeff00' => 'Warna 3',
        '#ffbf00' => 'Warna 4',
        '#ff0000' => 'Warna 5'
    ];

    $nilai = $type->detil()->select('nilai')->latest()->first();
    $nilai = $nilai ? $nilai->nilai + 10 : 0;

    $data['periode_id'] = $type['periode_id'];
    $data['tipe_id'] = $type['id'];

    $data['nilai'] = $data['nilai'] ?: $nilai;
    $data['kode'] = $data['kode'] ?: $codes[$data['nilai'] / 10];
    $data['warna'] = $data['warna'] ?: "#000000";
    $data['status'] = $data['status'] ?: 1;

    ?>
    <style>
        <?php foreach ($colors as $key => $color) : ?>
        .<?= $color ?> { background-color: <?= $key ?> }
        <?php endforeach; ?>
    </style>
    <div class="pageheader">
        <h1 class="pagetitle">Konversi</h1>
        <div style="margin-top: 10px">
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" id="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Konversi&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Periode</label>
                <div class="field">
                    <?= selectArray("inp[periode_id]", "", $periods, "kodeData", "namaData", $data['periode_id'], "", "", "200px", "disabled"); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Tipe</label>
                <div class="field">
                    <?= selectArray("inp[tipe_id]", "", $types, "id", "nama", $data['tipe_id'], "", "", "200px", "disabled"); ?>
                </div>
                </p>

                <br>

                <p>
                    <label class="l-input-small">Nilai</label>
                <div class="field">
                    <?= selectKey("inp[nilai]", "Nilai", $values, $data['nilai'], "", "vsmallinput alignRight", "200px", ""); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Kode</label>
                <div class="field">
                    <?= selectKey("inp[kode]", "Kode", $codes, $data['kode'], "", "vsmallinput", "200px", ""); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Persentase</label>
                <div class="field">
                    <input type="text" name="inp[minimal]" id="inp[minimal]" class="mediuminput" value="<?= $data['minimal'] ?>" style="width: 70px; text-align: right;" placeholder="( min )"/>
                    &nbsp; s/d &nbsp;
                    <input type="text" name="inp[maksimal]" id="inp[maksimal]" class="mediuminput" value="<?= $data['maksimal'] ?>" style="width: 70px; text-align: right;" placeholder="( max )"/>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Warna</label>
                <div class="field" style="display: flex; align-items: center">
                    <?= selectArray("inp[warna]", "Warna", $colors,"", "", $data['warna'], "", "vsmallinput", "100px", "onchange=\"jQuery('#color').css('background-color', this.value)\""); ?>
                    &emsp;
                    <div id="color" style="width: 25px; height: 25px; background-color: <?= $data['warna'] ?>;">&emsp;</div>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Status</label>
                <div class="field fradio">
                    <?php foreach ($arr_status as $key => $value) : $checked = $data['status'] == $key ? "checked" : ""; ?>
                        <input type="radio" <?= $checked ?> name="inp[status]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endforeach; ?>
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
            warna('<?= $data['warna'] ?>')
        })
    </script>
    <?php

}

function insert()
{
    global $par, $inp, $cID;

    repField();

    DB::beginTransaction();

    try {

        KPIMasterNilaiTipe::create([
            'periode_id' => $par['periode_id'],
            'nama' => $inp['nama'],
            'keterangan' => $inp['keterangan'],
            'created_by' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Tipe berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Tipe gagal disimpan');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function insertSync()
{
    global $par, $inp, $cID;

    repField();

    DB::beginTransaction();

    try {

        $types = KPIMasterNilaiTipe::where('periode_id', $inp['periode_id'])->get();

        foreach ($types as $type) {

            $type_create = KPIMasterNilaiTipe::create([
                'periode_id' => $par['periode_id'],
                'nama' => $type['nama'],
                'keterangan' => $type['keterangan'],
                'created_by' => $cID
            ]);

            foreach ($type->detil as $detil) {

                $type_create->detil()->create([
                    'periode_id' => $type_create['periode_id'],
                    'minimal' => $detil['minimal'],
                    'maksimal' => $detil['maksimal'],
                    'warna' => $detil['warna'],
                    'status' => $detil['status'],
                    'created_by' => $cID
                ]);

            }

        }

        DB::commit();

        echo "<script>alert('Data berhasil disingkron');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal disingkron');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function insertDetail()
{
    global $par, $inp, $cID;

    repField();

    DB::beginTransaction();

    try {

        KPIMasterNilai::create([
            'periode_id' => $par['periode_id'],
            'tipe_id' => $par['tipe_id'],
            'nilai' => $inp['nilai'],
            'kode' => $inp['kode'],
            'minimal' => $inp['minimal'],
            'maksimal' => $inp['maksimal'],
            'warna' => $inp['warna'],
            'status' => $inp['status'],
            'created_by' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Data berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        dd($e);

        echo "<script>alert('Data gagal disimpan');</script>";

    }

    $par['mode'] = 'detail';

    echo "<script>parent.window.location='index.php?" . getPar($par, "id") . "';</script>";
}

function update()
{
    global $par, $inp, $cID;

    repField();

    DB::beginTransaction();

    try {

        $data = KPIMasterNilaiTipe::find($par['id']);

        $data->update([
            'periode_id' => $par['periode_id'],
            'nama' => $inp['nama'],
            'keterangan' => $inp['keterangan'],
            'updated_by' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Tipe berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Tipe gagal disimpan');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function updateDetail()
{
    global $par, $inp, $cID;

    repField();

    DB::beginTransaction();

    try {

        $data = KPIMasterNilai::find($par['id']);

        $data->update([
            'periode_id' => $par['periode_id'],
            'tipe_id' => $par['tipe_id'],
            'nilai' => $inp['nilai'],
            'kode' => $inp['kode'],
            'minimal' => $inp['minimal'],
            'maksimal' => $inp['maksimal'],
            'warna' => $inp['warna'],
            'status' => $inp['status'],
            'updated_by' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Data berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal disimpan');</script>";

    }

    $par['mode'] = 'detail';

    echo "<script>parent.window.location='index.php?" . getPar($par, "id") . "';</script>";
}

function delete()
{
    global $par;

    DB::beginTransaction();

    try {

        $data = KPIMasterNilaiTipe::find($par['id']);

        $data->detil()->delete();
        $data->delete();

        DB::commit();

        echo "<script>alert('Tipe berhasil dihapus');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Tipe gagal dihapus');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, id") . "';</script>";
}

function deleteDetail()
{
    global $par;

    DB::beginTransaction();

    try {

        $data = KPIMasterNilai::find($par['id']);

        $data->delete();

        DB::commit();

        echo "<script>alert('Data berhasil dihapus');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal dihapus');</script>";

    }

    $par['mode'] = 'detail';

    echo "<script>parent.window.location='index.php?" . getPar($par, "id") . "';</script>";
}
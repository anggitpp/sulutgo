<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

use Illuminate\Support\Facades\DB;

$arr_routine = ["t" => "Ya", "f" => "Tidak"];
$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "det":
            indexObjective();
            break;

        case "add_objective":
            if (isset($menuAccess[$s]['add']))
                isset($_submit) ? insert() : form();
            else
                echo "Tidak ada akses";
            break;

        case "edit_objective":
            if (isset($menuAccess[$s]['edit']))
                isset($_submit) ? update() : form();
            else
                echo "Tidak ada akses";
            break;

        case "del_objective":
            if (isset($menuAccess[$s]['edit']))
                delete();
            else
                echo "Tidak ada akses";
            break;

        case "type":
            echo type();
            break;

        case "sync":
            isset($_submit) ? insertSync() : formSync();
            break;

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $par, $json, $arr_status_image;

    $par['type'] = $par['type'] ?: getField("SELECT `kodeTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC LIMIT 1");
    $par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory`= 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `pen_setting_kode` WHERE `kodeTipe` = '$par[type]'");

        while ($r = mysql_fetch_assoc($res)) {

            $total = 0;

            foreach ($arr_aspect as $value) {

                $count = getField("SELECT COUNT(*) FROM `pen_sasaran_obyektif` t1 JOIN `pen_setting_prespektif` t2 ON t2.`idPrespektif` = t1.`idPrespektif` WHERE t2.`idKode` = '$r[idKode]' AND t2.`idAspek` = '$value[idAspek]' AND t1.`idPeriode` = '$par[period]'");
                $status = $arr_status_image[$count > 0 ? 't' : 'f'];

                $r[$value['idAspek']] = "<a href='?" . getPar($par, "mode") . "&par[mode]=det&par[idKode]=$r[idKode]&par[tab]=$value[idAspek]'>$status</a>";

                $total += $count;
            }

            $r['total'] = $total;

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
                <th width="20" style="vertical-align: middle;">No.</th>
                <th style="vertical-align: middle;">Posisi</th>
                <th width="100" style="vertical-align: middle;">Kode Penilaian</th>
                <?php foreach ($arr_aspect as $value) : ?>
                    <th width="50" style="vertical-align: middle;"><?= $value['namaAspek'] ?></th>
                <?php endforeach; ?>
                <th width="50" style="vertical-align: middle;">Jumlah</th>
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
                    {"mData": "subKode", "bSortable": false},
                    {"mData": "namaKode", "bSortable": false, "sClass": "alignCenter"},
                    <?php foreach ($arr_aspect as $value) : ?>
                    {"mData": "<?= $value['idAspek'] ?>", "bSortable": false, "sClass": "alignCenter"},
                    <?php endforeach; ?>
                    {"mData": "total", "bSortable": true, "sClass": "alignCenter"},
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

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 8px; right: 0px'></div>`)

            jQuery("#datatable_wrapper #right_panel")
                .append(`<?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "period", "", $par['period'], "", "200px", ""); ?>`)

            jQuery("#type").change(() => {
                filter()
            })

            jQuery("#period").change(() => {
                filter()
            })

        })

        function filter() {

            type = jQuery("#type").val()
            period = jQuery("#period").val()

            window.location = '?<?= getPar($par, "type, period") ?>&par[type]=' + type + '&par[period]=' + period
        }

    </script>
    <?php
}

function indexObjective()
{
    global $s, $par, $arrTitle, $menuAccess;

    $code = getRow("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'");
    $never_sync = getField("SELECT COUNT(*) FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]'") == 0;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1rem;">

            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, idKode, tab") ?>';"/>

        </div>

        <form action="" method="" class="stdform">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Penilaian&emsp;</legend>

                <div style="display: flex">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Posisi</label>
                            <span class="field">&nbsp;<?= getField("SELECT `namaTipe` FROM `pen_tipe` WHERE `kodeTipe` = '$par[type]'"); ?></span>
                        </p>

                        <p>
                            <label class="l-input-small2">Kode Penilaian</label>
                            <span class="field">&nbsp;<?= $code['namaKode'] . " -- " . $code['subKode'] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small2">Periode</label>
                            <span class="field">
                            &nbsp;
                            <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[period]'"); ?>
                        </span>
                        </p>

                    </div>
                    <div style="flex: 1"></div>
                </div>

            </fieldset>

        </form>

        <br>

        <div style="position: relative;">

            <ul class="hornav" style="margin: 0;">
                <?php foreach ($arr_aspect as $value) : $selected = $value['idAspek'] == $par['tab'] ? "current" : ""; ?>
                    <li class="<?= $selected ?>"><a href="#tab_<?= $value['idAspek'] ?>"><?= $value['namaAspek'] ?></a></li>
                <?php endforeach; ?>
            </ul>

            <?php if ($menuAccess[$s]['add'] && $never_sync) : ?>
                <div style="position: absolute; bottom: .3rem; right: 0;">
                    <a class="btn btn1 btn_inboxi" onclick="openBox(`popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync`, 800, 250)"><span>Ambil Data</span></a>
                </div>
            <?php endif; ?>

        </div>

        <?php foreach ($arr_aspect as $aspect) : $selected = $aspect['idAspek'] == $par['tab'] ? "block" : "none"; ?>

            <div id="tab_<?= $aspect['idAspek'] ?>" class="subcontent" style="display: <?= $selected ?>; overflow-x: auto;">

                <!--default-->
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" style="width: max-content; min-width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 20px; vertical-align: middle;">No.</th>
                        <th style="min-width: 400px; vertical-align: middle;">Sasaran</th>
                        <?php if ($aspect['aspekKode'] == 1) : ?>
                            <th style="width: 200px; vertical-align: middle;">Key Performance Indicator (KPI)</th>
                        <?php endif; ?>
                        <?php if ($aspect['aspekKode'] == 2) : ?>
                            <th style="width: 200px; vertical-align: middle;">Profisiensi 1</th>
                            <th style="width: 200px; vertical-align: middle;">Profisiensi 2</th>
                            <th style="width: 200px; vertical-align: middle;">Profisiensi 3</th>
                            <th style="width: 200px; vertical-align: middle;">Profisiensi 4</th>
                        <?php endif; ?>
                        <th style="width: 100px; vertical-align: middle;">Scoring</th>
                        <th style="width: 150px; vertical-align: middle;">Pencapaian Target</th>
                        <th style="width: 100px; vertical-align: middle;">Measurement</th>
                        <?php if (isset($menuAccess[$s]['add']) || isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                            <th style="width: 50px; vertical-align: middle">Kontrol</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $tabs = $aspect['aspekKode'] == 1 ? 7 : ($aspect['aspekKode'] == 2 ? 10 : 6);
                    $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[idKode]' AND `idAspek` = '$aspect[idAspek]' AND `status` = 't'");

                    foreach ($perspectives as $perspective) :
                        ?>
                        <tr style="background-color: #d9d9d9;">
                            <td colspan="<?= $tabs ?>"><strong><?= strtoupper($perspective['namaPrespektif']) ?></strong></td>
                        </tr>
                        <?php

                        $indicators = [];

                        // flatting stacked array
                        $arr_indicators = getRows("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$perspective[idPrespektif]' AND `statusIndikator` = 't' ORDER BY `urutanIndikator`");

                        foreach ($arr_indicators as $key_indicator_1 => $indicator_1) {

                            if ($indicator_1['levelIndikator'] != 1)
                                continue;

                            $indicators[] = $indicator_1;

                            foreach ($arr_indicators as $key_indicator_2 => $indicator_2) {

                                if ($indicator_2['levelIndikator'] == 1 || $indicator_2['indukIndikator'] != $indicator_1['kodeIndikator'])
                                    continue;

                                $indicators[] = $indicator_2;
                            }

                        }

                        foreach ($indicators as $key_indicator => $indicator) :?>
                            <tr style="background-color: #f5f5f5;">
                                <td colspan="<?= $tabs - 1 ?>"><?= ($key_indicator + 1) . ". " . strtoupper($indicator['uraianIndikator']) ?></td>
                                <?php if (isset($menuAccess[$s]['add'])) : ?>
                                    <td>
                                        <center>
                                            <a title="Tambah Data" class="add"
                                               onclick="openBox('popup.php?<?= getPar($par, "mode") ?>&par[mode]=add_objective&par[model]=<?= $aspect['aspekKode'] ?>&par[idPrespektif]=<?= $perspective['idPrespektif'] ?>&par[idIndikator]=<?= $indicator['kodeIndikator'] ?>&par[idAspek]=<?= $aspect['idAspek'] ?>', 1000, 640);">
                                                <span>Add</span>
                                            </a>
                                        </center>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php

                            $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]' AND `idPrespektif` = '$perspective[idPrespektif]' AND `idIndikator` = '$indicator[kodeIndikator]'");

                            foreach ($objectives as $key_objective => $objective) : ?>
                                <tr>
                                    <td colspan="2"><?= ($key_indicator + 1) . "." . ($key_objective + 1) ?>&emsp;<?= $objective['uraianSasaran'] ?></td>
                                    <?php if ($aspect['aspekKode'] == 1) : ?>
                                        <td><?= getField("SELECT `komponen` FROM `master_kpi` WHERE `id` = '$objective[master_kpi_id]'") ?></td>
                                    <?php endif; ?>
                                    <?php if ($aspect['aspekKode'] == 2) : ?>
                                        <td width="150"><?= $objective['prof_1'] ?></td>
                                        <td width="150"><?= $objective['prof_2'] ?></td>
                                        <td width="150"><?= $objective['prof_3'] ?></td>
                                        <td width="150"><?= $objective['prof_4'] ?></td>
                                    <?php endif; ?>
                                    <td><?= $objective['scoringSasaran'] ?></td>
                                    <td><?= $objective['targetSasaran'] ?> <?= $objective['targetSasaran2'] ?></td>
                                    <td><?= $objective['measurementSasaran'] ?></td>
                                    <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                                        <td>
                                            <center>
                                                <?php if ($menuAccess[$s]['edit']) : ?>
                                                    <a title="Ubah Data"
                                                       class="edit"
                                                       onclick="openBox('popup.php?<?= getPar($par, "mode, idSasaran") ?>&par[mode]=edit_objective&par[model]=<?= $aspect['aspekKode'] ?>&par[idAspek]=<?= $aspect['idAspek'] ?>&par[idSasaran]=<?= $objective['idSasaran'] ?>', 1000, 640);">
                                                        <span>Edit</span>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($menuAccess[$s]['delete']) : ?>
                                                    <a title="Hapus Data"
                                                       href="?<?= getPar($par, "mode, idSasaran") ?>&par[mode]=del_objective&par[idSasaran]=<?= $objective['idSasaran'] ?>"
                                                       class="delete"
                                                       onclick="return confirm('Konfirmasi hapus data')">
                                                        <span>Delete</span>
                                                    </a>
                                                <?php endif; ?>
                                            </center>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>

                        <?php endforeach; ?>

                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

        <?php endforeach; ?>

    </div>

    <script>
        jQuery("#period").change(() => {

            period = jQuery("#period").val()

            window.location = '?<?= getPar($par, "period") ?>&par[period]=' + period
        })
    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_routine, $arr_status;

    $categories = KPIMasterCategory::where('status', 1)->orderBy('urut')->get();

    $conversions = KPIMasterNilaiTipe::where('periode_id', $par['period'])->orderBy('nama')->get();
    $data = KPIMasterObyektif::find($par['idSasaran']);

    $data['idPrespektif'] = $data['idPrespektif'] ?: $par['idPrespektif'];
    $data['idIndikator'] = $data['idIndikator'] ?: $par['idIndikator'];

    $data['urutanSasaran'] = $data['urutanSasaran'] ?: getField("SELECT `urutanSasaran` FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]' AND `idPrespektif` = '$data[idPrespektif]' AND `idIndikator` = '$data[idIndikator]' ORDER BY `urutanSasaran` DESC LIMIT 1") + 1;;

    $data['tipe'] = $data['tipe'] ?: $conversions[0]['id'];
    $data['rutinSasaran'] = $data['rutinSasaran'] ?: 't';
    $data['statusSasaran'] = $data['statusSasaran'] ?: 't';

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

                <legend>&emsp;Penilaian&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Prespektif</label>
                            <span class="field">
                                &nbsp;<?= getField("SELECT `namaPrespektif` FROM `pen_setting_prespektif` WHERE `idPrespektif` = '$data[idPrespektif]'") ?>
                            </span>
                        </p>

                        <p>
                            <label class="l-input-small2">Indikator</label>
                            <span class="field">
                                &nbsp;<?= getField("SELECT `uraianIndikator` FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$data[idPrespektif]' AND `kodeIndikator` = '$data[idIndikator]'") ?>
                            </span>
                        </p>

                    </div>
                    <div style="flex: 1"></div>
                </div>

            </fieldset>

            <br>

            <ul class="hornav" style="margin: 0;">
                <li class="current"><a href="#tab_11">Indikator</a></li>
                <li class=""><a href="#tab_12">Rating Nilai</a></li>
            </ul>

            <div id="tab_11" class="subcontent" style="display: block;">

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Sasaran</label>
                        <div class="field">
                            <input type="text" name="inp[uraianSasaran]" id="inp[uraianSasaran]" class="mediuminput" value="<?= $data['uraianSasaran'] ?>">
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Scoring</label>
                        <div class="field">
                            <input type="text" name="inp[scoringSasaran]" id="inp[scoringSasaran]" class="mediuminput" value="<?= $data['scoringSasaran'] ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <?php if ($par['model'] == 1) : ?>
                    <div style="display: flex;">
                        <div style="flex: 1">

                            <p>
                                <label class="l-input-small2">Key Performance Indicator (KPI)</label>
                            <div class="field">
                                <?= selectArray("inp[master_kpi_id]", "", $categories, "id", "komponen", $data['master_kpi_id'], "", "", "64%"); ?>
                            </div>
                            </p>

                        </div>
                        <div style="flex: 1">


                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($par['model'] == 2) : ?>
                    <p>
                        <label class="l-input-small">Profisiensi 1</label>
                    <div class="field">
                        <input type="text" name="inp[prof_1]" id="inp[prof_1]" class="smallinput" value="<?= $data['prof_1'] ?>">
                        <small style="display: block">&emsp;Level Jabatan JG 01 s.d 05</small>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Profisiensi 2</label>
                    <div class="field">
                        <input type="text" name="inp[prof_2]" id="inp[prof_2]" class="smallinput" value="<?= $data['prof_2'] ?>">
                        <small style="display: block">&emsp;Level Jabatan JG 06 s.d 09</small>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Profisiensi 3</label>
                    <div class="field">
                        <input type="text" name="inp[prof_3]" id="inp[prof_3]" class="smallinput" value="<?= $data['prof_3'] ?>">
                        <small style="display: block">&emsp;Level Jabatan JG 10 s.d 13</small>
                    </div>
                    </p>

                    <p>
                        <label class="l-input-small">Profisiensi 4</label>
                    <div class="field">
                        <input type="text" name="inp[prof_4]" id="inp[prof_4]" class="smallinput" value="<?= $data['prof_4'] ?>">
                        <small style="display: block">&emsp;Level Jabatan JG 14 s.d 16</small>
                    </div>
                    </p>
                <?php endif; ?>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Pencapaian Target</label>
                        <div class="field">
                            <input type="text" name="inp[targetSasaran]" id="inp[targetSasaran]" class="mediuminput" value="<?= $data['targetSasaran'] ?>" style="width: 50px;">
                            <input type="text" name="inp[targetSasaran2]" id="inp[targetSasaran2]" class="mediuminput" value="<?= $data['targetSasaran2'] ?>" style="width: 150px;">
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Measurement</label>
                        <div class="field">
                            <input type="text" name="inp[measurementSasaran]" id="inp[measurementSasaran]" class="mediuminput" value="<?= $data['measurementSasaran'] ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Keterangan</label>
                        <div class="field">
                            <textarea name="inp[keteranganSasaran]" id="inp[keteranganSasaran]" class="mediuminput" style="height: 50px"><?= $data['keteranganSasaran'] ?></textarea>
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Rating</label>
                        <div class="field">
                            <input type="text" name="inp[ratingSasaran]" id="inp[ratingSasaran]" class="vsmallinput" value="<?= $data['ratingSasaran'] ?>">
                        </div>
                        </p>

                        <p>
                            <label class="l-input-small2">Urut</label>
                        <div class="field">
                            <input type="text" name="inp[urutanSasaran]" id="inp[urutanSasaran]" class="vsmallinput" value="<?= $data['urutanSasaran'] ?>">
                        </div>
                        </p>

                    </div>
                </div>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Aktifitas Rutin</label>
                        <div class="field fradio">
                            <?php foreach ($arr_routine as $key => $value) : $checked = $data['rutinSasaran'] == $key ? "checked" : ""; ?>
                                <input type="radio" <?= $checked ?> name="inp[rutinSasaran]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Status</label>
                        <div class="field fradio">
                            <?php foreach ($arr_status as $key => $value) : $checked = $data['statusSasaran'] == $key ? "checked" : ""; ?>
                                <input type="radio" <?= $checked ?> name="inp[statusSasaran]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        </p>

                    </div>
                </div>

            </div>

            <div id="tab_12" class="subcontent" style="display: none;">

                <p>
                    <label class="l-input-small">Tipe Penilaian</label>
                <div class="field">
                    <?= selectArray("inp[tipe]", "", $conversions, "id", "nama", $data['tipe'], "", "", "50%", "onchange=\"updateType(this.value)\""); ?>
                </div>
                </p>

                <br>

                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                    <tr>
                        <th width="50">Nilai</th>
                        <th  width="50">Kode</th>
                        <th>WOM</th>
                        <th>Batas Bawah</th>
                        <th>Batas Atas</th>
                    </tr>
                    </thead>
                    <tbody id="rating">
                    <?php
                    if ($ratings = KPIMasterNilaiTipe::find($data['tipe'])) :
                        $ratings = $ratings->detil()->orderBy('nilai', 'DESC')->get();
                        ?>
                        <?php foreach ($ratings as $key => $rating) : ?>
                        <tr>
                            <td>
                                <center><?= $rating['nilai'] ?></center>
                            </td>
                            <td>
                                <center><?= $rating['kode'] ?></center>
                            </td>
                            <td>
                                <div style="background-color: <?= $rating['warna'] ?>">&emsp;</div>
                            </td>
                            <td>
                                <center><?= $rating['minimal'] ?></center>
                            </td>
                            <td>
                                <center><?= $rating['maksimal'] ?></center>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>

            </div>

        </form>
    </div>
    <script>

        function updateType(type_id) {

            jQuery.post(`ajax.php?<?= getPar($par, "mode, id") ?>&par[mode]=type&par[id]=${type_id}`, function (data) {

                jQuery('#rating tr').remove()

                datas = JSON.parse(data)

                if (!datas) {
                    return
                }

                datas.forEach(function (data) {
                    jQuery('#rating').append(`
                        <tr>
                            <td>
                                <center>${data.nilai}</center>
                            </td>
                            <td>
                                <center>${data.kode}</center>
                            </td>
                            <td>
                                <div style="background-color: ${data.warna}">&emsp;</div>
                            </td>
                            <td>
                                <center>${data.minimal}</center>
                            </td>
                            <td>
                                <center>${data.maksimal}</center>

                            </td>
                        </tr>
                    `)
                })

            });

        }

        <?php if ($par['mode'] == 'add_objective') : ?>
        updateType(<?= $data['tipe'] ?>)
        <?php endif; ?>

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

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Ambil Data&emsp;</legend>

                <p style="position: absolute; top: .5rem; right: 1rem;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Dari</label>
                    <span class="field">&nbsp;<?= getField("SELECT `subKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'") ?></span>
                </p>

                <p>
                    <label class="l-input-small">Periode</label>
                <div class="field">
                    <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' AND `kodeData` != '$par[period]' ORDER BY `urutanData` ASC", "kodeData", "namaData", "inp[period]", "", $par['period'], "", "250px", ""); ?>
                </div>
                </p>

            </fieldset>

        </form>
    </div>
    <?php
}

function type()
{
    global $par;

    $master = KPIMasterNilaiTipe::find($par['id']);
    $detail = $master->detil()->orderBy('nilai', 'DESC')->get();

    return json_encode($detail);
}

function insert()
{
    global $inp, $par, $cID;

    repField();

    DB::beginTransaction();

    try {

        KPIMasterObyektif::create([
            'idKode' => $par['idKode'],
            'idPeriode' => $par['period'],
            'idPrespektif' => $par['idPrespektif'],
            'idIndikator' => $par['idIndikator'],
            'uraianSasaran' => $inp['uraianSasaran'],
            'master_kpi_id' => $inp['master_kpi_id'],
            'targetSasaran' => $inp['targetSasaran'],
            'targetSasaran2' => $inp['targetSasaran2'],
            'keteranganSasaran' => $inp['keteranganSasaran'],
            'scoringSasaran' => $inp['scoringSasaran'],
            'measurementSasaran' => $inp['measurementSasaran'],
            'tipe' => $inp['tipe'],
            'statusSasaran' => $inp['statusSasaran'],
            'rutinSasaran' => $inp['rutinSasaran'],
            'ratingSasaran' => $inp['ratingSasaran'],
            'urutanSasaran' => $inp['urutanSasaran'],
            'createBy' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Data berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal disimpan');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, model, idPrespektif, idIndikator, idAspek, idSasaran") . "&par[mode]=det';</script>";
}

//function insertSync()
//{
//    global $inp, $par, $cUsername;
//
//    repField();
//
//    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$inp[period]' ORDER BY `urutanAspek`");
//
//    $datas = [];
//
//    // not filter status
//    foreach ($arr_aspect as $aspect) {
//
//        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[idKode]' AND `idPeriode` = '$inp[period]' AND `idAspek` = '$aspect[idAspek]'");
//
//        foreach ($perspectives as $perspective) {
//
//            $indicators = [];
//
//            $new_perspective = getRow("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]' AND `namaPrespektif` LIKE '$perspective[namaPrespektif]'");
//
//            // flatting stacked array
//            $arr_indicators = getRows("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$perspective[idPrespektif]' ORDER BY `urutanIndikator`");
//
//            foreach ($arr_indicators as $key_indicator_1 => $indicator_1) {
//
//                if ($indicator_1['levelIndikator'] != 1)
//                    continue;
//
//                $indicators[] = $indicator_1;
//
//                foreach ($arr_indicators as $key_indicator_2 => $indicator_2) {
//
//                    if ($indicator_2['levelIndikator'] == 1 || $indicator_2['indukIndikator'] != $indicator_1['kodeIndikator'])
//                        continue;
//
//                    $indicators[] = $indicator_2;
//                }
//
//            }
//
//            foreach ($indicators as $key_indicator => $indicator) {
//
//                $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[idKode]' AND `idPeriode` = '$inp[period]' AND `idPrespektif` = '$perspective[idPrespektif]' AND `idIndikator` = '$indicator[kodeIndikator]'");
//
//                foreach ($objectives as $key_objective => $objective) {
//
////                    // override for new object
//                    $objective['idPrespektif'] = $new_perspective['idPrespektif'];
//
//                    $datas[] = $objective;
//
//                }
//
//            }
//
//        }
//
//    }
//
//    // im not recomending use this design
//    foreach ($datas as $data) {
//
//        $nextId = getField("SELECT `idSasaran` FROM `pen_sasaran_obyektif` ORDER BY `idSasaran` DESC LIMIT 1") + 1;
//
//        $sql = "INSERT INTO `pen_sasaran_obyektif` SET
//
//        `idSasaran` = '$nextId',
//        `idKode` = '$par[idKode]',
//        `idPeriode` = '$par[period]',
//        `idPrespektif` = '$data[idPrespektif]',
//        `idIndikator` = '$data[idIndikator]',
//        `uraianSasaran` = '$data[uraianSasaran]',
//        `master_kpi_id` = '$data[master_kpi_id]',
//        `targetSasaran` = '$data[targetSasaran]',
//        `targetSasaran2` = '$data[targetSasaran2]',
//        `keteranganSasaran` = '$data[keteranganSasaran]',
//        `scoringSasaran` = '$data[scoringSasaran]',
//        `measurementSasaran` = '$data[measurementSasaran]',
//        `statusSasaran` = '$data[statusSasaran]',
//        `rutinSasaran` = '$data[rutinSasaran]',
//        `ratingSasaran` = '$data[ratingSasaran]',
//        `urutanSasaran` = '$data[urutanSasaran]',
//        `prof_1` = '$data[prof_1]',
//        `prof_2` = '$data[prof_2]',
//        `prof_3` = '$data[prof_3]',
//        `prof_4` = '$data[prof_4]',
//        `nilaiSasaran_1a` = '$data[nilaiSasaran_1a]',
//        `nilaiSasaran_1b` = '$data[nilaiSasaran_1b]',
//        `nilaiSasaran_1k` = '$data[nilaiSasaran_1k]',
//        `nilaiSasaran_2a` = '$data[nilaiSasaran_2a]',
//        `nilaiSasaran_2b` = '$data[nilaiSasaran_2b]',
//        `nilaiSasaran_2k` = '$data[nilaiSasaran_2k]',
//        `nilaiSasaran_3a` = '$data[nilaiSasaran_3a]',
//        `nilaiSasaran_3b` = '$data[nilaiSasaran_3b]',
//        `nilaiSasaran_3k` = '$data[nilaiSasaran_3k]',
//        `nilaiSasaran_4a` = '$data[nilaiSasaran_4a]',
//        `nilaiSasaran_4b` = '$data[nilaiSasaran_4b]',
//        `nilaiSasaran_4k` = '$data[nilaiSasaran_4k]',
//        `nilaiSasaran_5a` = '$data[nilaiSasaran_5a]',
//        `nilaiSasaran_5b` = '$data[nilaiSasaran_5b]',
//        `nilaiSasaran_5k` = '$data[nilaiSasaran_5k]',
//        `wom_1` = '$data[wom_1]',
//        `wom_2` = '$data[wom_2]',
//        `wom_3` = '$data[wom_3]',
//        `wom_4` = '$data[wom_4]',
//        `wom_5` = '$data[wom_5]',
//        `createTime` = '" . date("Y-m-d H:i:s") . "',
//        `createBy` = '$cUsername'";
//
//        db($sql);
//    }
//
//    echo "<script>alert('Data berhasil disalin')</script>";
//    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "&par[mode]=det';</script>";
//}

function update()
{
    global $inp, $par, $cID;

    repField();

    DB::beginTransaction();

    try {

        $objective = KPIMasterObyektif::find($par['idSasaran']);

        $objective->update([
            'uraianSasaran' => $inp['uraianSasaran'],
            'master_kpi_id' => $inp['master_kpi_id'],
            'targetSasaran' => $inp['targetSasaran'],
            'targetSasaran2' => $inp['targetSasaran2'],
            'keteranganSasaran' => $inp['keteranganSasaran'],
            'scoringSasaran' => $inp['scoringSasaran'],
            'measurementSasaran' => $inp['measurementSasaran'],
            'tipe' => $inp['tipe'],
            'statusSasaran' => $inp['statusSasaran'],
            'rutinSasaran' => $inp['rutinSasaran'],
            'ratingSasaran' => $inp['ratingSasaran'],
            'urutanSasaran' => $inp['urutanSasaran'],
            'updateBy' => $cID
        ]);

        DB::commit();

        echo "<script>alert('Data berhasil diubah');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal diubah');</script>";

    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, model, idPrespektif, idIndikator, idAspek, idSasaran") . "&par[mode]=det';</script>";
}

function delete()
{
    global $par;

    DB::beginTransaction();

    try {

        $objective = KPIMasterObyektif::find($par['idSasaran']);

        $objective->delete();

        DB::commit();

        echo "<script>alert('Data berhasil ihapus');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal ihapus');</script>";

    }

    echo "<script>window.location='?" . getPar($par, "mode, idAspek, idSasaran") . "&par[mode]=det';</script>";
}

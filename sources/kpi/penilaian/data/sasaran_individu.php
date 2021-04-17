<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$arr_breakdown = ["t" => "Ya", "f" => "Tidak"];
$arr_target = ["fixed" => "Fixed", "akumulasi" => "Akumulasi"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "det":
            indexObjective();
            break;

        case "add":
            if (isset($menuAccess[$s]['add']))
                isset($_submit) ? insert() : indexObjective();
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
    $par['idKode'] = $par['idKode'] ?: getField("SELECT `idKode` FROM `pen_setting_kode` WHERE `kodeTipe`= '$par[type]' AND `statusKode` = 't' ORDER BY `idKode` ASC LIMIT 1");

    $par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory`= 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");

    if ($json == 1) {

        header("Content-type: application/json");

        $ret = [];
        $res = db("SELECT * FROM `pen_pegawai` t1 JOIN `dta_pegawai` t2 ON t2.`id` = t1.`idPegawai` WHERE t1.`tipePenilaian` = '$par[type]' AND t1.`kodePenilaian` = '$par[idKode]'");

        while ($r = mysql_fetch_assoc($res)) {

            $r['divisi'] = getField("SELECT `namaTipe` FROM `pen_tipe` WHERE `kodeTipe` = '$r[tipePenilaian]'");
            $r['jabatan'] = getField("SELECT `subKode` FROM `pen_setting_kode` WHERE `idKode` = '$r[kodePenilaian]'");

            $count = getField("SELECT COUNT(*) FROM `pen_sasaran_individu` WHERE `idPegawai` = '$r[id]' AND `idPeriode` = '$par[period]'");
            $status = $arr_status_image[$count > 0 ? 't' : 'f'];

            $r['status'] = "<a href='?" . getPar($par, "mode") . "&par[mode]=det&par[idPegawai]=$r[id]'>$status</a>";

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
                <th width="80" style="vertical-align: middle;">NPP</th>
                <th style="vertical-align: middle;">Nama</th>
                <th width="150" style="vertical-align: middle;">Unit Kerja</th>
                <th width="150" style="vertical-align: middle;">Jabatan</th>
                <th width="120" style="vertical-align: middle;">Sasaran Individu</th>
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
                    {"mData": "reg_no", "bSortable": true},
                    {"mData": "name", "bSortable": true},
                    {"mData": "divisi", "bSortable": false},
                    {"mData": "jabatan", "bSortable": false},
                    {"mData": "status", "bSortable": false, "sClass": "alignCenter"},
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
                .append(`<?= comboData("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC", "kodeTipe", "namaTipe", "par[type]", "", $par['type'], "", "200px", ""); ?>`)
                .append(`<?= comboData("SELECT `idKode`, `subKode` FROM `pen_setting_kode` WHERE `kodeTipe` = '$par[type]' AND statusKode = 't' ORDER BY `idKode` ASC", "idKode", "subKode", "par[idKode]", "", $par['idKode'], "", "250px", ""); ?>`)

            jQuery("#datatable_wrapper .top").append(`<div id='right_panel' class='dataTables_filter' style='float:right; top: 8px; right: 0px'></div>`)

            jQuery("#datatable_wrapper #right_panel")
                .append(`<?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "par[period]", "", $par['period'], "", "200px", ""); ?>`)

            // filter

            jQuery("#par\\[type\\]").change(() => {

                type = jQuery("#par\\[type\\]").val()

                window.location = '?<?= getPar($par, "type, idKode") ?>&par[type]=' + type
            })

            jQuery("#par\\[idKode\\]").change(() => {

                code = jQuery("#par\\[idKode\\]").val()

                window.location = '?<?= getPar($par, "idKode") ?>&&par[idKode]=' + code
            })

            jQuery("#par\\[period\\]").change(() => {

                period = jQuery("#par\\[period\\]").val()

                window.location = '?<?= getPar($par, "period") ?>&par[period]=' + period
            })

        })

    </script>
    <?php
}

function indexObjective()
{
    global $s, $par, $arrTitle, $menuAccess;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");

    $appraisal = getRow("SELECT * FROM `pen_pegawai` WHERE `tipePenilaian` = '$par[type]' AND `kodePenilaian` = '$par[idKode]' AND `idPegawai` = '$par[idPegawai]'");
    $appraisal_type = getRow("SELECT * FROM `pen_tipe` WHERE `kodeTipe` = '$appraisal[tipePenilaian]'");
    $appraisal_code = getRow("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$appraisal[kodePenilaian]'");

    $master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");

    $emp = getRow("SELECT * FROM `dta_pegawai` WHERE `id` = '$par[idPegawai]'");
    $emp_phist = getRow("SELECT * FROM `emp_phist` WHERE `parent_id` = '$par[idPegawai]'");

    $never_sync = getField("SELECT COUNT(*) FROM `pen_sasaran_individu` WHERE `idPegawai` = '$par[idPegawai]' AND `idPeriode` = '$par[period]'") == 0;

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1rem;">

            <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "period", "", $par['period'], "", "200px", ""); ?>
            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, idKode, tab") ?>';"/>

        </div>

        <form action="" method="post" class="stdform">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Pegawai&emsp;</legend>

                <div style="display: flex">
                    <div style="flex: 1; display: flex; justify-content: center;">

                        <img alt="<?= $emp["regNo"] ?>" width="120" src="<?= ($emp["pic_filename"] == "" ? "files/emp/pic/nophoto.jpg" : "images/foto/" . $emp["pic_filename"]) ?>" class='pasphoto'>

                    </div>
                    <div style="flex: 4">

                        <p>
                            <label class="l-input-small">Nama</label>
                            <span class="field">&nbsp;<?= $emp['name'] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">NPP</label>
                            <span class="field">&nbsp;<?= $emp['reg_no'] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Tipe Penilaian</label>
                            <span class="field">&nbsp;<?= $appraisal_type['namaTipe'] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Kode Penilaian</label>
                            <span class="field">&nbsp;<?= $appraisal_code['subKode'] ?></span>
                        </p>

                    </div>
                    <div style="flex: 4">

                        <p>
                            <label class="l-input-small">NPWP</label>
                            <span class="field">&nbsp;<?= $emp['npwp_no'] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Pangkat/Grade</label>
                            <span class="field">&nbsp;<?= $master[$emp_phist['rank']] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Lokasi</label>
                            <span class="field">&nbsp;<?= $master[$emp_phist['location']] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Jabatan</label>
                            <span class="field">&nbsp;<?= $emp['pos_name'] ?></span>
                        </p>

                    </div>
                </div>

            </fieldset>

        </form>

        <br>

        <form method="post" action="?<?= getPar($par) ?>&_submit" id="form">

            <div style="position: relative;">

                <ul class="hornav" style="margin: 0;">
                    <?php foreach ($arr_aspect as $key => $value) : $selected = $key == 0 ? "current" : ""; ?>
                        <li class="<?= $selected ?>"><a href="#tab_<?= $value['idAspek'] ?>"><?= $value['namaAspek'] ?></a></li>
                    <?php endforeach; ?>
                </ul>

                <div style="position: absolute; bottom: .3rem; right: 0;">
                    <?php if ($menuAccess[$s]['edit'] && $par['mode'] == 'det') : ?>
                        <?php if ($never_sync) : ?>
                            <a class="btn btn1 btn_inboxi" onclick="openBox(`popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync`, 800, 250)"><span>Ambil Data</span></a>
                        <?php endif; ?>
                        <input type="button" class="cancel radius2" value="Setting Sasaran" onclick="window.location='?<?= getPar($par, "mode") ?>&par[mode]=add'">
                    <?php elseif ($par['mode'] == 'add') : ?>
                        <input type="submit" class="submit radius2" value="Simpan"/>
                        <input type="button" class="cancel radius2" value="Batal" onclick="window.location='?<?= getPar($par, "mode") ?>&par[mode]=det';"/>
                    <?php endif; ?>
                </div>

            </div>

            <?php foreach ($arr_aspect as $key => $aspect) : $selected = $key == 0 ? "block" : "none"; ?>

                <div id="tab_<?= $aspect['idAspek'] ?>" class="subcontent" style="display: <?= $selected ?>; overflow-x: auto;">

                    <!--default-->
                    <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" style="width: max-content; min-width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 20px; vertical-align: middle;">No.</th>
                            <th style="min-width: 400px; vertical-align: middle;">Sasaran</th>
                            <?php if ($aspect['aspekKode'] == 1) : ?>
                                <th style="width: 230px; vertical-align: middle;">Key Performance Indicator (KPI)</th>
                            <?php endif; ?>
                            <?php if ($aspect['aspekKode'] == 2) : ?>
                                <th style="width: 200px; vertical-align: middle;">Profisiensi 1</th>
                                <th style="width: 200px; vertical-align: middle;">Profisiensi 2</th>
                                <th style="width: 200px; vertical-align: middle;">Profisiensi 3</th>
                                <th style="width: 200px; vertical-align: middle;">Profisiensi 4</th>
                            <?php endif; ?>
                            <th style="width: 100px; vertical-align: middle;">Scoring</th>
                            <th style="width: 150px; vertical-align: middle;">Pencapaian Target</th>
                            <?php if ($par['mode'] == 'det') : ?>
                                <th style="width: 200px; vertical-align: middle;">Satuan KPI</th>
                                <th style="width: 50px; vertical-align: middle;">Bobot</th>
                            <?php elseif ($par['mode'] == 'add') : ?>
                                <th style="width: 100px; vertical-align: middle;">Measurement</th>
                            <?php endif; ?>
                            <?php if ($par['mode'] == 'det') : ?>
                                <?php if ((isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']))) : ?>
                                    <th style="width: 50px; vertical-align: middle">Kontrol</th>
                                <?php endif; ?>
                            <?php elseif ($par['mode'] == 'add') : ?>
                                <th style="width: 50px; vertical-align: middle"><input type="checkbox" id="checkall_tab_<?= $aspect['idAspek'] ?>" onclick="checkAll('<?= $aspect['idAspek'] ?>')"></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $tabs = $aspect['aspekKode'] == 1 ? 8 : ($aspect['aspekKode'] == 2 ? 11 : 7);
                        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$appraisal[kodePenilaian]' AND `idAspek` = '$aspect[idAspek]' AND `status` = 't'");

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

                            foreach ($indicators as $key_indicator => $indicator) : ?>
                                <tr style="background-color: #f5f5f5;">
                                    <td colspan="<?= $tabs ?>"><?= ($key_indicator + 1) . ". " . strtoupper($indicator['uraianIndikator']) ?></td>
                                </tr>
                                <?php

                                $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$appraisal[kodePenilaian]' AND `idPrespektif` = '$perspective[idPrespektif]' AND `idIndikator` = '$indicator[kodeIndikator]' AND `idPeriode` = '$par[period]'");

                                foreach ($objectives as $key_objective => $objective) : ?>
                                    <?php $exist = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPegawai` = '$emp[id]' AND `idSasaran` = '$objective[idSasaran]' AND `idPeriode` = '$par[period]'"); ?>
                                    <?php if ($par['mode'] == 'det' && $exist) : ?>
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
                                            <td><?= $exist['satuanIndividu'] . " " . $exist['satuanIndividu2'] ?></td>
                                            <td>
                                                <center><?= $exist['bobotIndividu'] ?>%</center>
                                            </td>
                                            <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                                                <td>
                                                    <center>
                                                        <?php if ($menuAccess[$s]['edit']) : ?>
                                                            <a title="Ubah Data" class="edit"
                                                               onclick="openBox('popup.php?<?= getPar($par, "mode, idSasaran") ?>&par[mode]=edit&par[idIndividu]=<?= $exist['idIndividu'] ?>', 1000, 350);">
                                                                <span>Edit</span>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($menuAccess[$s]['delete']) : ?>
                                                            <a title="Hapus Data"
                                                               href="?<?= getPar($par, "mode, idSasaran") ?>&par[mode]=del&par[idIndividu]=<?= $exist['idIndividu'] ?> ?>"
                                                               class="delete"
                                                               onclick="return confirm('Konfirmasi hapus data')">
                                                                <span>Delete</span>
                                                            </a>
                                                        <?php endif; ?>
                                                    </center>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php elseif ($par['mode'] == 'add') : ?>
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
                                            <td>
                                                <center>
                                                    <input type="checkbox"
                                                           id="checkbox_1"
                                                           class="checkbox_tab_<?= $aspect['idAspek'] ?>"
                                                           name="inp[objective][<?= $objective['idSasaran'] ?>]" <?= $exist ? "checked" : "" ?>>
                                                </center>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            <?php endforeach; ?>

                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>

            <?php endforeach; ?>

        </form>
    </div>

    <script>
        jQuery("#period").change(() => {

            period = jQuery("#period").val()

            window.location = '?<?= getPar($par, "period") ?>&par[period]=' + period
        })

        function checkAll(tab_id) {

            state = document.getElementById("checkall_tab_" + tab_id).checked

            jQuery(".checkbox_tab_" + tab_id).each(function () {

                if (state)
                    jQuery(this).attr("checked", "true")
                else
                    jQuery(this).removeAttrs("checked")

            })

            jQuery.uniform.update()

        }

    </script>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_breakdown, $arr_target;

    $r = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idIndividu` = '$par[idIndividu]'");

    $r['settingIndividu'] = $r['settingIndividu'] ?: 't';
    $r['tipeTarget'] = $r['tipeTarget'] ?: 'akumulasi';

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
                            <label class="l-input-small2">Target</label>
                        <div class="field">
                            <input type="text" name="inp[targetIndividu]" id="inp[targetIndividu]" class="mediuminput" value="<?= $r['targetIndividu'] ?>" style="width: 50px;">
                            <input type="text" name="inp[keteranganTargetIndividu]" id="inp[keteranganTargetIndividu]" class="mediuminput" value="<?= $r['keteranganTargetIndividu'] ?>" style="width: 150px;">
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Breakdown Target</label>
                        <div class="field fradio">
                            <?php foreach ($arr_breakdown as $key => $value) : $checked = $r['settingIndividu'] == $key ? "checked" : ""; ?>
                                <input type="radio" <?= $checked ?> name="inp[settingIndividu]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        </p>

                    </div>
                </div>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Satuan</label>
                        <div class="field">
                            <input type="text" name="inp[satuanIndividu]" id="inp[satuanIndividu]" class="mediuminput" value="<?= $r['satuanIndividu'] ?>" placeholder="Depan" style="width: 100px;">
                            <input type="text" name="inp[satuanIndividu2]" id="inp[satuanIndividu2]" class="mediuminput" value="<?= $r['satuanIndividu2'] ?>" placeholder="Belakang" style="width: 100px;">
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Target Bulanan</label>
                        <div class="field fradio">
                            <?php foreach ($arr_target as $key => $value) : $checked = $r['tipeTarget'] == $key ? "checked" : ""; ?>
                                <input type="radio" <?= $checked ?> name="inp[tipeTarget]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php endforeach; ?>
                        </div>
                        </p>

                    </div>
                </div>

                <div style="display: flex;">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Catatan</label>
                        <div class="field">
                            <textarea name="inp[catatanIndividu]" id="inp[catatanIndividu]" class="mediuminput" style="height: 50px"><?= $r['catatanIndividu'] ?></textarea>
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Bobot</label>
                        <div class="field">
                            <input type="text" name="inp[bobotIndividu]" id="inp[bobotIndividu]" class="vsmallinput" value="<?= $r['bobotIndividu'] ?>"> %
                        </div>
                        </p>

                        <p>
                            <label class="l-input-small2">Interval</label>
                        <div class="field">
                            <?= comboArray("inp[intervalIndividu]", ["Bulanan", "Tahunan"], $r['intervalIndividu']) ?>
                        </div>
                        </p>

                    </div>
                </div>

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

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Ambil Data&emsp;</legend>

                <p style="position: absolute; top: .5rem; right: 1rem;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Dari</label>
                <div class="field">
                    <?= comboData("SELECT t2.`id`, t2.`name` FROM `pen_pegawai` t1 JOIN `dta_pegawai` t2 ON t2.`id` = t1.`idPegawai` WHERE t1.`tipePenilaian` = '$par[type]' AND t1.`kodePenilaian` = '$par[idKode]' AND t2.`id` != '$par[idPegawai]'", "id", "name", "inp[from]", "", "", "", "250px", ""); ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small" style="width: 150px">Ke</label>
                    <span class="field">&nbsp;<?= getField("SELECT `name` FROM `emp` WHERE `id` = '$par[idPegawai]'") ?></span>
                </p>

            </fieldset>

        </form>
    </div>
    <?php
}

function insertSync()
{
    global $inp, $par, $cUsername;

    repField();

    $datas = getRows("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idPegawai` = '$inp[from]'");

    // im not recomending use this design
    foreach ($datas as $data) {

        $nextId = getField("SELECT `idIndividu` FROM `pen_sasaran_individu` ORDER BY `idIndividu` DESC LIMIT 1") + 1;

        $sql = "INSERT INTO `pen_sasaran_individu` SET

        `idIndividu` = '$nextId',
        `idPeriode` = '$par[period]',
        `idPegawai` = '$par[idPegawai]',
        `idSasaran` = '$data[idSasaran]',
        `tipeTarget` = '$data[tipeTarget]',
        `targetIndividu` = '$data[targetIndividu]',
        `keteranganTargetIndividu` = '$data[keteranganTargetIndividu]',
        `satuanIndividu` = '$data[satuanIndividu]',
        `satuanIndividu2` = '$data[satuanIndividu2]',
        `bobotIndividu` = '$data[bobotIndividu]',
        `intervalIndividu` = '$data[intervalIndividu]',
        `catatanIndividu` = '$data[catatanIndividu]',
        `settingIndividu` = '$data[settingIndividu]',
        `createTime` = '" . date("Y-m-d H:i:s") . "',
        `createBy` = '$cUsername'";

        db($sql);
    }

    echo "<script>alert('Data berhasil disalin')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "&par[mode]=det';</script>";
}

function insert()
{
    global $inp, $par, $cUsername;

    // insert without delete exist
    $white_list = [];

    foreach ($inp['objective'] as $key => $_) {
        $white_list[] = $key;
    }
    $white_list = implode(",", $white_list);

    if (!empty($white_list))
        db("DELETE FROM `pen_sasaran_individu` WHERE `idPegawai` = '$par[idPegawai]' AND `idPeriode` = '$par[period]' AND `idSasaran` NOT IN ($white_list)");

    $exist = getRows("SELECT `idSasaran` FROM `pen_sasaran_individu` WHERE `idPegawai` = '$par[idPegawai]' AND `idPeriode` = '$par[period]'", "idSasaran");

    foreach ($inp['objective'] as $key => $objective) {

        if (@$exist[$key])
            continue;

        $last_id = getField("SELECT `idIndividu` FROM `pen_sasaran_individu` ORDER BY `idIndividu` DESC LIMIT 1") + 1;

        $sql = "INSERT INTO `pen_sasaran_individu` SET

        `idIndividu` = '$last_id',
        `idPegawai` = '$par[idPegawai]',
        `idSasaran` = '$key',
        `idPeriode` = '$par[period]',
        `createTime` = '" . date('Y-m-d H:i:s') . "',
        `createBy` = '$cUsername'";

        db($sql);
    }

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "&par[mode]=det';</script>";
}

function update()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "UPDATE `pen_sasaran_individu` SET

    `tipeTarget` = '$inp[tipeTarget]',
    `keteranganTargetIndividu` = '$inp[keteranganTargetIndividu]',
    `targetIndividu` = '$inp[targetIndividu]',
    `satuanIndividu` = '$inp[satuanIndividu]',
    `satuanIndividu2` = '$inp[satuanIndividu2]',
    `bobotIndividu` = '$inp[bobotIndividu]',
    `intervalIndividu` = '$inp[intervalIndividu]',
    `catatanIndividu` = '$inp[catatanIndividu]',
    `settingIndividu` = '$inp[settingIndividu]',
    `settingIndividu` = '$inp[settingIndividu]',
    `updateTime` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'

     WHERE

    `idIndividu` = '$par[idIndividu]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idIndividu") . "&par[mode]=det';</script>";
}

function delete()
{
    global $par;

    db("DELETE FROM `pen_sasaran_individu` WHERE `idIndividu` = '$par[idIndividu]'");

    echo "<script>window.location='?" . getPar($par, "mode, idIndividu") . "&par[mode]=det';</script>";
}

<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

function getContent()
{
    global $par;

    switch ($par['mode']) {

        case "datas":
            datas();
            break;

        case "result":
            indexResult();
            break;

        case "print":
            indexResultPrint();
            break;

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $arrParameter;

    $type = getField("SELECT `kodeTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC LIMIT 1");
    $idKode = getField("SELECT `idKode` FROM `pen_setting_kode` WHERE `kodeTipe`= '$type' AND `statusKode` = 't' ORDER BY `idKode` ASC LIMIT 1");

    $period = getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory`= 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");
    $arr_month = getRows("SELECT * FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `kodeInduk` = '$period' ORDER BY `urutanData` ASC", "kodeData");

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <form action="" class="stdform">

            <div style="display: flex; margin-bottom: .5rem;">
                <div style="flex: 3">

                    <input type="text" id="fSearch" name="fSearch" style="width: 200px;" onkeyup=""/>

                    <?= comboData("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC", "kodeTipe", "namaTipe", "aSearch", "", $type, "onchange=\"refreshCode()\"", "200px", ""); ?>

                    <?= comboData("SELECT `idKode`, `subKode` FROM `pen_setting_kode` WHERE `kodeTipe` = '$type' AND statusKode = 't' ORDER BY `idKode` ASC", "idKode", "subKode", "bSearch", "", $idKode, "", "250px", ""); ?>

                </div>
                <div style="flex: 1; display: flex; justify-content: end">

                    <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "cSearch", "", $period, "", "200px", ""); ?>

                </div>
            </div>

        </form>

        <div style="overflow-x: auto">

            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
                <thead>
                <tr>
                    <th style="vertical-align:middle; min-width:20px;" rowspan="2">No.</th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2">Nama</th>
                    <th style="vertical-align:middle; min-width:100px;" rowspan="2">NPP</th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2"><?= $arrParameter[38] ?></th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2">Jabatan</th>
                    <th style="vertical-align:middle;" colspan="<?= sizeof($arr_month) ?>">Nilai</th>
                </tr>
                <tr>
                    <?php foreach ($arr_month as $month) : ?>
                        <th style="vertical-align: middle; min-width:75px;"><?= $month['kodeMaster'] ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

    <?= table(17, range(5, 17), "datas", "true", "", "datatable") ?>

    <script>

        codes = <?= json_encode(getRows("SELECT `kodeTipe`, `idKode`, `subKode` FROM `pen_setting_kode` WHERE statusKode = 't' ORDER BY `idKode` ASC", "idKode")) ?>

        function refreshCode() {

            parent_id = jQuery("#aSearch").val()
            jQuery("#bSearch").empty()

            for (index in codes) {

                if (codes[index].kodeTipe != parent_id)
                    continue

                jQuery("#bSearch").append(`<option value="${index}">${codes[index].subKode}</option>`)
            }

        }

    </script>
    <?php
}

function indexResult()
{
    global $s, $par, $arrTitle;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[year_id]' ORDER BY `urutanAspek`", "idAspek");
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[year_id]'");

    $appraisal = getRow("SELECT * FROM `pen_pegawai` WHERE `tipePenilaian` = '$par[type]' AND `idPegawai` = '$par[emp_id]'");
    $appraisal_type = getRow("SELECT * FROM `pen_tipe` WHERE `kodeTipe` = '$appraisal[tipePenilaian]'");
    $appraisal_code = getRow("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$appraisal[kodePenilaian]'");

    $master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");
    $master_kpi = arrayQuery("SELECT `id`, `komponen` FROM `master_kpi`");

    $emp = getRow("SELECT * FROM `dta_pegawai` WHERE `id` = '$par[emp_id]'");
    $emp_phist = getRow("SELECT * FROM `emp_phist` WHERE `parent_id` = '$par[emp_id]'");

    $par['month_id'] = $par['month_id'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `statusData` = 't' ORDER BY `urutanData` ASC LIMIT 1");

    $components = [];

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1.1rem;">

            <a class="btn btn1 btn_print" href="popup.php?<?= getPar($par, "mode") ?>&par[mode]=print" target="_blank">
                <span>Cetak</span>
            </a>
            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, type, code, year_id, month_id, emp_id, id") ?>';"/>

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
                            <label class="l-input-small">Unit Kerja</label>
                            <span class="field">&nbsp;<?= $master[$emp_phist['location']] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Jabatan</label>
                            <span class="field">&nbsp;<?= $emp['pos_name'] ?></span>
                        </p>

                    </div>
                </div>

            </fieldset>

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <div style="display: flex">
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Periode Tahun</label>
                            <span class="field">
                                &nbsp;
                                <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[period]'"); ?>
                            </span>
                        </p>

                        <p>
                            <label class="l-input-small2">Periode Realisasi</label>
                            <span class="field">
                                &nbsp;
                                <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[month_id]'"); ?>
                            </span>
                        </p>

                    </div>
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Periode Penilaian</label>
                            <span class="field">
                                &nbsp;
                                <?= getTanggal($appraisal['periodeStart']) ?: "0" ?>
                                &emsp;
                                <b>s.d.</b>
                                &emsp;
                                <?= getTanggal($appraisal['periodeEnd']) ?: "0" ?>
                            </span>
                        </p>

                    </div>
                </div>

            </fieldset>

        </form>

        <?php foreach ($arr_aspect as $aspect_id => $aspect) : ?>

            <br>

            <?php if ($aspect['aspekKode'] == '1') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[code]' AND `idPeriode` = '$par[year_id]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = '$perspective[idPrespektif]' AND `idKode` = '$par[code]' AND `idPeriode` = '$par[year_id]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idSasaran` = '$objective[idSasaran]' AND `idPeriode` = '$par[year_id]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[year_id]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
                        $result = $realization ? $realization['nilai'] : 0;

                        // push
                        $data['objectives'][] = [
                            'name' => $master_kpi[$objective['master_kpi_id']],
                            'unit' => $target['satuanIndividu'] . " " . $target['satuanIndividu2'],
                            'weight' => $target['bobotIndividu'],
                            'target' => $objective['targetSasaran'],
                            'realization' => $realization['realisasi'],
                            'achieve' => $realization['realisasi'] / $objective['targetSasaran'] * 100,
                            'value' => $realization['nilai'],
                            'result' => $result * ($target['bobotIndividu'] / 100)
                        ];

                        $data['weight'] += $target['bobotIndividu'];
                        $data['result'] += $result * ($target['bobotIndividu'] / 100);

                    }

                    $datas[] = $data;
                    $components[] = [
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }
                ?>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                    <tr>
                        <th style="min-width: 100px; vertical-align: middle;"><?= $aspect['namaAspek'] ?></th>
                        <th style="min-width: 150px; vertical-align: middle;">Komponen KPI</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Bobot</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Target</th>
                        <th rowspan="2" style="width: 150px; vertical-align: middle;">Realisasi</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Pencapaian</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai x Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr>
                            <td style="vertical-align: middle;" rowspan="<?= sizeof($data['objectives']) + 1 ?>">
                                <?= $data['name'] ?>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td><?= $object['name'] ?></td>
                                <td>
                                    <center><?= $object['unit'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['weight'] ?>%</center>
                                </td>
                                <td>
                                    <center><?= $object['target'] ?>%</center>
                                </td>
                                <td>
                                    <center><?= $object['realization'] ?>%</center>
                                </td>
                                <td>
                                    <center><?= round($object['achieve']) ?>%</center>
                                </td>
                                <td>
                                    <center><?= $object['value'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['result'] ?></center>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: rgba(0, 0, 0, 0.015)">
                            <td colspan="3" style="text-align: end">
                                <b>Jumlah Bobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['weight'] ?>%</center>
                                </b>
                            </td>
                            <td colspan="4" style="text-align: end">
                                <b>Jumlah Terbobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['result'] ?></center>
                                </b>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

            <?php if ($aspect['aspekKode'] == '2') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[code]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[code]' AND `idPrespektif` = '$perspective[idPrespektif]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[year_id]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[year_id]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
                        $result = $realization ? $realization['nilai'] : 0;

                        // push
                        $data['objectives'][] = [
                            'name' => $objective['uraianSasaran'],
                            'prof_1' => $objective['prof_1'],
                            'prof_2' => $objective['prof_2'],
                            'prof_3' => $objective['prof_3'],
                            'prof_4' => $objective['prof_4'],
                            'weight' => $target['bobotIndividu'],
                            'value' => $result,
                            'result' => $result * ($target['bobotIndividu'] / 100)
                        ];

                        $data['weight'] += $target['bobotIndividu'];
                        $data['result'] += $result * ($target['bobotIndividu'] / 100);

                    }

                    $datas[] = $data;
                    $components[] = [
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }
                ?>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                    <tr>
                        <th style="min-width: 100px; vertical-align: middle;"><?= $aspect['namaAspek'] ?></th>
                        <th style="min-width: 150px; vertical-align: middle;">Komponen KPI</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 1<br>JG 01 s/d JG 05</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 2<br>JG 06 s/d JG 09</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 3<br>JG 10 s/d JG 13</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 4<br>JG 14 s/d JG 16</th>
                        <th rowspan="2" style="width: 70px; vertical-align: middle;">Bobot</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai Prilaku Utama</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai x Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr>
                            <td style="vertical-align: middle;" rowspan="<?= sizeof($data['objectives']) + 1 ?>">
                                <?= $data['name'] ?>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td><?= $object['name'] ?></td>
                                <td>
                                    <center><?= $object['prof_1'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_2'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_3'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_4'] ?></center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['weight'] ?>%</center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['value'] ?></center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['result'] ?></center>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: rgba(0, 0, 0, 0.015)">
                            <td colspan="6" style="text-align: end">
                                <b>Jumlah Bobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['weight'] ?></center>
                                </b>
                            </td>
                            <td></td>
                            <td>
                                <b>
                                    <center><?= $data['result'] ?></center>
                                </b>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <thead>
            <tr>
                <th style="vertical-align: middle;">Komponen Penilaian</th>
                <th style="width: 150px; vertical-align: middle;">Bobot</th>
                <th style="width: 150px; vertical-align: middle;">Nilai</th>
                <th style="width: 150px; vertical-align: middle;">Nilai x Bobot</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($components as $component) : ?>
                <tr>
                    <td><?= $component['name'] ?></td>
                    <td>
                        <center><?= $component['weight'] ?>%</center>
                    </td>
                    <td>
                        <center><?= $component['result'] ?></center>
                    </td>
                    <td>
                        <center><?= $total += $component['result'] * ($component['weight'] / 100) ?></center>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: rgba(0, 0, 0, 0.015)">
                <td colspan="3" style="text-align: end">
                    <b>Nilai Kinerja Akhir</b>
                </td>
                <td>
                    <center><?= $total ?></center>
                </td>
            </tr>
            </tbody>
        </table>

        <br>

        <?php
        $total_background = "#000";
        foreach ($arr_conversions as $conversion) {

            if ($total >= $conversion['nilaiMin'] && $total <= $conversion['nilaiMax'])
                $total_background = $conversion['warnaKonversi'];

        }
        ?>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <tr>
                <th rowspan="3" style="vertical-align: middle; background-color: #fff; font-size: 15pt; color: #000;">YUDISIUM</th>
            </tr>
            <tr>
                <th width="200" style="background-color: #fff; color: #000;">Nilai Kinerja Akhir</th>
                <th width="200" rowspan="2" style="vertical-align: middle; background-color: #fff; color: #000;">Naik Skala 2</th>
            </tr>
            <tr>
                <th style="height: 70px; vertical-align: middle; background-color: <?= $total_background ?>; font-size: 15pt;"><?= $total ?></th>
            </tr>
            </thead>
        </table>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <th>Rating</th>
            <th width="200">Rentang Nilai Kinerja</th>
            <th width="200">Kategori Nilai Kinerja</th>
            </thead>
            <tbody>
            <?php foreach ($arr_conversions as $conversion) : ?>
                <tr>
                    <td><?= $conversion['penjelasanKonversi'] ?></td>
                    <td>
                        <center><?= $conversion['nilaiMin'] ?> - <?= $conversion['nilaiMax'] ?></center>
                    </td>
                    <td>
                        <div style="background-color: <?= $conversion['warnaKonversi'] ?>">&emsp;</div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <?php
}

function indexResultPrint()
{
    global $par;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[year_id]' ORDER BY `urutanAspek`", "idAspek");
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[year_id]'");

    $appraisal = getRow("SELECT * FROM `pen_pegawai` WHERE `tipePenilaian` = '$par[type]' AND `idPegawai` = '$par[emp_id]'");
    $appraisal_type = getRow("SELECT * FROM `pen_tipe` WHERE `kodeTipe` = '$appraisal[tipePenilaian]'");
    $appraisal_code = getRow("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$appraisal[kodePenilaian]'");

    $master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");
    $master_kpi = arrayQuery("SELECT `id`, `komponen` FROM `master_kpi`");

    $emp = getRow("SELECT * FROM `dta_pegawai` WHERE `id` = '$par[emp_id]'");
    $emp_phist = getRow("SELECT * FROM `emp_phist` WHERE `parent_id` = '$par[emp_id]'");

    $par['month_id'] = $par['month_id'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `statusData` = 't' ORDER BY `urutanData` ASC LIMIT 1");

    $components = [];

    ?>

    <div id="page" class="contentwrapper">

        <br>

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
                            <label class="l-input-small">Unit Kerja</label>
                            <span class="field">&nbsp;<?= $master[$emp_phist['location']] ?></span>
                        </p>

                        <p>
                            <label class="l-input-small">Jabatan</label>
                            <span class="field">&nbsp;<?= $emp['pos_name'] ?></span>
                        </p>

                    </div>
                </div>

            </fieldset>

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <div style="display: flex">
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Periode Tahun</label>
                            <span class="field">
                                &nbsp;
                                <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[year_id]'"); ?>
                            </span>
                        </p>

                        <p>
                            <label class="l-input-small2">Periode Realisasi</label>
                            <span class="field">
                                &nbsp;
                                <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[month_id]'"); ?>
                            </span>
                        </p>

                    </div>
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Periode Penilaian</label>
                            <span class="field">
                                &nbsp;
                                <?= getTanggal($appraisal['periodeStart']) ?: "0" ?>
                                &emsp;
                                <b>s.d.</b>
                                &emsp;
                                <?= getTanggal($appraisal['periodeEnd']) ?: "0" ?>
                            </span>
                        </p>

                    </div>
                </div>

            </fieldset>

        </form>

        <?php foreach ($arr_aspect as $aspect_id => $aspect) : ?>

            <br>

            <?php if ($aspect['aspekKode'] == '1') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[code]' AND `idPeriode` = '$par[year_id]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = '$perspective[idPrespektif]' AND `idKode` = '$par[code]' AND `idPeriode` = '$par[year_id]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idSasaran` = '$objective[idSasaran]' AND `idPeriode` = '$par[year_id]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[year_id]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
                        $result = $realization ? $realization['nilai'] : 0;

                        // push
                        $data['objectives'][] = [
                            'name' => $master_kpi[$objective['master_kpi_id']],
                            'unit' => $target['satuanIndividu'] . " " . $target['satuanIndividu2'],
                            'weight' => $target['bobotIndividu'],
                            'target' => $objective['targetSasaran'],
                            'realization' => $realization['realisasi'],
                            'achieve' => $realization['realisasi'] / $objective['targetSasaran'] * 100,
                            'value' => $realization['nilai'],
                            'result' => $result * ($target['bobotIndividu'] / 100)
                        ];

                        $data['weight'] += $target['bobotIndividu'];
                        $data['result'] += $result * ($target['bobotIndividu'] / 100);

                    }

                    $datas[] = $data;
                    $components[] = [
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }
                ?>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                    <tr>
                        <th style="min-width: 100px; vertical-align: middle;"><?= $aspect['namaAspek'] ?></th>
                        <th style="min-width: 150px; vertical-align: middle;">Komponen KPI</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Bobot (%)</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Target</th>
                        <th rowspan="2" style="width: 150px; vertical-align: middle;">Realisasi</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Pencapaian (%)</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai x Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr>
                            <td style="vertical-align: middle;" rowspan="<?= sizeof($data['objectives']) + 1 ?>">
                                <?= $data['name'] ?>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td><?= $object['name'] ?></td>
                                <td>
                                    <center><?= $object['unit'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['weight'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['target'] ?>%</center>
                                </td>
                                <td>
                                    <center><?= $object['realization'] ?>%</center>
                                </td>
                                <td>
                                    <center><?= round($object['achieve']) ?></center>
                                </td>
                                <td>
                                    <center><?= $object['value'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['result'] ?></center>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: rgba(0, 0, 0, 0.015)">
                            <td colspan="3" style="text-align: end">
                                <b>Jumlah Bobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['weight'] ?></center>
                                </b>
                            </td>
                            <td colspan="4" style="text-align: end">
                                <b>Jumlah Terbobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['result'] ?></center>
                                </b>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

            <?php if ($aspect['aspekKode'] == '2') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[code]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[code]' AND `idPrespektif` = '$perspective[idPrespektif]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[year_id]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[year_id]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
                        $result = $realization ? $realization['nilai'] : 0;

                        // push
                        $data['objectives'][] = [
                            'name' => $objective['uraianSasaran'],
                            'prof_1' => $objective['prof_1'],
                            'prof_2' => $objective['prof_2'],
                            'prof_3' => $objective['prof_3'],
                            'prof_4' => $objective['prof_4'],
                            'weight' => $target['bobotIndividu'],
                            'value' => $result,
                            'result' => $result * ($target['bobotIndividu'] / 100)
                        ];

                        $data['weight'] += $target['bobotIndividu'];
                        $data['result'] += $result * ($target['bobotIndividu'] / 100);

                    }

                    $datas[] = $data;
                    $components[] = [
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }
                ?>
                <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
                    <thead>
                    <tr>
                        <th style="min-width: 100px; vertical-align: middle;"><?= $aspect['namaAspek'] ?></th>
                        <th style="min-width: 150px; vertical-align: middle;">Komponen KPI</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 1<br>JG 01 s/d JG 05</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 2<br>JG 06 s/d JG 09</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 3<br>JG 10 s/d JG 13</th>
                        <th rowspan="2" style="width: 200px; vertical-align: middle;">Profisiensi 4<br>JG 14 s/d JG 16</th>
                        <th rowspan="2" style="width: 70px; vertical-align: middle;">Bobot (%)</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai Prilaku Utama</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Nilai x Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr>
                            <td style="vertical-align: middle;" rowspan="<?= sizeof($data['objectives']) + 1 ?>">
                                <?= $data['name'] ?>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td><?= $object['name'] ?></td>
                                <td>
                                    <center><?= $object['prof_1'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_2'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_3'] ?></center>
                                </td>
                                <td>
                                    <center><?= $object['prof_4'] ?></center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['weight'] ?></center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['value'] ?></center>
                                </td>
                                <td style="vertical-align: middle;">
                                    <center><?= $object['result'] ?></center>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr style="background-color: rgba(0, 0, 0, 0.015)">
                            <td colspan="6" style="text-align: end">
                                <b>Jumlah Bobot</b>
                            </td>
                            <td>
                                <b>
                                    <center><?= $data['weight'] ?></center>
                                </b>
                            </td>
                            <td></td>
                            <td>
                                <b>
                                    <center><?= $data['result'] ?></center>
                                </b>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <thead>
            <tr>
                <th style="vertical-align: middle;">Komponen Penilaian</th>
                <th style="width: 150px; vertical-align: middle;">Bobot</th>
                <th style="width: 150px; vertical-align: middle;">Nilai</th>
                <th style="width: 150px; vertical-align: middle;">Nilai x Bobot</th>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($components as $component) : ?>
                <tr>
                    <td><?= $component['name'] ?></td>
                    <td>
                        <center><?= $component['weight'] ?>%</center>
                    </td>
                    <td>
                        <center><?= $component['result'] ?></center>
                    </td>
                    <td>
                        <center><?= $total += $component['result'] * ($component['weight'] / 100) ?></center>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr style="background-color: rgba(0, 0, 0, 0.015)">
                <td colspan="3" style="text-align: end">
                    <b>Nilai Kinerja Akhir</b>
                </td>
                <td>
                    <center><?= $total ?></center>
                </td>
            </tr>
            </tbody>
        </table>

        <br>

        <?php
        $total_background = "#000";
        foreach ($arr_conversions as $conversion) {

            if ($total >= $conversion['nilaiMin'] && $total <= $conversion['nilaiMax'])
                $total_background = $conversion['warnaKonversi'];

        }
        ?>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <tr>
                <th rowspan="3" style="vertical-align: middle; background-color: #fff; font-size: 15pt; color: #000;">YUDISIUM</th>
            </tr>
            <tr>
                <th width="200" style="background-color: #fff; color: #000;">Nilai Kerja Akhir</th>
                <th width="200" rowspan="2" style="vertical-align: middle; background-color: #fff; color: #000;">Naik Skala 2</th>
            </tr>
            <tr>
                <th style="height: 70px; vertical-align: middle; background-color: <?= $total_background ?>; font-size: 15pt;"><?= $total ?></th>
            </tr>
            </thead>
        </table>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <th>Rating</th>
            <th width="200">Rentang Nilai Kinerja</th>
            <th width="200">Kategori Nilai Kinerja</th>
            </thead>
            <tbody>
            <?php foreach ($arr_conversions as $conversion) : ?>
                <tr>
                    <td><?= $conversion['penjelasanKonversi'] ?></td>
                    <td>
                        <center><?= $conversion['nilaiMin'] ?> - <?= $conversion['nilaiMax'] ?></center>
                    </td>
                    <td>
                        <div style="background-color: <?= $conversion['warnaKonversi'] ?>">&emsp;</div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <script>
        this.print()
    </script>
    <?php
}

function datas()
{
    global $par, $iSortCol_0, $sSortDir_0, $iDisplayStart, $iDisplayLength, $fSearch, $aSearch, $bSearch, $cSearch;

    $arr_types = arrayQuery("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe`");
    $arr_codes = arrayQuery("SELECT `idKode`, `subKode` FROM `pen_setting_kode`");
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$cSearch'");
    $arr_month = getRows("SELECT * FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `kodeInduk` = '$cSearch' ORDER BY `urutanData` ASC", "kodeData");

    $orders = ['t1.reg_no', 't1.name', 't1.reg_no'];
    $datas = [];
    $no = 0;

    $q_filter = "1 = 1 ";
    $q_filter .= $fSearch ? "AND (t1.`name` LIKE '%$fSearch%' OR t1.`reg_no` LIKE '%$fSearch%') " : "";
    $q_filter .= "AND t2.`tipePenilaian` = '$aSearch' ";
    $q_filter .= "AND t2.`kodePenilaian` = '$bSearch' ";
    $q_filter .= "AND t2.`tahunPenilaian` = '$cSearch' ";

    $q_order = $orders[$iSortCol_0] . " " . $sSortDir_0;
    $q_limit = "$iDisplayStart, $iDisplayLength";

    $res = db("SELECT * FROM `emp` t1 JOIN `pen_pegawai` t2 ON t2.`idPegawai` = t1.`id` WHERE $q_filter ORDER BY $q_order LIMIT $q_limit");
    $count = getField("SELECT COUNT(*) FROM `emp` t1 JOIN `pen_pegawai` t2 ON t2.`idPegawai` = t1.`id` WHERE $q_filter");

    while ($row = mysql_fetch_assoc($res)) {

        $no++;

        $results = getRows("SELECT * FROM `pen_hasil` WHERE `id_periode` = '$cSearch' AND `id_pegawai` = '$row[idPegawai]'", "id_bulan");

        $data = [
            "<div align='center'>$no</div>",
            "<div align='left'>" . strtoupper($row['name']) . "</div>",
            "<div align='center'>$row[reg_no]</div>",
            "<div align='left'>" . $arr_types[$row['tipePenilaian']] . "</div>",
            "<div align='left'>" . $arr_codes[$row['kodePenilaian']] . "</div>"
        ];

        foreach ($arr_month as $key => $month) {

            if ($result = $results[$key]) {

                if ($result['nilai'] == 0) {
                    $data[] = "<div align='center'>0</div>";
                    continue;
                }

                $color = "#000000";

                foreach ($arr_conversions as $value) {
                    if ($result['nilai'] >= $value['nilaiMin'] && $result['nilai'] <= $value['nilaiMax'])
                        $color = $value['warnaKonversi'];
                }

                $data[] = "<a href='#' style='color: #fff;' onclick='window.location=`index.php?" . getPar($par, "mode, type, code, year_id, month_id, emp_id, id") . "&par[mode]=result&par[type]=$aSearch&par[code]=$bSearch&par[year_id]=$cSearch&par[month_id]=$month[kodeData]&par[emp_id]=$row[idPegawai]&par[id]=$row[id]`'>
                                <div align='center' style='background-color: $color;'>$result[nilai]</div>
                            </a>";

                continue;
            }

            $data[] = "<div align='center'>-</div>";

        }

        $datas[] = $data;
    }

    echo json_encode([
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => $count,
        "aaData" => $datas
    ]);
}
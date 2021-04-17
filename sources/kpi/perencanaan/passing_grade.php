<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

function getContent()
{
    global $par;

    switch ($par['mode']) {

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $par;

    $locations = KPIMasterLokasi::orderBy('urutanTipe')->get();
    $par['lokasi_id'] = $par['lokasi_id'] ?: $locations->first()->kodeTipe;

    $areas = KPIMasterLokasiCabang::where('kodeTipe', $par['lokasi_id'])->get();
    $par['area_id'] = $par['area_id'] ?: $areas->first()->idKode;

    $periods = Master::where('kodeCategory', 'PRDT')->orderBy('namaData', 'DESC')->get();
    $par['periode_id'] = $par['periode_id'] ?: $periods->first()->kodeData;

    $kpi = KPIMasterLokasiCabang::where('kodeTipe', $par['lokasi_id'])->first();


    $arr_aspect = KPIMasterAspek::where('idPeriode', $par['periode_id'])->orderBy('urutanAspek')->get();
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[periode_id]'");

    $master_kpi = arrayQuery("SELECT `id`, `komponen` FROM `master_kpi`");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <fieldset style="padding: .5rem; border-radius: .3rem;">

            <form action="?<?= getPar($par, "mode,lokasi_id,area_id,periode_id") ?>" method="post" id="filter" class="stdform">

                <div style="display: flex">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small">Penilaian</label>
                        <div class="field">
                            <?= selectArray("par[lokasi_id]", "Penilaian", $locations, "kodeTipe", "namaTipe", $par['lokasi_id'], "", "chosen-select", "20rem") ?>
                        </div>
                        </p>

                        <p>
                            <label class="l-input-small">Kode Penilaian</label>
                        <div class="field">
                            <?= selectArray("par[area_id]", "Kode Penilaian", $areas, "idKode", "subKode", $par['area_id'], "", "chosen-select", "20rem") ?>
                        </div>
                        </p>

                        <p>
                            <label class="l-input-small">Periode</label>
                        <div class="field">
                            <?= selectArray("par[periode_id]", "Periode", $periods, "kodeData", "namaData", $par['periode_id'], "", "chosen-select", "10rem") ?>
                        </div>
                        </p>

                        <p>
                            <label class="l-input-small">Surat Keputusan</label>
                            <span class="field" style="padding-left: 0;">
                            <?php if ($kpi['skKode']) : ?>
                                <a title="Lihat"
                                   onclick="openBox('view.php?&par[tipe]=file_pen_kode&par[idKode]=<?= $kpi['idKode'] ?>', 900, 500);">
                                    <img style=" height:20px;" src="<?= getIcon($kpi['skKode']) ?>">
                                </a>
                            <?php else : ?>
                                &nbsp;
                                -
                            <?php endif; ?>
                        </span>
                        </p>

                    </div>
                    <div style="flex: 1"></div>
                </div>

            </form>

        </fieldset>

        <br>

        <?php foreach ($arr_aspect as $aspect_id => $aspect) : ?>

            <br>

            <?php if ($aspect['aspekKode'] == '1') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[lokasi_id]' AND `idKode` = '$par[area_id]' AND `idPeriode` = '$par[periode_id]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => $perspective['bobot'],
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = '$perspective[idPrespektif]' AND `idKode` = '$par[area_id]' AND `idPeriode` = '$par[periode_id]'");

                    foreach ($objectives as $objective) {

                        // push
                        $data['objectives'][] = [
                            'name' => $master_kpi[$objective['master_kpi_id']],
                            'target' => $objective['targetSasaran'] . " " . $objective['targetSasaran2'],
                            'scoring' => $objective['scoringSasaran'],
                            'measurement' => $objective['measurementSasaran'],
                        ];

                        $data['weight'] += $objective['bobotIndividu'];

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
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Target</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Scoring</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Measurement</th>
                        <th rowspan="2" style="width: 50px; vertical-align: middle;">Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr style="background-color: rgba(0, 0, 0, .1)">
                            <td style="vertical-align: middle;" colspan="5  ">
                                <?= $data['name'] ?>
                            </td>
                            <td>
                                <center><?= $data['weight'] ?>%</center>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td colspan="2"><?= $object['name'] ?></td>
                                <td>
                                    <?= $object['target'] ?>
                                </td>
                                <td>
                                    <?= $object['scoring'] ?>
                                </td>
                                <td>
                                    <?= $object['measurement'] ?>
                                </td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

            <?php if ($aspect['aspekKode'] == '2') : ?>
                <?php

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[lokasi_id]' AND `idKode` = '$par[area_id]' AND `idPeriode` = '$par[periode_id]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => $perspective['bobot'],
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$par[area_id]' AND `idPrespektif` = '$perspective[idPrespektif]'");

                    foreach ($objectives as $objective) {

                        // push
                        $data['objectives'][] = [
                            'name' => $objective['uraianSasaran'],
                            'prof_1' => $objective['prof_1'],
                            'prof_2' => $objective['prof_2'],
                            'prof_3' => $objective['prof_3'],
                            'prof_4' => $objective['prof_4'],
                            'target' => $objective['targetSasaran'] . " " . $objective['targetSasaran2'],
                            'scoring' => $objective['scoringSasaran'],
                            'measurement' => $objective['measurementSasaran'],
                        ];

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
                        <th rowspan="2" style="width: 70px; vertical-align: middle;">Target</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Scoring</th>
                        <th rowspan="2" style="width: 100px; vertical-align: middle;">Measurement</th>
                        <th rowspan="2" style="width: 50px; vertical-align: middle;">Bobot</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="min-width: 250px; vertical-align: middle;">Sasaran Kinerja KPI</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datas as $data) : ?>
                        <tr style="background-color: rgba(0, 0, 0, .1)">
                            <td style="vertical-align: middle;" colspan="9">
                                <?= $data['name'] ?>
                            </td>
                            <td>
                                <center><?= $data['weight'] ?>%</center>
                            </td>
                        </tr>
                        <?php foreach ($data['objectives'] as $object) : ?>
                            <tr>
                                <td colspan="2"><?= $object['name'] ?></td>
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
                                <td>
                                    <?= $object['target'] ?>
                                </td>
                                <td>
                                    <?= $object['scoring'] ?>
                                </td>
                                <td>
                                    <?= $object['measurement'] ?>
                                </td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php continue; ?>
            <?php endif; ?>

        <?php endforeach; ?>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <th>Rating</th>
            <th width="150">Rentang Nilai Kinerja</th>
            <th width="100">Nilai</th>
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
                        <center><?= $conversion['uraianKonversi'] ?></center>
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

        jQuery('#par\\[lokasi_id\\]').change(() => {
            jQuery('#filter').submit()
        })

        jQuery('#par\\[area_id\\]').change(() => {
            jQuery('#filter').submit()
        })

        jQuery('#par\\[periode_id\\]').change(() => {
            jQuery('#filter').submit()
        })

    </script>
    <?php
}
<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$path = "files/kpi/individu/";

$par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `kodeData` ASC LIMIT 1");
$arr_month = getRows("SELECT * FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `kodeInduk` = '$par[period]' ORDER BY `urutanData` ASC", "kodeData");

function getContent()
{
    global $s, $menuAccess, $par, $_submit;

    switch ($par['mode']) {

        case "datas":
            datas();
            break;

        case "det":
            indexObjective();
            break;

        case "result":
            indexResult();
            break;

        case "remove":
            if (isset($menuAccess[$s]['edit']))
                remove();
            else
                echo "Tidak ada akses";
            break;

        case "edit":
            if (isset($menuAccess[$s]['edit']))
                isset($_submit) ? update() : form();
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
    global $s, $arrTitle, $arrParameter, $arr_month;

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">
        <div style="overflow-x: auto">

            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
                <thead>
                <tr>
                    <th style="vertical-align:middle; min-width:20px;" rowspan="2">No.</th>
                    <th style="vertical-align:middle; min-width:30px;" rowspan="2">#</th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2">Nama</th>
                    <th style="vertical-align:middle; min-width:100px;" rowspan="2">NPP</th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2"><?= $arrParameter[38] ?></th>
                    <th style="vertical-align:middle; min-width:200px;" rowspan="2">Jabatan</th>
                    <th style="vertical-align:middle;" colspan="<?= sizeof($arr_month) ?>">Nilai</th>
                </tr>
                <tr>
                    <?php foreach ($arr_month as $month) : ?>
                        <th style="vertical-align:middle; min-width:75px;"><?= $month['kodeMaster'] ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

    <?= table(18, [5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18], "datas", "true", "", "datatable") ?>
    <?php
}

function datas()
{
    global $par, $arr_month, $iSortCol_0, $sSortDir_0, $iDisplayStart, $iDisplayLength;

    $arr_types = arrayQuery("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe`");
    $arr_codes = arrayQuery("SELECT `idKode`, `subKode` FROM `pen_setting_kode`");
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[period]'");


    $orders = ['t1.reg_no', 't1.name', 't1.reg_no'];
    $datas = [];
    $no = 0;

    $q_order = $orders[$iSortCol_0] . " " . $sSortDir_0;

    $q_filter = "1 = 1 ";

    $q_limit = "$iDisplayStart, $iDisplayLength";

    $res = db("SELECT * FROM `emp` t1 JOIN `pen_pegawai` t2 ON t2.`idPegawai` = t1.`id` WHERE $q_filter ORDER BY $q_order LIMIT $q_limit");
    $count = getField("SELECT COUNT(*) FROM `emp` t1 JOIN `pen_pegawai` t2 ON t2.`idPegawai` = t1.`id` WHERE $q_filter");

    while ($row = mysql_fetch_assoc($res)) {

        $no++;

        $results = getRows("SELECT * FROM `pen_hasil` WHERE `id_pegawai` = '$row[idPegawai]' AND `id_periode` = '$par[period]'", "id_bulan");

        $data = [
            "<div align=\"center\">$no</div>",
            "<div align='center'>
                <a class='detail' title='Realisasi' href='?" . getPar($par, "mode, id, emp_id, type, code") . "&par[mode]=det&par[id]=$row[id]&par[emp_id]=$row[idPegawai]&par[type]=$row[tipePenilaian]&par[code]=$row[kodePenilaian]'>
                    <span>View</span>
                </a>
            </div>",
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

                $button = "<a style=\"color: #fff;\" onclick=\"window.location='index.php?" . getPar($par, "mode, id, type, code, year_id, month_id, emp_id") . "&par[mode]=result&par[id]=$row[id]&par[emp_id]=$row[idPegawai]&par[type]=$row[tipePenilaian]&par[code]=$row[kodePenilaian]&par[year_id]=$par[period]&par[month_id]=$month[kodeData]'\">$result[nilai]</a>";
                $data[] = "<div align='center' style='background-color: $color;'>$button</div>";

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

function indexObjective()
{
    global $s, $par, $arrTitle, $menuAccess, $path, $tab;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`", "idAspek");

    $appraisal = getRow("SELECT * FROM `pen_pegawai` WHERE `tipePenilaian` = '$par[type]' AND `idPegawai` = '$par[emp_id]'");
    $appraisal_type = getRow("SELECT * FROM `pen_tipe` WHERE `kodeTipe` = '$appraisal[tipePenilaian]'");
    $appraisal_code = getRow("SELECT * FROM `pen_setting_kode` WHERE `idKode` = '$appraisal[kodePenilaian]'");

    $master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");

    $emp = getRow("SELECT * FROM `dta_pegawai` WHERE `id` = '$par[emp_id]'");
    $emp_phist = getRow("SELECT * FROM `emp_phist` WHERE `parent_id` = '$par[emp_id]'");

    $tab = $tab ?: key($arr_aspect);

    $par['month_id'] = $par['month_id'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `statusData` = 't' ORDER BY `urutanData` ASC LIMIT 1");

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1rem;">

            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, month_id, emp_id") ?>';"/>

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
                        <div class="field">
                            <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `statusData` = 't' ORDER BY `urutanData`", "kodeData", "namaData", "month_id", "", $par['month_id'], "", "200px", ""); ?>
                        </div>
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

        <br>

        <form method="post" action="?<?= getPar($par) ?>&_submit" id="form">

            <div style="position: relative;">

                <ul class="hornav" style="margin: 0;">
                    <?php foreach ($arr_aspect as $key => $value) : $selected = $tab == $key ? "current" : ""; ?>
                        <li class="<?= $selected ?>"><a href="#tab_<?= $value['idAspek'] ?>"><?= $value['namaAspek'] ?></a></li>
                    <?php endforeach; ?>
                </ul>

            </div>

            <?php foreach ($arr_aspect as $key => $aspect) : $selected = $tab == $key ? "block" : "none"; ?>

                <div id="tab_<?= $aspect['idAspek'] ?>" class="subcontent" style="display: <?= $selected ?>; overflow-x: auto;">

                    <!--default-->
                    <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" style="width: max-content; min-width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 20px; vertical-align: middle;">No.</th>
                            <th style="min-width: 400px; vertical-align: middle;">Sasaran</th>
                            <th style="width: 150px; vertical-align: middle;">Target Sasaran</th>
                            <th style="width: 150px; vertical-align: middle;">Realisasi</th>
                            <th style="width: 150px; vertical-align: middle;">Nilai</th>
                            <th style="width: 50px; vertical-align: middle;">File</th>
                            <th style="width: 50px; vertical-align: middle;">WOM</th>
                            <?php if (isset($menuAccess[$s]['edit'])) : ?>
                                <th style="width: 50px; vertical-align: middle">Kontrol</th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$appraisal[kodePenilaian]' AND `idAspek` = '$aspect[idAspek]' AND `status` = 't'");

                        foreach ($perspectives as $perspective) :
                            ?>
                            <tr style="background-color: #d9d9d9;">
                                <td colspan="8"><strong><?= strtoupper($perspective['namaPrespektif']) ?></strong></td>
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
                                    <td colspan="8"><?= ($key_indicator + 1) . ". " . strtoupper($indicator['uraianIndikator']) ?></td>
                                </tr>
                                <?php

                                $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$appraisal[kodePenilaian]' AND `idPrespektif` = '$perspective[idPrespektif]' AND `idIndikator` = '$indicator[kodeIndikator]' AND `idPeriode` = '$par[period]'");

                                foreach ($objectives as $key_objective => $objective) : ?>
                                    <?php

                                    $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idSasaran` = '$objective[idSasaran]' AND `idPeriode` = '$par[period]' AND `idPegawai` = '$emp[id]'");

                                    if (!$target)
                                        continue;

                                    $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");

                                    $file = empty($realization['file_upload']) ? "-" : "<a href='" . $path . $realization['file_upload'] . "' title='Preview'><img style='height:20px;' src='" . getIcon($realization['file_upload']) . "'></a>";

                                    $wom = "#ffffff00";

                                    if ($realization['realisasi'] >= $objective['nilaiSasaran_1a'] and $realization['realisasi'] <= $objective['nilaiSasaran_1b'])
                                        $wom = $objective['wom_1'];

                                    if ($realization['realisasi'] >= $objective['nilaiSasaran_2a'] and $realization['realisasi'] <= $objective['nilaiSasaran_2b'])
                                        $wom = $objective['wom_2'];

                                    if ($realization['realisasi'] >= $objective['nilaiSasaran_3a'] and $realization['realisasi'] <= $objective['nilaiSasaran_3b'])
                                        $wom = $objective['wom_3'];

                                    if ($realization['realisasi'] >= $objective['nilaiSasaran_4a'] and $realization['realisasi'] <= $objective['nilaiSasaran_4b'])
                                        $wom = $objective['wom_4'];

                                    if ($realization['realisasi'] >= $objective['nilaiSasaran_5a'] and $realization['realisasi'] <= $objective['nilaiSasaran_5b'])
                                        $wom = $objective['wom_5'];

                                    ?>

                                    <tr>
                                        <td colspan="2"><?= ($key_indicator + 1) . "." . ($key_objective + 1) ?>&emsp;<?= $objective['uraianSasaran'] ?></td>
                                        <td><?= $objective['targetSasaran'] ?> <?= $objective['targetSasaran2'] ?></td>
                                        <td><?= $target['satuanIndividu'] ?> <?= $realization['realisasi'] ?> <?= $target['satuanIndividu2'] ?></td>
                                        <td>
                                            <center><?= $realization['nilai'] ?></center>
                                        </td>
                                        <td>
                                            <center><?= $file ?></center>
                                        </td>
                                        <td>
                                            <div style="background-color: <?= $wom ?>">&emsp;</div>
                                        </td>
                                        <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                                            <td>
                                                <center>
                                                    <?php if ($menuAccess[$s]['edit']) : ?>
                                                        <a title="Ubah Data" class="edit"
                                                           onclick="openBox('popup.php?<?= getPar($par, "mode, appraisal_id, id_aspek, idSasaran") ?>&par[mode]=edit&par[appraisal_id]=<?= $appraisal['id'] ?>&par[aspect_id]=<?= $aspect['idAspek'] ?>&par[idSasaran]=<?= $target['idSasaran'] ?>&tab=<?= $aspect['idAspek'] ?>', 900, 650);">
                                                            <span>Edit</span>
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

        </form>
    </div>

    <script>
        jQuery("#month_id").change(() => {

            month = jQuery("#month_id").val()

            window.location = '?<?= getPar($par, "month_id") ?>&par[month_id]=' + month
        })
    </script>
    <?php
}

function indexResult()
{
    global $s, $par, $arrTitle;

    $arr_aspect = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`", "idAspek");
    $arr_conversions = getRows("SELECT * FROM `pen_setting_konversi` WHERE `idPeriode` = '$par[period]'");

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

        <div style="position: absolute; top: 1rem; right: 1rem;">

            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, month_id, emp_id") ?>';"/>

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
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = '$perspective[idPrespektif]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
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
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'name' => $perspective['namaPrespektif'],
                        'weight' => 0,
                        'value' => 0,
                        'objectives' => []
                    ];

                    $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = '$perspective[idPrespektif]'");

                    foreach ($objectives as $objective) {

                        $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$par[emp_id]'");

                        if (!$target)
                            continue;

                        $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
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

    <?php
}

function form()
{
    global $s, $par, $arrTitle, $tab;

    $objective = getRow("SELECT * FROM `pen_sasaran_obyektif` WHERE `idSasaran` = '$par[idSasaran]'");
    $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_aspek` = '$par[aspect_id]' AND `id_sasaran` = '$par[idSasaran]' AND `id_pegawai` = '$par[emp_id]'");

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <div style="margin-top: 10px">
            <?= getBread(ucwords($par['mode'] . " data")) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit&tab=<?= $tab ?>" name="form" id="form" class="stdform" onsubmit="return validation(document.form);" enctype="multipart/form-data">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" class="submit radius2" value="Simpan"/>
            </p>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Sasaran Obyektif&emsp;</legend>

                <p>
                    <label class="l-input-small">Sasaran</label>
                    <span class="field">
                    &nbsp;
                    <?= $objective['uraianSasaran'] ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Pencapaian Target</label>
                    <span class="field">
                    &nbsp;
                    <?= $objective['targetSasaran'] ?>
                    &nbsp;
                    <?= $objective['targetSasaran2'] ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Keterangan</label>
                    <span class="field">
                    &nbsp;
                    <?= $objective['keteranganSasaran'] ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Scoring</label>
                    <span class="field">
                    &nbsp;
                    <?= $objective['scoringSasaran'] ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Measurement</label>
                    <span class="field">
                    &nbsp;
                    <?= $objective['measurementSasaran'] ?>
                </span>
                </p>

            </fieldset>

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Sasaran Individu&emsp;</legend>

                <p>
                    <label class="l-input-small">Penilaian</label>
                    <span class="field">
                    &nbsp;
                    <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[period]'"); ?>
                    &nbsp;>&nbsp;
                    <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[month_id]'"); ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Tanggal Realisasi</label>
                <div class="field">
                    <input type="text" name="inp[tanggal_realisasi]" id="inp[tanggal_realisasi]" class="vsmallinput hasDatePicker" value="<?= getTanggal($realization['tanggal_realisasi']) ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Realisasi</label>
                <div class="field">
                    <input type="text" name="inp[realisasi]" id="inp[realisasi]" class="vsmallest" value="<?= $realization['realisasi'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Bukti Pencapaian</label>
                <div class="field">
                    <?php if (empty($realization['file_upload'])) : ?>
                        <input type="file" name="file" style="margin-top: 5px;"/>
                    <?php else : ?>
                        <a href="" target="_blank" title="Preview">
                            <img style="height: 20px;" src="<?= getIcon($realization['file_upload']) ?>">
                        </a>
                        &nbsp;
                        <a href="?<?= getPar($par, "mode"); ?>&par[mode]=remove" onclick="return confirm('Konfirmasi hapus file')">Delete</a>
                    <?php endif; ?>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Catatan</label>
                <div class="field">
                    <textarea name="inp[keterangan]" id="inp[keterangan]" class="mediuminput" style="height: 50px"><?= $realization['keterangan'] ?></textarea>
                </div>
                </p>

            </fieldset>

        </form>

        <table>

        </table>

    </div>


    <?php
}

function remove()
{
    global $par;

    $exist = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_pegawai` = '$par[emp_id]' AND `id_tahun` = '$par[period]'  AND `id_bulan` = '$par[month_id]'  AND `id_aspek` = '$par[aspect_id]'  AND `id_sasaran` = '$par[idSasaran]'");

    db("UPDATE `pen_realisasi_individu` SET `file_upload` = '' WHERE `id_realisasi` = '$exist[id_realisasi]'");

    $par['mode'] = 'edit';
    echo "<script>window.location='popup.php?" . getPar($par) . "'</script>";
}

function update()
{
    global $par, $inp, $cID, $path, $tab;

    $path_new = $par['emp_id'] . "/";
    $aspects = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");

    $exist = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_pegawai` = '$par[emp_id]' AND `id_tahun` = '$par[period]'  AND `id_bulan` = '$par[month_id]'  AND `id_aspek` = '$par[aspect_id]'  AND `id_sasaran` = '$par[idSasaran]'");

    $nilai = getNilaiSasaran($par['idSasaran'], $inp['realisasi']);

    if ($exist) {

        $old_file = getField("SELECT `file_upload` FROM `pen_realisasi_individu` WHERE `id_realisasi` = '$exist[id_realisasi]'");
        $file = customeUploadFile($_FILES['file'], $par['appraisal_id'] . "-" . date("Ymd-His"), $path . $path_new, $old_file);

        $sql = "UPDATE `pen_realisasi_individu` SET

        `tanggal_realisasi` = '" . setTanggal($inp['tanggal_realisasi']) . "',
        `realisasi` = '$inp[realisasi]',
        `nilai` = '$nilai',
        `file_upload` = '" . ($file ? $path_new . $file : "") . "',
        `keterangan` = '$inp[keterangan]',
        `update_date` ='" . date('Y-m-d H:i:s') . "',
        `update_by` = '$cID'

         WHERE

        `id_realisasi` = '$exist[id_realisasi]'";

    } else {

        $file = customeUploadFile($_FILES['file'], par['appraisal_id'] . "-" . date("Ymd-His"), $path . $path_new);

        $sql = "INSERT INTO `pen_realisasi_individu` SET
        `id_pen_pegawai` = '$par[appraisal_id]',
        `id_pegawai` = '$par[emp_id]',
        `id_tahun` = '$par[period]',
        `id_bulan` = '$par[month_id]',
        `id_aspek` = '$par[aspect_id]',
        `id_sasaran` = '$par[idSasaran]',
        `tanggal_realisasi` = '" . setTanggal($inp['tanggal_realisasi']) . "',
        `realisasi` = '$inp[realisasi]',
        `nilai` = '$nilai',
        `file_upload` = '" . ($file ? $path_new . $file : "") . "',
        `keterangan` = '$inp[keterangan]',
        `create_date` ='" . date('Y-m-d H:i:s') . "',
        `create_by` = '$cID'";

    }

    db($sql);

//    IDK for what

//    foreach ($aspects as $aspect) {
//
//        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idAspek` = '$aspect[idAspek]'");
//
//        foreach ($perspectives as $perspective) {
//
//            $perspective_[] = $perspective;
//
//            $objective_ids = [];
//            $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = $perspective[idPrespektif]");
//
//            foreach ($objectives as $objective) {
//                $objective_ids[] = $objective['idSasaran'];
//            }
//            $objective_ids = implode(",", $objective_ids);
//
//            $target_count = getField("SELECT COUNT(*) FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idPegawai` = '$par[emp_id]' AND `idSasaran` IN ($objective_ids)");
//            $target_value = getField("SELECT SUM(nilai/$target_count) FROM `pen_realisasi_individu` WHERE `id_pegawai` = '$par[emp_id]' AND `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]'  AND `id_aspek` = '$aspect[idAspek]'");
//            $target_value = $target_value ?: 0;
//
//            $exist = getField("SELECT `id_hasil` FROM `pen_hasil_detail` WHERE `id_pegawai` = '$par[emp_id]' AND `id_periode` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_prespektif` = '$perspective[idPrespektif]'");
//
//            if ($exist) {
//
//                $sql = "UPDATE INTO `pen_hasil_detail` SET
//                `nilai` = '$target_value'
//                WHERE
//                `id_hasil` = '$exist'
//                ";
//
//            } else {
//
//                $sql = "INSERT INTO `pen_hasil_detail` SET
//                `id_periode` = '$par[period]',
//                `id_bulan` = '$par[month_id]',
//                `id_pegawai` = '$par[emp_id]',
//                `id_prespektif` = '$perspective[idPrespektif]',
//                `nilai` = '$target_value'
//                ";
//
//            }
//
//            db($sql);
//
//        }
//
//    }

    $total = 0;

    foreach ($aspects as $aspect) {

        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idAspek` = '$aspect[idAspek]'");

        foreach ($perspectives as $perspective) {

            $value_perspective = 0;

            $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idPrespektif` = $perspective[idPrespektif]");

            foreach ($objectives as $objective) {

                $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$par[emp_id]'");
                $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_tahun` = '$par[period]' AND `id_bulan` = $par[month_id] AND `id_sasaran` = '$objective[idSasaran]' AND `id_pegawai` = '$par[emp_id]'");
                $result = $realization ? $realization['nilai'] : 0;

                $value_perspective += $result * ($target['bobotIndividu'] / 100);
            }

            $total += $value_perspective * ($perspective['bobot'] / 100);

        }

    }

    $exist = getField("SELECT `id_hasil` FROM `pen_hasil` WHERE `id_periode` = '$par[period]' AND `id_bulan` = $par[month_id] AND `id_pegawai` = '$par[emp_id]'");

    if ($exist) {

        $sql = "UPDATE `pen_hasil` SET
        
        `nilai` = '$total',
        `update_date` = '" . date('Y-m-d H:i:s') . "',
        `update_by` = '$cID'
        
        WHERE
        
        `id_hasil` = '$exist'";

    } else {

        $sql = "INSERT INTO `pen_hasil` SET
        
        `id_periode` = '$par[period]',
        `id_bulan` = '$par[month_id]',
        `id_pegawai` = '$par[emp_id]',
        `nilai` = '$total',
        `create_date` = '" . date('Y-m-d H:i:s') . "',
        `create_by` = '$cID'
        ";

    }

    db($sql);

    echo "<script>alert('Data berhasil disimpan');</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, appraisal_id, aspect_id, idSasaran") . "&par[mode]=det&tab=$tab';</script>";
}
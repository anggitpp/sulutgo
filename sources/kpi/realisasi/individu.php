<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$path = "files/kpi/individu/";

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
                        <th style="vertical-align:middle; min-width:75px;"><?= $month['kodeMaster'] ?></th>
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

//                if ($result['nilai'] == 0) {
//                    $data[] = "<div align='center'>0</div>";
//                    continue;
//                }

                $color = "#000000";

                foreach ($arr_conversions as $value) {
                    if ($result['nilai'] >= $value['nilaiMin'] && $result['nilai'] <= $value['nilaiMax'])
                        $color = $value['warnaKonversi'];
                }

                $data[] = "<a href='#' style='color: #fff;' onclick='window.location=`index.php?" . getPar($par, "mode, month_id, type, code, id, emp_id") . "&par[mode]=det&par[type]=$aSearch&par[code]=$bSearch&par[period]=$cSearch&par[month_id]=$key&par[emp_id]=$row[idPegawai]&par[id]=$row[id]`'>
                                <div align='center' style='background-color: $color;'>$result[nilai]</div>
                            </a>";

                continue;
            }

            $data[] = "<a href='#' style='color: #000;' onclick='window.location=`index.php?" . getPar($par, "mode, month_id, type, code, id, emp_id") . "&par[mode]=det&par[type]=$aSearch&par[code]=$bSearch&par[period]=$cSearch&par[month_id]=$key&par[emp_id]=$row[idPegawai]&par[id]=$row[id]`'>
                            <div align='center' style='background-color: #fff;'>0</div>
                        </a>";

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

            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, type, code, period, month_id, emp_id, id") ?>';"/>

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
                            <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `kodeInduk` = '$par[period]' AND `statusData` = 't' ORDER BY `urutanData`", "kodeData", "namaData", "month_id", "", $par['month_id'], "", "200px", ""); ?>
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
                            <th style="width: 50px; vertical-align: middle;">Bobot</th>
                            <th style="width: 150px; vertical-align: middle;">Target</th>
                            <th style="width: 150px; vertical-align: middle;">Realisasi</th>
                            <th style="width: 150px; vertical-align: middle;">Pencapaian</th>
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
                                <td colspan="2"><strong><?= strtoupper($perspective['namaPrespektif']) ?></strong></td>
                                <td>
                                    <center><strong><?= $perspective['bobot'] ?>%</strong></center>
                                </td>
                                <td colspan="7"></td>
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
                                    <td colspan="10"><?= ($key_indicator + 1) . ". " . strtoupper($indicator['uraianIndikator']) ?></td>
                                </tr>
                                <?php

                                $objectives = getRows("SELECT * FROM `pen_sasaran_obyektif` WHERE `idKode` = '$appraisal[kodePenilaian]' AND `idPrespektif` = '$perspective[idPrespektif]' AND `idIndikator` = '$indicator[kodeIndikator]' AND `idPeriode` = '$par[period]'");

                                foreach ($objectives as $key_objective => $objective) : ?>
                                    <?php

                                    $target = getRow("SELECT * FROM `pen_sasaran_individu` WHERE `idPeriode` = '$par[period]' AND `idSasaran` = '$objective[idSasaran]' AND `idPegawai` = '$emp[id]'");

                                    if (!$target)
                                        continue;

                                    $realization = getRow("SELECT * FROM `pen_realisasi_individu` WHERE `id_sasaran` = '$objective[idSasaran]' AND `id_tahun` = '$par[period]' AND `id_bulan` = '$par[month_id]' AND `id_pegawai` = '$emp[id]'");
                                    $achieve = $realization ? $realization['pencapaian'] : -1;

                                    $file = empty($realization['file_upload']) ? "-" : "<a target='_blank' href='" . $path . $realization['file_upload'] . "' title='Preview'><img style='height:20px;' src='" . getIcon($realization['file_upload']) . "'></a>";

                                    $conversion_color = getKPIKonversiWarna($objective['idSasaran'], $achieve);

                                    ?>
                                    <tr>
                                        <td colspan="2"><?= ($key_indicator + 1) . "." . ($key_objective + 1) ?>&emsp;<?= $objective['uraianSasaran'] ?></td>
                                        <td>
                                            <center><?= $target['bobotIndividu'] ?>%</center>
                                        </td>
                                        <td><?= $target['satuanIndividu'] ?> <?= $target['targetIndividu'] ?> <?= $objective['targetSasaran2'] ?></td>
                                        <td><?= $target['satuanIndividu'] ?> <?= $realization['realisasi'] ?> <?= $target['keteranganTargetIndividu'] ?> <?= $target['satuanIndividu2'] ?></td>
                                        <td>
                                            <center><?= round($realization['pencapaian'], 1) ?>%</center>
                                        </td>
                                        <td>
                                            <center><?= $realization['nilai'] ?></center>
                                        </td>
                                        <td>
                                            <center><?= $file ?></center>
                                        </td>
                                        <td>
                                            <div style="background-color: <?= $conversion_color ?>">&emsp;</div>
                                        </td>
                                        <?php if (isset($menuAccess[$s]['edit'])) : ?>
                                            <td>
                                                <center>
                                                    <?php if ($menuAccess[$s]['edit']) : ?>
                                                        <a title="Ubah Data" class="edit"
                                                           onclick="openBox('popup.php?<?= getPar($par, "mode, appraisal_id, id_aspek, idSasaran") ?>&par[mode]=edit&par[appraisal_id]=<?= $appraisal['id'] ?>&par[aspect_id]=<?= $aspect['idAspek'] ?>&par[idSasaran]=<?= $target['idSasaran'] ?>&tab=<?= $aspect['idAspek'] ?>', 900, 700);">
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

function form()
{
    global $s, $par, $arrTitle, $tab, $path;

    $employee = Pegawai::find($par['emp_id']);

    $objective = KPIMasterObyektif::find($par['idSasaran']);
    $target = $employee->kpiSetingObyektif()->where('idSasaran', $objective['idSasaran'])->first();

    $realization = $employee->kpiRealisasiHasilDetil()->where('id_tahun', $par['period'])->where('id_bulan', $par['month_id'])->where('id_aspek', $par['aspect_id'])->where('id_sasaran', $par['idSasaran'])->first();

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <div style="margin-top: 10px">
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
                    <?= $target['targetIndividu'] ?>
                    &nbsp;
                    <?= $target['keteranganTargetIndividu'] ?>
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
                    &nbsp;-&nbsp;
                    <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[month_id]'"); ?>
                </span>
                </p>

                <p>
                    <label class="l-input-small">Tanggal Realisasi</label>
                <div class="field">
                    <input type="text" name="inp[tanggal_realisasi]" id="inp[tanggal_realisasi]" class="vsmallinput hasDatePicker" value="<?= getTanggal($realization['tanggal_realisasi']) ?>">
                </div>
                </p>

                <div style="display: flex">
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Realisasi</label>
                        <div class="field">
                            <input type="text" name="inp[realisasi]" id="inp[realisasi]" class="vsmallinput" value="<?= $realization['realisasi'] ?>" onkeyup="updatePencapaian(this.value)">&nbsp; /
                            &nbsp;<b><?= $target['targetIndividu'] ?> <?= $target['keteranganTargetIndividu'] ?></b>
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1">

                        <p>
                            <label class="l-input-small2">Pencapaian</label>
                        <div class="field">
                            <input type="text" name="inp[pencapaian]" id="inp[pencapaian]" class="vsmallinput" value="<?= $realization['pencapaian'] ?>" readonly> <b>%</b>
                        </div>
                        </p>

                    </div>
                </div>

                <p>
                    <label class="l-input-small">Bukti Pencapaian</label>
                <div class="field">
                    <?php if (empty($realization['file_upload'])) : ?>
                        <input type="file" name="file" style="margin-top: 5px;"/>
                    <?php else : ?>
                        <a href="<?= $path . $realization['file_upload'] ?>" target="_blank" title="Preview">
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

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Kendala&emsp;</legend>

                <p>
                    <label class="l-input-small">Teknis</label>
                <div class="field">
                    <textarea name="inp[kendala_teknis]" id="inp[kendala_teknis]" class="mediuminput" style="height: 5rem"><?= $realization['kendala_teknis'] ?></textarea>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Non Teknis</label>
                <div class="field">
                    <textarea name="inp[kendala_teknis_non]" id="inp[kendala_teknis_non]" class="mediuminput" style="height: 5rem"><?= $realization['kendala_teknis_non'] ?></textarea>
                </div>
                </p>

            </fieldset>

        </form>

    </div>
    <script>

        value_target = <?= $target['targetIndividu'] ?: 0 ?>

        function updatePencapaian(value) {
            result = ((value_target * 2 - value) / value_target) * 100
            jQuery('#inp\\[pencapaian\\]').val(Math.round(result))
        }

    </script>
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

    $aspects = KPIMasterAspek::where('idPeriode', $par['period'])->orderBy('urutanAspek')->get();

    $employee = Pegawai::find($par['emp_id']);

    $exist = $employee->kpiRealisasiHasilDetil()->where('id_tahun', $par['period'])->where('id_bulan', $par['month_id'])->where('id_aspek', $par['aspect_id'])->where('id_sasaran', $par['idSasaran'])->first();

    $nilai = getKPIKonversiNilai($par['idSasaran'], $inp['pencapaian']);

    if ($exist) {

        $old_file = $exist['file_upload'];
        $file = customeUploadFile($_FILES['file'], $par['appraisal_id'] . "-" . date("Ymd-His"), $path . $path_new);

        $sql = "UPDATE `pen_realisasi_individu` SET

        `tanggal_realisasi` = '" . setTanggal($inp['tanggal_realisasi']) . "',
        `realisasi` = '$inp[realisasi]',
        `nilai` = '$nilai',
        `pencapaian` = '$inp[pencapaian]',
        `file_upload` = '" . ($file ? $path_new . $file : $old_file) . "',
        `keterangan` = '$inp[keterangan]',
        `kendala_teknis` = '$inp[kendala_teknis]',
        `kendala_teknis_non` = '$inp[kendala_teknis_non]',
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
        `pencapaian` = '$inp[pencapaian]',
        `nilai` = '$nilai',
        `file_upload` = '" . ($file ? $path_new . $file : "") . "',
        `keterangan` = '$inp[keterangan]',
        `kendala_teknis` = '$inp[kendala_teknis]',
        `kendala_teknis_non` = '$inp[kendala_teknis_non]',
        `create_date` ='" . date('Y-m-d H:i:s') . "',
        `create_by` = '$cID'";

    }

    db($sql);


    $total = 0;

    foreach ($aspects as $aspect) {

        $perspectives = KPIMasterPrespektif::where('idAspek', $aspect['idAspek'])->get();

        foreach ($perspectives as $perspective) {

            $value_perspective = 0;

            $objectives = KPIMasterObyektif::where('idPrespektif', $perspective['idPrespektif'])->get();

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
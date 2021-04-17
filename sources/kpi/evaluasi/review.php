<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

use Illuminate\Support\Facades\DB;

function getContent()
{
    global $par, $_submit;

    switch ($par['mode']) {

        case "datas":
            datas();
            break;

        case "result":
            indexResult();
            break;

        case "edit_technician":
            $_submit ? update() : formObstacleTecnician();
            break;

        case "edit_technician_non":
            $_submit ? update() : formObstacleTecnicianNon();
            break;

        case "add_agreed":
            $_submit ? insertAgreed() : formEvaluationAgreed();
            break;

        case "edit_agreed":
            $_submit ? updateAgreed() : formEvaluationAgreed();
            break;

        case "delete_agreed":
            deleteAgreed();
            break;

        case "edit_note_employee":
            $_submit ? updateEvaluationNote() : formEvaluationNoteEmployee();
            break;

        case "edit_note_leader":
            $_submit ? updateEvaluationNote() : formEvaluationNoteLeader();
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
                <div style="flex: none;">

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
    global $s, $arrTitle, $menuAccess, $par;

    $arr_aspect = KPIMasterAspek::where('idPeriode', $par['year_id'])->orderBy('urutanAspek')->get()->keyBy('idAspek');
    $arr_conversions = KPIMasterNilaiHasil::where('idPeriode', $par['year_id'])->get();

    $appraisal_type = KPIMasterLokasi::find($par['type']);
    $appraisal_code = $appraisal_type->cabang()->find($par['code']);

    $master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data`");
    $master_kpi = KPIMasterCategory::get()->keyBy('id')->map(function ($master) {
        return $master->komponen;
    });

    $par['month_id'] = $par['month_id'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDB' AND `statusData` = 't' ORDER BY `urutanData` ASC LIMIT 1");


    $appraisal = KPIPegawai::where('tipePenilaian', $par['type'])->where('kodePenilaian', $par['code'])->where('idPegawai', $par['emp_id'])->first();

    $emp = $appraisal->pegawai;
    $emp_phist = $emp->posisi;

    $realizations = $emp->kpiRealisasiHasilDetil()->where('id_tahun', $par['year_id'])->where('id_bulan', $par['month_id'])->get();
    $obstacle_tecnicians = $realizations->filter(function ($realization) {
        return $realization->kendala_teknis;
    });
    $obstacle_tecnician_nons = $realizations->filter(function ($realization) {
        return $realization->kendala_teknis_non;
    });

    $evaluation_agreeds = $emp->kpiEvaluasiKesepakatan()->where('tahun_id', $par['year_id'])->where('bulan_id', $par['month_id'])->get();
    $evaluation_note = $emp->kpiEvaluasiCatatan()->where('tahun_id', $par['year_id'])->where('bulan_id', $par['month_id'])->first();

    $components = [];

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1.1rem;">

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

        <?php
        foreach ($arr_aspect as $aspect_id => $aspect) {

            if ($aspect['aspekKode'] == '1') {

                $datas = [];
                $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[type]' AND `idKode` = '$par[code]' AND `idPeriode` = '$par[year_id]' AND `idAspek` = '$aspect[idAspek]'");

                foreach ($perspectives as $perspective) {

                    $data = [
                        'aspect' => $aspect['namaAspek'],
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
                            'id' => $realization['id_realisasi'],
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
                        'aspect' => $aspect['namaAspek'],
                        'aspect_weight' => $aspect['bobotAspek'],
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }

            }

            if ($aspect['aspekKode'] == '2') {

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
                            'id' => $realization['id_realisasi'],
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
                        'aspect' => $aspect['namaAspek'],
                        'aspect_weight' => $aspect['bobotAspek'],
                        'name' => $data['name'],
                        'weight' => $perspective['bobot'],
                        'result' => $data['result']
                    ];
                }

                continue;
            }

        }
        ?>
        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <thead>
            <tr>
                <th style="width: 30px; vertical-align: middle;">No</th>
                <th style="vertical-align: middle;">Komponen Penilaian</th>
                <th style="width: 150px; vertical-align: middle;">Bobot</th>
                <th style="width: 150px; vertical-align: middle;">Nilai</th>
                <th style="width: 150px; vertical-align: middle;">WOM</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $aspect = "";
            $aspect_no = 0;
            ?>
            <?php foreach ($components as $key => $component) : ?>

                <?php if ($component['aspect'] != $aspect) : ?>
                    <?php $no = 0; ?>
                    <?php $aspect = $component['aspect']; ?>
                    <tr style="background-color: rgba(0, 0, 0, 0.1)">
                        <td>
                            &nbsp;<?= ++$aspect_no ?>
                        </td>
                        <td><?= $component['aspect'] ?></td>
                        <td>
                            <center><?= $component['aspect_weight'] ?>%</center>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                <?php endif; ?>

                <?php @$no++ ?>
                <?php $total += $component['result'] * ($component['weight'] / 100); ?>
                <?php $wom = getWOMWithColor($par['year_id'], $component['result']); ?>
                <tr>
                    <td>
                        &nbsp;<?= $aspect_no ?>.<?= $no ?>
                    </td>
                    <td><?= $component['name'] ?></td>
                    <td>
                        <center><?= $component['weight'] ?>%</center>
                    </td>
                    <td>
                        <center><?= $component['result'] ?></center>
                    </td>
                    <td style="background-color: <?= $wom['color'] ?>; color: #fff">
                        <center><b><?= $wom['tag'] ?></b></center>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br>

        <div class="widgetbox">

            <div class="title" style="margin-bottom: 10px">
                <h3>Kendala, Hambatan dan Aktifitas Tindak Lanjut</h3>
            </div>

            <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
                <thead>
                <th width="50">No</th>
                <th>Kendala</th>
                <th>Tindak Lanjut</th>
                <th width="50">Jawab</th>
                </thead>
                <tbody>
                <tr style="background-color: rgba(0, 0, 0, 0.1)">
                    <td colspan="4">
                        TEKNIS
                    </td>
                </tr>
                <?php foreach ($obstacle_tecnicians as $key => $realization) : ?>
                    <tr>
                        <td>
                            <center><?= $key + 1 ?>.</center>
                        </td>
                        <td>
                            <?= $realization['kendala_teknis'] ?>
                        </td>
                        <td>
                            <?= $realization['kendala_teknis_lanjut'] ?>
                        </td>
                        <td>
                            <center>
                                <?php if ($menuAccess[$s]['edit']) : ?>
                                    <a title="Ubah Data"
                                       class="edit"
                                       onclick="openBox('popup.php?<?= getPar($par, "mode, result_id") ?>&par[mode]=edit_technician&par[realization_id]=<?= $realization['id_realisasi'] ?>', 650, 250);">
                                        <span>Tindak lanjut</span>
                                    </a>
                                <?php endif; ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: rgba(0, 0, 0, 0.1)">
                    <td colspan="4">
                        NON TEKNIS
                    </td>
                </tr>
                <?php foreach ($obstacle_tecnician_nons as $key => $realization) : ?>
                    <tr>
                        <td>
                            <center><?= $key + 1 ?>.</center>
                        </td>
                        <td>
                            <?= $realization['kendala_teknis_non'] ?>
                        </td>
                        <td>
                            <?= $realization['kendala_teknis_non_lanjut'] ?>
                        </td>
                        <td>
                            <center>
                                <?php if ($menuAccess[$s]['edit']) : ?>
                                    <a title="Ubah Data"
                                       class="edit"
                                       onclick="openBox('popup.php?<?= getPar($par, "mode, result_id") ?>&par[mode]=edit_technician_non&par[realization_id]=<?= $realization['id_realisasi'] ?>', 650, 250);">
                                        <span>Tindak lanjut</span>
                                    </a>
                                <?php endif; ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <br>

        <div class="widgetbox" style="position: relative;">

            <div class="title" style="margin-bottom: 10px">
                <h3>Kesepakatan Aktifitas Tindak Lanjut</h3>
            </div>

            <?php if ($menuAccess[$s]['edit']) : ?>
                <a title="Tambah Data"
                   class="btn btn1 btn_document"
                   onclick="openBox('popup.php?<?= getPar($par, "mode, agreement_id") ?>&par[mode]=add_agreed', 650, 450);"
                   style="position: absolute; top: -1rem; right: 0;">
                    <span>Tambah Data</span>
                </a>
            <?php endif; ?>

            <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
                <thead>
                <th width="50">No</th>
                <th>Aktifitas Periode Mendatang</th>
                <th>Jadwal</th>
                <th>Hal yang Diharapkan</th>
                <th>Keterangan</th>
                <th width="50">Kontrol</th>
                </thead>
                <tbody>
                <?php foreach ($evaluation_agreeds as $key => $agreed) : ?>
                    <tr>
                        <td>
                            <center><?= $key + 1 ?>.</center>
                        </td>
                        <td>
                            <?= $agreed['aktifitas'] ?>
                        </td>
                        <td>
                            <?= $agreed['rencana'] ?>
                        </td>
                        <td>
                            <?= $agreed['target'] ?>
                        </td>
                        <td>
                            <?= $agreed['keterangan'] ?>
                        </td>
                        <td>
                            <center>
                                <?php if ($menuAccess[$s]['edit']) : ?>
                                    <a title="Ubah Data"
                                       class="edit"
                                       onclick="openBox('popup.php?<?= getPar($par, "mode, agreement_id") ?>&par[mode]=edit_agreed&par[agreement_id]=<?= $agreed['id_tindakan'] ?>', 650, 450);">
                                        <span>Ubah</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ($menuAccess[$s]['delete']) : ?>
                                    <a title="Hapus Data"
                                       class="delete"
                                       href="?<?= getPar($par, "mode, agreement_id") ?>&par[mode]=delete_agreed&par[agreement_id]=<?= $agreed['id_tindakan'] ?>"
                                       onclick="return confirm(`Konfirmasi hapus kesepakatan`);">
                                        <span>Hapus</span>
                                    </a>
                                <?php endif; ?>
                            </center>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <br>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable">
            <thead>
            <th colspan="2">Catatan / Komentar dari Pegawai</th>
            <th colspan="2">Catatan / Komentar dari Atasan</th>
            </thead>
            <tr>
                <td>
                    <?= $evaluation_note['pegawai'] ?>
                </td>
                <td width="10">
                    <center>
                        <?php if ($menuAccess[$s]['edit']) : ?>
                            <a title="Ubah Data"
                               class="edit"
                               onclick="openBox('popup.php?<?= getPar($par, "mode, note_id") ?>&par[mode]=edit_note_employee&par[note_id]=<?= $evaluation_note['id_catatan'] ?>', 650, 300);">
                                <span>Ubah</span>
                            </a>
                        <?php endif; ?>
                    </center>
                </td>
                <td>
                    <?= $evaluation_note['atasan'] ?>
                </td>
                <td width="10">
                    <center>
                        <?php if ($menuAccess[$s]['edit']) : ?>
                            <a title="Ubah Data"
                               class="edit"
                               onclick="openBox('popup.php?<?= getPar($par, "mode, note_id") ?>&par[mode]=edit_note_leader&par[note_id]=<?= $evaluation_note['id_catatan'] ?>', 650, 300);">
                                <span>Ubah</span>
                            </a>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>
        </table>

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

    <?php
}

function formObstacleTecnician()
{
    global $par;

    $realization = KPIRealisasiIndividuDetil::find($par['realization_id']);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Tindak Lanjut Kendala Teknisi</h1>
        <div style="margin-top: 10px">
        </div>
    </div>

    <br>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>" name="form" id="form" class="stdform">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" name="_submit" class="submit radius2" value="Simpan">
            </p>

            <textarea id="inp[kendala_teknis_lanjut]" name="inp[kendala_teknis_lanjut]" class="mediuminput" placeholder="Tindak Lanjut" style="width:98%; height: 70px"><?= $realization['kendala_teknis_lanjut'] ?></textarea>

        </form>

    </div>

    <?php
}

function formObstacleTecnicianNon()
{
    global $par;

    $realization = KPIRealisasiIndividuDetil::find($par['realization_id']);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Tindak Lanjut Kendala Non Teknisi</h1>
        <div style="margin-top: 10px">
        </div>
    </div>

    <br>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>" name="form" id="form" class="stdform">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" name="_submit" class="submit radius2" value="Simpan">
            </p>

            <textarea id="inp[kendala_teknis_non_lanjut]" name="inp[kendala_teknis_non_lanjut]" class="mediuminput" placeholder="Tindak Lanjut" style="width:98%; height: 70px"><?= $realization['kendala_teknis_non_lanjut'] ?></textarea>

        </form>

    </div>
    <?php
}

function formEvaluationAgreed()
{
    global $par;

    $agreed = KPIPegawaiEvaluasiKesepakatan::find($par['agreement_id']);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Kesepakatan</h1>
        <div style="margin-top: 10px">
        </div>
    </div>

    <br>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>" name="form" id="form" class="stdform">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" name="_submit" class="submit radius2" value="Simpan">
            </p>

            <p>
                <label class="l-input-small">Rencana Jadwal</label>
            <div class="field">
                <input type="text" name="inp[rencana]" id="inp[rencana]" class="mediuminput" value="<?= $agreed['rencana'] ?>">
            </div>
            </p>

            <p>
                <label class="l-input-small">Aktifitas</label>
            <div class="field">
                <textarea name="inp[aktifitas]" id="inp[aktifitas]" class="mediuminput" style="height: 5rem"><?= $agreed['aktifitas'] ?></textarea>
            </div>
            </p>

            <p>
                <label class="l-input-small">Target Hasil</label>
            <div class="field">
                <textarea name="inp[target]" id="inp[target]" class="mediuminput" style="height: 5rem"><?= $agreed['target'] ?></textarea>
            </div>
            </p>

            <p>
                <label class="l-input-small">Keterangan</label>
            <div class="field">
                <textarea name="inp[keterangan]" id="inp[keterangan]" class="mediuminput" style="height: 5rem"><?= $agreed['keterangan'] ?></textarea>
            </div>
            </p>

        </form>

    </div>
    <?php
}

function formEvaluationNoteEmployee()
{
    global $par;

    $note = KPIPegawaiEvaluasiCatatan::find($par['note_id']);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Catatan / Komentar dari Pegawai</h1>
        <div style="margin-top: 10px">
        </div>
    </div>

    <br>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>" name="form" id="form" class="stdform">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" name="_submit" class="submit radius2" value="Simpan">
            </p>

            <textarea id="inp[pegawai]" name="inp[pegawai]" class="mediuminput" placeholder="Catatan / Komentar" style="width:98%; height: 70px"><?= $note['pegawai'] ?></textarea>

        </form>

    </div>
    <?php
}

function formEvaluationNoteLeader()
{
    global $par;

    $note = KPIPegawaiEvaluasiCatatan::find($par['note_id']);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">Catatan / Komentar dari Atasan</h1>
        <div style="margin-top: 10px">
        </div>
    </div>

    <br>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>" name="form" id="form" class="stdform">

            <p style="position: absolute; top:10px; right: 20px;">
                <input type="submit" name="_submit" class="submit radius2" value="Simpan">
            </p>

            <textarea id="inp[atasan]" name="inp[atasan]" class="mediuminput" placeholder="Catatan / Komentar" style="width:98%; height: 70px"><?= $note['atasan'] ?></textarea>

        </form>

    </div>
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

function insertAgreed()
{
    global $par, $inp, $cID;

    unset($inp['page']);

    DB::beginTransaction();

    try {

        $inp['id_pen_pegawai'] = $par['id'];
        $inp['id_pegawai'] = $par['emp_id'];
        $inp['tahun_id'] = $par['year_id'];
        $inp['bulan_id'] = $par['month_id'];
        $inp['create_by'] = $cID;

        KPIPegawaiEvaluasiKesepakatan::create($inp);

        DB::commit();

        echo "<script>alert('Persetujuan berhasil disimpan');</script>";

    } catch (\Exception $e) {

        dd($e);

        DB::rollBack();

        echo "<script>alert('Persetujuan gagal disimpan');</script>";

    }

    $par['mode'] = 'result';

    echo "<script>parent.window.location='index.php?" . getPar($par, "realization_id") . "';</script>";
}

function update()
{
    global $par, $inp, $cID;

    unset($inp['page']);

    $realization = KPIRealisasiIndividuDetil::find($par['realization_id']);

    DB::beginTransaction();

    try {

        $inp['update_by'] = $cID;

        $realization->update($inp);

        DB::commit();

        echo "<script>alert('Data berhasil disimpan');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Data gagal disimpan');</script>";

    }

    $par['mode'] = 'result';

    echo "<script>parent.window.location='index.php?" . getPar($par, "agreement_id") . "';</script>";
}

function updateAgreed()
{
    global $par, $inp, $cID;

    unset($inp['page']);

    $agreed = KPIPegawaiEvaluasiKesepakatan::find($par['agreement_id']);

    DB::beginTransaction();

    try {

        $inp['update_by'] = $cID;

        $agreed->update($inp);

        DB::commit();

        echo "<script>alert('Persetujuan berhasil diubah');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Persetujuan gagal diubah');</script>";

    }

    $par['mode'] = 'result';

    echo "<script>parent.window.location='index.php?" . getPar($par, "agreement_id") . "';</script>";
}

function updateEvaluationNote()
{
    global $par, $inp, $cID;

    DB::beginTransaction();

    unset($inp['page']);

    try {

        KPIPegawaiEvaluasiCatatan::updateOrCreate([
            'id_pen_pegawai' => $par['id'],
            'id_pegawai' => $par['emp_id'],
            'tahun_id' => $par['year_id'],
            'bulan_id' => $par['month_id']
        ], [
                'id_pen_pegawai' => $par['id'],
                'id_pegawai' => $par['emp_id'],
                'tahun_id' => $par['year_id'],
                'bulan_id' => $par['month_id'],
                'create_by' => $cID,
                'update_by' => $cID
            ] + $inp
        );

        DB::commit();

        echo "<script>alert('Catatan berhasil diubah');</script>";

    } catch (\Exception $e) {

        dd($e);

        DB::rollBack();

        echo "<script>alert('Catatan gagal diubah');</script>";

    }

    $par['mode'] = 'result';

    echo "<script>parent.window.location='index.php?" . getPar($par, "note_id") . "';</script>";
}

function deleteAgreed()
{
    global $par, $inp;

    unset($inp['page']);

    $agreed = KPIPegawaiEvaluasiKesepakatan::find($par['agreement_id']);

    DB::beginTransaction();

    try {

        $agreed->delete();

        DB::commit();

        echo "<script>alert('Persetujuan berhasil dihapus');</script>";

    } catch (\Exception $e) {

        DB::rollBack();

        echo "<script>alert('Persetujuan gagal dihapus');</script>";

    }

    $par['mode'] = 'result';

    echo "<script>parent.window.location='index.php?" . getPar($par, "agreement_id") . "';</script>";
}
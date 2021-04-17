<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

$arr_status = ["t" => "Aktif", "f" => "Tidak Aktif"];
$arr_status_image = ["t" => "<img src='styles/images/t.png' title='Aktif' />", "f" => "<img src='styles/images/f.png' title='Tidak Aktif' />"];

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
                index();
            break;

        case "det":
            indexIndicator();
            break;

        case "add_indicator":
            if (isset($menuAccess[$s]['add']))
                isset($_submit) ? insertIndicator() : formIndicator();
            else
                echo "Tidak ada akses";
            break;

        case "edit_indicator":
            if (isset($menuAccess[$s]['edit']))
                isset($_submit) ? updateIndicator() : formIndicator();
            else
                echo "Tidak ada akses";
            break;

        case "del_indicator":
            if (isset($menuAccess[$s]['delete']))
                deleteIndicator();
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
    global $s, $arrTitle, $menuAccess, $par, $arr_status_image;

    $par['idTipe'] = $par['idTipe'] ?: getField("SELECT `kodeTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC LIMIT 1");
    $par['idKode'] = $par['idKode'] ?: getField("SELECT `idKode` FROM `pen_setting_kode` WHERE `kodeTipe`= '$par[idTipe]' AND `statusKode` = 't' ORDER BY `idKode` ASC LIMIT 1");

    $par['period'] = $par['period'] ?: getField("SELECT `kodeData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` DESC LIMIT 1");

    $never_sync = getField("SELECT COUNT(*) FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[idTipe]' AND `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]'") == 0;

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <form method="post" action="?<?= getPar($par, "idTipe, idKode") ?>" id="filter">

            <div style="display: flex;">
                <div style="flex: 2; text-align: start;">

                    <?= comboData("SELECT `kodeTipe`, `namaTipe` FROM `pen_tipe` WHERE `statusTipe` = 't' ORDER BY `urutanTipe` ASC", "kodeTipe", "namaTipe", "par[idTipe]", "", $par['idTipe'], "", "200px", ""); ?>
                    <?= comboData("SELECT `idKode`, `subKode` FROM `pen_setting_kode` WHERE `kodeTipe` = '$par[idTipe]' AND statusKode = 't' ORDER BY `idKode` ASC", "idKode", "subKode", "par[idKode]", "", $par['idKode'], "", "250px", ""); ?>

                </div>
                <div style="flex: 1; text-align: end">

                    <?php if ($menuAccess[$s]['add']) : ?>
                        <?php if ($never_sync) : ?>
                            <a class="btn btn1 btn_inboxi" onclick="openBox(`popup.php?<?= getPar($par, "mode") ?>&par[mode]=sync`, 700, 270)"><span>Ambil Data</span></a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' ORDER BY `urutanData` ASC", "kodeData", "namaData", "par[period]", "", $par['period'], "", "200px", ""); ?>

                </div>
            </div>

        </form>

        <div style="height: 10px;"></div>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th width="200">Prespektif</th>
                <th width="50">Kode</th>
                <th>KPI</th>
                <th width="50">Bobot</th>
                <th width="50">Urut</th>
                <th width="50">Indikator</th>
                <th width="50">Status</th>
                <?php if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    <th width="50" style="vertical-align: middle">Kontrol</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $res = db("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]' ORDER BY `urutanAspek`");
            while ($row = mysql_fetch_assoc($res)) :
                ?>
                <tr style="background-color: #d9d9d9;">
                    <td colspan="8"><strong><?= $row['namaAspek'] ?></strong></td>
                    <td style="text-align: center;">
                        <a class="add" title="Tambah Data"
                           onclick="openBox('popup.php?<?= getPar($par, "mode, idAspek") ?>&par[mode]=add&par[idAspek]=<?= $row['idAspek'] ?>', 800, 480);"><span>Tambah</span></a>
                    </td>
                </tr>
                <?php
                $_res = db("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[idTipe]' AND `idKode` = '$par[idKode]' AND `idPeriode` = '$par[period]' AND `idAspek` = '$row[idAspek]' ORDER BY `urut`");
                $no = 0;
                while ($_row = mysql_fetch_assoc($_res)) : $no++ ?>
                    <?php $total = getField("SELECT COUNT(`kodeIndikator`) FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$_row[idPrespektif]'") ?>
                    <tr>
                        <td style="text-align: center;"><?= $no ?></td>
                        <td style="text-align: left;"><?= $_row['namaPrespektif'] ?></td>
                        <td style="text-align: center;"><?= $_row['kodeNama'] ?></td>
                        <td style="text-align: left;"><?= $_row['kpiPrespektif'] ?></td>
                        <td style="text-align: center;"><?= $_row['bobot'] ?>%</td>
                        <td style="text-align: center;"><?= $_row['urut'] ?></td>
                        <td style="text-align: center;">
                            <a href="?<?= getPar($par, "mode, idPrespektif") ?>&par[mode]=det&par[idAspek]=<?= $row['idAspek'] ?>&par[idPrespektif]=<?= $_row['idPrespektif'] ?>"><?= $total ?></a>
                        </td>
                        <td style="text-align: center;"><?= $arr_status_image[$_row['status']] ?></td>
                        <td style="text-align: center;">
                            <?php if ($menuAccess[$s]['edit']) : ?>
                                <a class="edit" title="Ubah Data"
                                   onclick="openBox('popup.php?<?= getPar($par, "mode, idPrespektif") ?>&par[mode]=edit&par[idPrespektif]=<?= $_row['idPrespektif'] ?>',800, 480);"><span>Edit</span></a>
                            <?php endif; ?>
                            <?php if ($menuAccess[$s]['delete']) : ?>
                                <a class="delete" title="Hapus Data" href="?<?= getPar($par, "mode, idPrespektif") ?>&par[mode]=del&par[idPrespektif]=<?= $_row['idPrespektif'] ?>"
                                   onclick="return confirm('Konfirmasi hapus data (termasuk anak)')">
                                    <span>Hapus</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endwhile; ?>
            </tbody>
        </table>

    </div>

    <script>

        jQuery("#par\\[idTipe\\]").change(() => {
            jQuery("#par\\[idKode\\]").remove()
            jQuery("#filter").submit()
        })

        jQuery("#par\\[idKode\\]").change(() => {
            jQuery("#filter").submit()
        })

        jQuery("#par\\[period\\]").change(() => {
            jQuery("#filter").submit()
        })

    </script>
    <?php
}

function indexIndicator()
{
    global $s, $par, $arrTitle, $menuAccess, $arr_status_image;

    $res = db("SELECT * FROM `pen_setting_prespektif` WHERE `idPrespektif` = '$par[idPrespektif]'");
    $r = mysql_fetch_assoc($res);

    $par['kodeAspek'] = $r['kodeAspek'];
    $par['kodePrespektif'] = $r['kodePrespektif'];

    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div class="contentwrapper">

        <div style="position: absolute; top: 1rem; right: 1rem;">

            <input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, idAspek, idPrespektif") ?>';"/>

        </div>

        <form action="" method="" class="stdform">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Penilaian&emsp;</legend>

                <p>
                    <label class="l-input-small">Penilaian</label>
                    <span class="field">&nbsp;<?= getField("SELECT concat(`namaKode`, ' -- ', `subKode`) FROM `pen_setting_kode` WHERE `idKode` = '$r[idKode]'"); ?></span>
                </p>

                <p>
                    <label class="l-input-small">Aspek</label>
                    <span class="field">&nbsp;<?= getField("SELECT `namaAspek` FROM `pen_setting_aspek` WHERE `idAspek` = '$par[idAspek]'"); ?></span>
                </p>

                <p>
                    <label class="l-input-small">Prespektif</label>
                    <span class="field">&nbsp;<?= $r['namaPrespektif']; ?></span>
                </p>

            </fieldset>

        </form>

        <br>

        <div style="display: flex;">
            <div style="flex: 2; text-align: start; display: flex; align-items: end;">

                <h3>Indikator</h3>

            </div>
            <div style="flex: 1; text-align: end">

                <?php if ($menuAccess[$s]['add']) : ?>
                    <a class="btn btn1 btn_document" onclick="openBox(`popup.php?<?= getPar($par, "mode") ?>&par[mode]=add_indicator`, 800, 300)"><span>Tambah Data</span></a>
                <?php endif; ?>

            </div>
        </div>

        <div style="background-color: #ccc; height: 1px; margin: 10px 0 10px 0;"></div>

        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
            <thead>
            <tr>
                <th width="20">No.</th>
                <th>Uraian</th>
                <th width="50">Urut</th>
                <th width="50">Status</th>
                <?php if (isset($menuAccess[$s]['add']) || isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) : ?>
                    <th width="50" style="vertical-align: middle">Kontrol</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $no = 0;

            $res = db("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' AND `levelIndikator` = 1 ORDER BY `urutanIndikator`");
            while ($row = mysql_fetch_assoc($res)) :
                $no++;
                ?>
                <tr>
                    <td widtd="20"><?= $no ?>.</td>
                    <td><?= $row['uraianIndikator'] ?></td>
                    <td widtd="50" style="text-align: center;"><?= $row['urutanIndikator'] ?></td>
                    <td widtd="50" style="text-align: center;"><?= $arr_status_image[$row['statusIndikator']] ?></td>
                    <td width="50" style="text-align: center;">
                        <?php if ($menuAccess[$s]['add']) : ?>
                            <a title="Tambah Data" class="add"
                               onclick="openBox('popup.php?<?= getPar($par, "mode, kodeIndikator") ?>&par[mode]=add_indicator&par[indukIndikator]=<?= $row['kodeIndikator'] ?>', 800, 300);">
                                <span>Add</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($menuAccess[$s]['edit']) : ?>
                            <a title="Ubah Data" class="edit"
                               onclick="openBox('popup.php?<?= getPar($par, "mode, kodeIndikator") ?>&par[mode]=edit_indicator&par[kodeIndikator]=<?= $row['kodeIndikator'] ?>', 800, 300);">
                                <span>Edit</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($menuAccess[$s]['delete']) : ?>
                            <a title="Hapus Data" href="?<?= getPar($par, "mode, kodeIndikator") ?>&par[mode]=del_indicator&par[kodeIndikator]=<?= $row['kodeIndikator'] ?>" class="delete"
                               onclick="return confirm('Konfirmasi hapus data (termasuk anak)')">
                                <span>Delete</span>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                $alfabets = 0;

                $_res = db("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' AND `indukIndikator` = '$row[kodeIndikator]' ORDER BY `urutanIndikator`");
                while ($_row = mysql_fetch_assoc($_res)) :
                    $alfabets++;
                    ?>
                    <tr>
                        <td widtd="20"></td>
                        <td><?= strtolower(numToAlpha($alfabets)) . ". " . $_row['uraianIndikator'] ?></td>
                        <td widtd="50" style="text-align: center;"><?= $_row['urutanIndikator'] ?></td>
                        <td widtd="50" style="text-align: center;"><?= $arr_status_image[$_row['statusIndikator']] ?></td>
                        <td width="80" style="text-align: center;">
                            <?php if ($menuAccess[$s]['edit']) : ?>
                                <a title="Ubah Data" class="edit"
                                   onclick="openBox('popup.php?<?= getPar($par, "mode, kodeIndikator") ?>&par[mode]=edit_indicator&par[indukIndikator]=<?= $_row['indukIndikator'] ?>&par[kodeIndikator]=<?= $_row['kodeIndikator'] ?>',800, 300);">
                                    <span>Edit</span>
                                </a>
                            <?php endif; ?>
                            <?php if ($menuAccess[$s]['delete']) : ?>
                                <a title="Hapus Data"
                                   href="?<?= getPar($par, "mode, kodeIndikator") ?>&par[mode]=del_indicator&par[indukIndikator]=<?= $_row['indukIndikator'] ?>&par[kodeIndikator]=<?= $_row['kodeIndikator'] ?>"
                                   class="delete"
                                   onclick="return confirm('Konfirmasi hapus data')">
                                    <span>Delete</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endwhile; ?>
            </tbody>
        </table>

    </div>
    <?php
}

function form()
{
    global $s, $par, $arrTitle, $arr_status;

    $res = db("SELECT * FROM `pen_setting_prespektif` WHERE `idPrespektif` = '$par[idPrespektif]'");
    $r = mysql_fetch_assoc($res);

    $r['bobot'] = $r['bobot'] ?: 0;
    $r['urut'] = $r['urut'] ?: getField("SELECT `urut` FROM `pen_setting_prespektif` WHERE `idAspek` = '$par[idAspek]' AND `idTipe` = '$par[idTipe]' AND `idKode` = '$par[idKode]' ORDER BY `urut` DESC LIMIT 1") + 1;
    $r['status'] = $r['status'] ?: "t";

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

                <legend>&emsp;Prespektif Indikator&emsp;</legend>

                <p style="position: absolute; top:10px; right: 20px;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Kode</label>
                <div class="field">
                    <input type="text" name="inp[kodeNama]" id="inp[kodeNama]" class="vsmallinput" value="<?= $r['kodeNama'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Prespektif</label>
                <div class="field">
                    <input type="text" name="inp[namaPrespektif]" id="inp[namaPrespektif]" class="mediuminput" value="<?= $r['namaPrespektif'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">KPI</label>
                <div class="field">
                    <textarea name="inp[kpiPrespektif]" id="inp[kpiPrespektif]" class="mediuminput" style="height: 50px"><?= $r['kpiPrespektif'] ?></textarea>
                </div>
                </p>

                <p>
                    <label class="l-input-small">Keterangan</label>
                <div class="field">
                    <textarea name="inp[keteranganPrespektif]" id="inp[keteranganPrespektif]" class="mediuminput" style="height: 50px"><?= $r['keteranganPrespektif'] ?></textarea>
                </div>
                </p>

                <div style="display: flex">
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small2">Bobot</label>
                        <div class="field">
                            <input type="text" name="inp[bobot]" id="inp[bobot]" class="vsmallinput" value="<?= $r['bobot'] ?>"> %
                        </div>
                        </p>

                    </div>
                    <div style="flex: 1;">

                        <p>
                            <label class="l-input-small">Urut</label>
                        <div class="field">
                            <input type="text" name="inp[urut]" id="inp[urut]" class="vsmallinput" value="<?= $r['urut'] ?>">
                        </div>
                        </p>

                    </div>
                </div>

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

    <script type="text/javascript">

        function warna(hex) {
            jQuery('#colorSelector span').css('backgroundColor', hex)
        }

        jQuery(document).ready(function ($) {
            warna('<?= $r['warnaKonversi'] ?>')
        })
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

            <p style="position: absolute; top: .5rem; right: 1rem;">
                <input type="submit" class="submit radius2" value="Simpan"/>
            </p>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Indikator&emsp;</legend>
                &nbsp;
                <?= getField("SELECT `namaTipe` FROM `pen_tipe` WHERE `kodeTipe` = '$par[idTipe]'"); ?>
                &nbsp;>&nbsp;
                <?= getField("SELECT `subKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'"); ?>
                &nbsp;>&nbsp;
                <?= getField("SELECT `namaData` FROM `mst_data` WHERE `kodeData` = '$par[period]'"); ?>

            </fieldset>

            <br>

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Ambil Dari&emsp;</legend>
                &nbsp;
                <?= getField("SELECT `namaTipe` FROM `pen_tipe` WHERE `kodeTipe` = '$par[idTipe]'"); ?>
                &nbsp;>&nbsp;
                <?= getField("SELECT `subKode` FROM `pen_setting_kode` WHERE `idKode` = '$par[idKode]'"); ?>
                &nbsp;>&nbsp;
                <?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = 'PRDT' AND `statusData` = 't' AND `kodeData` != '$par[period]' ORDER BY `urutanData` ASC", "kodeData", "namaData", "inp[from]", "", "", "", "150px", ""); ?>

            </fieldset>

        </form>
    </div>
    <?php
}

function formIndicator()
{
    global $par, $arr_status;

    $res = db("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' AND `kodeIndikator` = '$par[kodeIndikator]'");
    $r = mysql_fetch_assoc($res);

    $q_filter = $par['indukIndikator'] ? "AND `kodeIndikator` = '$par[kodeIndikator]' " : "";

    $r['urutanIndikator'] = $r['urutanIndikator'] ?: getField("SELECT `urutanIndikator` FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' $q_filter ORDER BY `urutanIndikator` DESC LIMIT 1") + 1;
    $r['statusIndikator'] = $r['statusIndikator'] ?: 't';

    ?>

    <div class="pageheader">
        <h1 class="pagetitle">Indikator</h1>
        <div style="margin-top: 10px">
            <?= getBread($par['mode']) ?>
        </div>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <form method="post" action="?<?= getPar($par) ?>&_submit" name="form" class="stdform" onsubmit="return validation(document.form);">

            <fieldset style="padding: .5rem; border-radius: .3rem;">

                <legend>&emsp;Indikator&emsp;</legend>

                <p style="position: absolute; top: .5rem; right: 1rem;">
                    <input type="submit" class="submit radius2" value="Simpan"/>
                </p>

                <p>
                    <label class="l-input-small">Uraian</label>
                <div class="field">
                    <input type="text" name="inp[uraianIndikator]" id="inp[uraianIndikator]" class="mediuminput" value="<?= $r['uraianIndikator'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Urutan</label>
                <div class="field">
                    <input type="text" name="inp[urutanIndikator]" id="inp[urutanIndikator]" class="vsmallinput" value="<?= $r['urutanIndikator'] ?>">
                </div>
                </p>

                <p>
                    <label class="l-input-small">Status</label>
                <div class="field fradio">
                    <?php foreach ($arr_status as $key => $value) : $checked = $r['statusIndikator'] == $key ? "checked" : ""; ?>
                        <input type="radio" <?= $checked ?> name="inp[statusIndikator]" id="<?= $key ?>" value="<?= $key ?>"> <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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

    $nextId = getField("SELECT `idPrespektif` FROM `pen_setting_prespektif` ORDER BY `idPrespektif` DESC LIMIT 1") + 1;

    $sql = "INSERT INTO `pen_setting_prespektif` SET

    `idPrespektif` = '$nextId',
    `idTipe` = '$par[idTipe]',
    `idKode` = '$par[idKode]',
    `idAspek` = '$par[idAspek]',
    `idPeriode` = '$par[period]',
    `kodeNama` = '$inp[kodeNama]',
    `namaPrespektif` = '$inp[namaPrespektif]',
    `kpiPrespektif` = '$inp[kpiPrespektif]',
    `keteranganPrespektif` = '$inp[keteranganPrespektif]',
    `bobot` = '$inp[bobot]',
    `urut` = '$inp[urut]',
    `status` = '$inp[status]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idPrespektif") . "';</script>";
}

function insertSync()
{
    global $inp, $par, $cUsername;

    repField();

    db("DELETE FROM `pen_setting_aspek` WHERE `idPeriode` = '$par[period]'");

    $aspects = getRows("SELECT * FROM `pen_setting_aspek` WHERE `idPeriode` = '$inp[from]'");

    foreach ($aspects as $aspect) {

        $aspect_last_id = getField("SELECT `idAspek` FROM `pen_setting_aspek` ORDER BY `idAspek` DESC LIMIT 1") + 1;

        $sql = "INSERT INTO `pen_setting_aspek` SET
        `idAspek` = '$aspect_last_id',
        `idPeriode` = '$par[period]',
        `namaAspek` = '$aspect[namaAspek]',
        `penilaianAspek` = '$aspect[penilaianAspek]',
        `keteranganAspek` = '$aspect[keteranganAspek]',
        `aspekKode` = '$aspect[aspekKode]',
        `urutanAspek` = '$aspect[urutanAspek]',
        `targetAspek` = '$aspect[targetAspek]',
        `bobotAspek` = '$aspect[bobotAspek]',
        `statusAspek` = '$aspect[statusAspek]',
        `createDate` = '" . date('Y-m-d H:i:s') . "',
        `createBy` = '$cUsername'
        ";

        db($sql);

        $perspectives = getRows("SELECT * FROM `pen_setting_prespektif` WHERE `idTipe` = '$par[idTipe]' AND `idKode` = '$par[idKode]' AND `idPeriode` = '$inp[from]' AND `idAspek` = '$aspect[idAspek]'");

        foreach ($perspectives as $perspective) {

            $perspective_last_id = getField("SELECT `idPrespektif` FROM `pen_setting_prespektif` ORDER BY `idPrespektif` DESC LIMIT 1") + 1;

            $sql = "INSERT INTO `pen_setting_prespektif` SET

            `idPrespektif` = '$perspective_last_id',
            `idTipe` = '$par[idTipe]',
            `idKode` = '$par[idKode]',
            `idPeriode` = '$par[period]',
            `idAspek` = '$aspect_last_id',
            `kodeNama` = '$perspective[kodeNama]',
            `namaPrespektif` = '$perspective[namaPrespektif]',
            `kpiPrespektif` = '$perspective[kpiPrespektif]',
            `keteranganPrespektif` = '$perspective[keteranganPrespektif]',
            `bobot` = '$perspective[bobot]',
            `urut` = '$perspective[urut]',
            `status` = '$perspective[status]',
            `createDate` = '" . date("Y-m-d H:i:s") . "',
            `createBy` = '$cUsername'";

            db($sql);


            $details = getRows("SELECT * FROM `pen_setting_prespektif_detail` WHERE `idPrespektif` = '$perspective[idPrespektif]'");

            foreach ($details as $detail) {

                $sql = "INSERT INTO `pen_setting_prespektif_detail` SET

                `idPrespektif` = '$perspective_last_id',
                `kodeDetail` = '$detail[kodeDetail]',
                `kodeTipe` = '$detail[kodeTipe]',
                `nilaiDetail` = '$detail[nilaiDetail]',
                `createDate` = '" . date("Y-m-d H:i:s") . "',
                `createBy` = '$cUsername'";

                db($sql);

            }


            $indicators = getRows("SELECT * FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$perspective[idPrespektif]'");

            foreach ($indicators as $indicator) {

                $sql = "INSERT INTO `pen_setting_prespektif_indikator` SET

                `idPrespektif` = '$perspective_last_id',
                `kodeIndikator` = '$indicator[kodeIndikator]',
                `indukIndikator` = '$indicator[indukIndikator]',
                `uraianIndikator` = '$indicator[uraianIndikator]',
                `statusIndikator` = '$indicator[statusIndikator]',
                `levelIndikator` = '$indicator[levelIndikator]',
                `urutanIndikator` = '$indicator[urutanIndikator]',
                `createDate` = '" . date("Y-m-d H:i:s") . "',
                `createBy` = '$cUsername'";

                db($sql);

            }

        }

    }

    echo "<script>alert('Data berhasil disalin')</script>";
    echo "<script>parent.window.location='index.php?" . getPar($par, "mode") . "';</script>";
}

function insertIndicator()
{
    global $inp, $par, $cUsername;

    repField();

    $nextId = getField("SELECT `kodeIndikator` FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' ORDER BY `kodeIndikator` DESC LIMIT 1") + 1;

    $inp['levelIndikator'] = empty($par['indukIndikator']) ? 1 : 2;

    $sql = "INSERT INTO `pen_setting_prespektif_indikator` SET

    `idPrespektif` = '$par[idPrespektif]',
    `kodeIndikator` = '$nextId',
    `indukIndikator` = '$par[indukIndikator]',
    `uraianIndikator` = '$inp[uraianIndikator]',
    `statusIndikator` = '$inp[statusIndikator]',
    `levelIndikator` = '$inp[levelIndikator]',
    `urutanIndikator` = '$inp[urutanIndikator]',
    `createDate` = '" . date("Y-m-d H:i:s") . "',
    `createBy` = '$cUsername'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, indukIndikator, kodeIndikator") . "&par[mode]=det';</script>";
}

function update()
{
    global $inp, $par, $cUsername;

    repField();

    $sql = "UPDATE `pen_setting_prespektif` SET

    `kodeNama` = '$inp[kodeNama]',
    `namaPrespektif` = '$inp[namaPrespektif]',
    `kpiPrespektif` = '$inp[kpiPrespektif]',
    `keteranganPrespektif` = '$inp[keteranganPrespektif]',
    `bobot` = '$inp[bobot]',
    `urut` = '$inp[urut]',
    `status` = '$inp[status]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'

     WHERE

    `idPrespektif` = '$par[idPrespektif]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, idPrespektif") . "';</script>";
}

function updateIndicator()
{
    global $inp, $par, $cUsername;

    repField();

    $inp['levelIndikator'] = empty($par['indukIndikator']) ? 1 : 2;

    $sql = "UPDATE `pen_setting_prespektif_indikator` SET

    `indukIndikator` = '$par[indukIndikator]',
    `uraianIndikator` = '$inp[uraianIndikator]',
    `statusIndikator` = '$inp[statusIndikator]',
    `levelIndikator` = '$inp[levelIndikator]',
    `urutanIndikator` = '$inp[urutanIndikator]',
    `updateDate` ='" . date('Y-m-d H:i:s') . "',
    `updateBy` = '$cUsername'

     WHERE

    `idPrespektif` = '$par[idPrespektif]' AND `kodeIndikator` = '$par[kodeIndikator]'";

    if (db($sql))
        echo "<script>alert('Data berhasil disimpan');</script>";
    else
        echo "<script>alert('Data gagal disimpan');</script>";

    echo "<script>parent.window.location='index.php?" . getPar($par, "mode, indukIndikator, kodeIndikator") . "&par[mode]=det';</script>";
}

function delete()
{
    global $par;

    db("DELETE FROM `pen_setting_prespektif` WHERE `idPrespektif` = '$par[idPrespektif]'");
    db("DELETE FROM `pen_setting_prespektif_detail` WHERE `idPrespektif` = '$par[idPrespektif]'");
    db("DELETE FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]'");

    echo "<script>window.location='?" . getPar($par, "mode, idPrespektif") . "';</script>";
}

function deleteIndicator()
{
    global $par;

    db("DELETE FROM `pen_setting_prespektif_indikator` WHERE `idPrespektif` = '$par[idPrespektif]' AND (`kodeIndikator` = '$par[kodeIndikator]' OR `indukIndikator` = '$par[kodeIndikator]')");

    echo "<script>window.location='?" . getPar($par, "mode, indukIndikator, kodeIndikator") . "&par[mode]=det';</script>";
}

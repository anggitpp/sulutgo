<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function lihat()
{
    global $par, $arrTitle, $s, $ui;

    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $arrSeleksi = array("602" => "Panggilan", "603" => "Psikotest", "604" => "Wawancara HR", "605" => "Wawancara User", "606" => "MCU", "607" => "Hasil");
    $arrStatus = arrayQuery("SELECT kodeData, namaData FROM mst_data where kodeCategory = 'R07'");

    $arrData[""] = 0;

    $sql = "SELECT id, administrasi FROM rec_applicant where year(tgl_input) = '$par[tahunData]'";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $arrData["Pelamar"]++;
        if ($r[administrasi] == "t") {
            $arrData["Administrasi"]++;
        }
    }

    $sql = "SELECT id, phase_id, sel_status, month(sel_date) as bulanData FROM rec_selection_appl where year(sel_date) = '$par[tahunData]'";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $arrData["Bulan"][$r[phase_id]][$r[bulanData]]++;
        $arrData["Seleksi"][$r[phase_id]]++;
        $arrData["Status"][$r[phase_id]][$r[sel_status]]++;
    }

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" action="" method="post" class="stdform">
            <p class="btnSave">
                <?= comboYear("par[tahunData]", $par[tahunData], 5, "onchange=\"document.getElementById('form').submit();\""); ?>
            </p>
        </form>
        <br clear="all" />
        <table style="width:100%;border-collapse:separate;border-spacing:20px;">
            <tr>
                <td width="30%"><?= $ui->createDashboardBox("Total Pelamar", "", $arrData["Pelamar"], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("Administrasi", "", $arrData["Administrasi"], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("Psikotest", "", $arrData["Seleksi"][603], "", "box2") ?></td>
            </tr>
            <tr>
                <td width="30%"><?= $ui->createDashboardBox("Wawancara HR", "", $arrData["Seleksi"][604], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("Wawancara User", "", $arrData["Seleksi"][605], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("MCU", "", $arrData["Seleksi"][606], "", "box2") ?></td>
            </tr>
        </table>
        <div class="widgetbox">
            <div class="title">
                <h3>DATA SELEKSI PER BULAN</h3>
            </div>
        </div>
        <div id="chartSeleksi"></div>

        <br clear="all" />

        <table style="width:100%">
            <tr>
                <td style="width:50%">
                    <div class="widgetbox">
                        <div class="title">
                            <h3>WAWANCARA HR</h3>
                        </div>
                    </div>
                    <div id="chartHR"></div>
                </td>
                <td style="width:50%">
                    <div class="widgetbox">
                        <div class="title">
                            <h3>WAWANCARA USER</h3>
                        </div>
                    </div>
                    <div id="chartUser"></div>
                </td>
            </tr>
        </table>

        <br clear="all" />

        <div class="widgetbox">
            <div class="title">
                <h3>HASIL</h3>
            </div>
        </div>
        <table style="width:100%;">
            <tr>
                <td width="30%"><?= $ui->createDashboardBox("Diterima", "", $arrData["Status"][607][601], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("Tidak Diterima", "", $arrData["Status"][607][600], "", "box2") ?></td>
                <td width="30%"><?= $ui->createDashboardBox("Belum di Proses", "", $arrData["Status"][607][0], "", "box2") ?></td>
            </tr>
        </table>
    </div>

    <style>
        #chartSeleksi {
            width: 100%;
            height: 400px;
        }

        #chartHR {
            width: 100%;
            height: 300px;
        }

        #chartUser {
            width: 100%;
            height: 300px;
        }
    </style>
    <script type="text/javascript">
        AmCharts.makeChart("chartSeleksi", {
            "type": "serial",
            "categoryField": "category",
            "startDuration": 1,
            "theme": "light",
            "integersOnly": true,
            "export": {
                "enabled": true
            },
            "categoryAxis": {
                "gridPosition": "start"
            },
            "trendLines": [],
            "graphs": [
                <?php
                    $no = 0;
                    foreach ($arrSeleksi as $key => $value) {
                        $no++;
                        ?> {
                        "balloonText": "[[title]] of [[category]]:[[value]]",
                        "bullet": "round",
                        "id": "AmGraph-<?= $no ?>",
                        "title": "<?= $value ?>",
                        "valueField": "column-<?= $no ?>"
                    },
                <?php
                    }
                    ?>
            ],
            "guides": [],
            "valueAxes": [{
                "id": "ValueAxis-1",
                "title": "Jumlah"
            }],
            "allLabels": [],
            "balloon": {},
            "legend": {
                "enabled": true,
                "useGraphSettings": true
            },
            "titles": [{
                "id": "Title-1",
                "size": 15,
                "text": ""
            }],
            "dataProvider": [
                <?php
                    for ($i = 1; $i <= 12; $i++) {
                        ?> {
                        "category": "<?= getBulan($i) ?>",
                        <?php
                                $no = 0;
                                foreach ($arrSeleksi as $key => $value) {
                                    $no++;
                                    $dataUsulan = empty($arrData["Bulan"][$key][$i]) ? 0 : $arrData["Bulan"][$key][$i];
                                    ?> "column-<?= $no ?>": <?= $dataUsulan ?>,
                        <?php }   ?>
                    },
                <?php } ?>
            ]
        });
        AmCharts.makeChart("chartHR", {
            "type": "pie",
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
            "titleField": "pendidikan",
            "valueField": "data",
            "fontSize": 12,
            "theme": "chalk",
            "allLabels": [],
            "balloon": {},
            "titles": [],
            "export": {
                "enabled": true
            },
            "dataProvider": [
                <?php
                    foreach ($arrStatus as $key => $value) {
                        ?> {
                        "pendidikan": "<?= $value ?>",
                        "data": "<?= $arrData["Status"][604][$key] ?>"
                    },
                <?php
                    }
                    ?>
            ]
        });
        AmCharts.makeChart("chartUser", {
            "type": "pie",
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
            "titleField": "pendidikan",
            "valueField": "data",
            "fontSize": 12,
            "theme": "chalk",
            "allLabels": [],
            "balloon": {},
            "titles": [],
            "export": {
                "enabled": true
            },
            "dataProvider": [
                <?php
                    foreach ($arrStatus as $key => $value) {
                        ?> {
                        "pendidikan": "<?= $value ?>",
                        "data": "<?= $arrData["Status"][605][$key] ?>"
                    },
                <?php
                    }
                    ?>
            ]
        });
    </script>

<?php
}

function getContent($par)
{
    switch ($par[mode]) {
        default:
            $text = lihat();
            break;
    }
    return $text;
}

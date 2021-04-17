<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function lihat()
{
    global $par, $arrTitle, $s, $ui;

    $par[bulanData] = empty($par[bulanData]) ? date('m') : $par[bulanData];
    $par[tahunData] = empty($par[tahunData]) ? date('Y') : $par[tahunData];

    $jmlUmur["< 50"] = 0;
    $jmlUmur["41-50"] = 0;
    $jmlUmur["31-40"] = 0;
    $jmlUmur["> 31"] = 0;

    $arrGender["M"] = 0;
    $arrGender["F"] = 0;

    $dataPendidikan[] = 0;

    $dataPegawai = 0;
    $dataDiterima = 0;
    $dataLolos = 0;

    $arrPendidikan = arrayQuery("select kodeData, namaData from mst_data where kodeCategory = 'R11' and statusData = 't'");

    $sql = "SELECT t1.id, t1.gender, TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()) umur, (SELECT x1.edu_type FROM rec_applicant_edu x1 where x1.parent_id= t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduType, t1.emp_id, t2.id as idSeleksi from rec_applicant t1 left join rec_selection_appl t2 on (t1.id = t2.parent_id AND t2.phase_id = '607' AND t2.sel_status = '601')";
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $dataPendidikan[$r[eduType]]++;

        if ($r[umur] < 31)
            $jmlUmur["> 31"]++;
        else if ($r[umur] <= 41)
            $jmlUmur["31-40"]++;
        else if ($r[umur] <= 51)
            $jmlUmur["41-50"]++;
        else
            $jmlUmur["< 50"]++;

        $arrGender["$r[gender]"]++;
        $dataPegawai++;
        if (!empty($r[emp_id])) {
            $dataDiterima++;
        }
        if (!empty($r[idSeleksi])) {
            $dataLolos++;
        }
    }

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
        <span class="pagedesc">&nbsp;</span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form id="form" name="form" action="" method="post" class="stdform">
            <p style="position: absolute; right: 20px; top: 4px;">

            </p>
        </form>
        <table style="width:100%">
            <tr style="width:100%">
                <td style="width:30%;">
                    <?= $ui->createDashboardBox("Data Pelamar", "", $dataPegawai, "", "box2"); ?>
                </td>
                <td style="width:30%;">
                    <?= $ui->createDashboardBox("Lulus Seleksi", "", $dataLolos, "", "box2"); ?>
                </td>
                <td style="width:30%;">
                    <?= $ui->createDashboardBox("Diterima", "", $dataDiterima, "", "box2"); ?>
                </td>
            </tr>
        </table>
        <br clear="all" />
        <div id="chartUmur"></div>
        <br clear="all" />
        <table style="width:100%;position:relative;">
            <tr>
                <td style="width:50%;">
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA PENDIDIKAN</h3>
                        </div>
                    </div>
                    <div id="chartPendidikan"></div>
                </td>
                <td style="width:50%; position:absolute;">
                    <div class="widgetbox">
                        <div class="title">
                            <h3>DATA GENDER</h3>
                        </div>
                    </div>
                    <ul class="toplist">
                        <?php
                            $totGender = array_sum($arrGender);
                            if (is_array($arrGender)) {
                                reset($arrGender);
                                while (list($idGender, $jmlGender) = each($arrGender)) {
                                    $persenGender = $jmlGender / $totGender * 100;
                                    ?>
                                <li>
                                    <div>
                                        <span class="one_fourth">
                                            <span class="left">
                                                <img src="styles/images/<?= strtolower($idGender) ?>gender.jpg">
                                            </span>
                                        </span>
                                        <span class="three_fourth last">
                                            <span class="right">
                                                <h1><?= getAngka($persenGender) ?> %</h1>
                                                <span class="title" style="margin-top:10px;"><?= getAngka($jmlGender) ?> Orang</span>
                                            </span>
                                        </span>
                                        <br clear="all">
                                    </div>
                                </li>
                        <?php
                                }
                            }
                            ?>
                    </ul>
                </td>
            </tr>
        </table>
    </div>

    <style>
        #chartUmur {
            width: 100%;
            height: 200px;
        }

        #chartPendidikan {
            width: 100%;
            height: 300px;
        }
    </style>
    <script type="text/javascript">
        AmCharts.makeChart("chartUmur", {
            "type": "serial",
            "categoryField": "category",
            "rotate": true,
            "startDuration": 1,
            "categoryAxis": {
                "gridPosition": "start"
            },
            "trendLines": [],
            "export": {
                "enabled": true
            },
            "graphs": [
                <?php
                    $no = 0;
                    foreach ($jmlUmur as $key => $value) {
                        $no++;
                        ?> {
                        "balloonText": "[[title]] of [[category]]:[[value]]",
                        "fillAlphas": 1,
                        "id": "AmGraph-<?= $no ?>",
                        "title": "Umur <?= $key ?>",
                        "type": "column",
                        "valueField": "column-<?= $no ?>"
                    },
                <?php }
                    ?>
            ],
            "guides": [],
            "valueAxes": [{
                "id": "ValueAxis-1",
                "stackType": "100%",
                "title": ""
            }],
            "allLabels": [],
            "balloon": {},
            "legend": {
                "enabled": true,
                "useGraphSettings": true
            },
            "titles": [{
                "id": "Title-1",
                "size": 20,
                "text": "Pegawai Berdasarkan Umur"
            }],
            "dataProvider": [{
                "category": "Umur (%)",
                <?php
                    $no = 0;
                    foreach ($jmlUmur as $key => $value) {
                        $no++;
                        ?> "column-<?= $no ?>": <?= $value ?>,
                <?php
                    }
                    ?>
            }]
        });
        AmCharts.makeChart("chartPendidikan", {
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
                    foreach ($arrPendidikan as $key => $value) {
                        ?> {
                        "pendidikan": "<?= $value ?>",
                        "data": "<?= $dataPendidikan[$key] ?>"
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
    global $db, $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        default:
            $text = lihat();
            break;
    }
    return $text;
}

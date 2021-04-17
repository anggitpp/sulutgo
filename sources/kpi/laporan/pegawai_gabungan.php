<?php
if (!isset($menuAccess[$s]['view'])) echo "<script>logout();</script>";

function getContent()
{
    global $s, $menuAccess, $par;

    switch ($par['mode']) {

        case "datas":
            datas();
            break;

        case "export":
            export();
            break;

        default:
            index();
            break;

    }

}

function index()
{
    global $s, $arrTitle, $arrParameter, $par;

    $locations = KPIMasterLokasi::where('statusTipe', 't')->orderBy('urutanTipe')->get();
    $location_id = $locations->first()->kodeTipe;

    $branches = KPIMasterLokasiCabang::where('kodeTipe', $location_id)->where('statusKode', 't')->orderBy('idKode')->get();

    $periods = Master::where('kodeCategory', 'PRDT')->where('statusData', 't')->orderBy('urutanData', 'desc')->get();
    $period_id = $periods->first()->kodeData;

    $months = Master::where('kodeCategory', 'PRDB')->where('statusData', 't')->where('kodeInduk', $period_id)->orderBy('urutanData', 'asc')->get();

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

                    <input type="text" id="search" style="width: 200px;"/>

                    &nbsp;

                    <?= selectArray("combo1", "Tipe", $locations, "kodeTipe", "namaTipe", $location_id, "", "", "15rem", "onchange=\"refreshCode()\""); ?>
                    <?= selectArray("combo2", "Kode", $branches, "idKode", "subKode", "", "", "", "15rem"); ?>

                </div>
                <div style="flex: unset;">

                    <?= selectArray("combo4", "Bulan", $months, "kodeData", "namaData", "", "", "", "10rem", ""); ?>
                    &nbsp;
                    <?= selectArray("combo3", "Periode", $periods, "kodeData", "namaData", "", "", "", "10rem", ""); ?>

                </div>
            </div>

            <div style="position:absolute; top: .8rem; right: 1.2rem">
                <a href="#"
                   class="btn btn1 btn_inboxi"
                   onclick="exports()">
                    <span>Export Data</span>
                </a>
            </div>

        </form>

        <div style="overflow-x: auto">

            <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
                <thead>
                <tr>
                    <th rowspan="3" style="vertical-align:middle;" width="30">No.</th>
                    <th rowspan="3" style="vertical-align:middle;">Nama</th>
                    <th rowspan="3" width="70" style="vertical-align:middle;">NPP</th>
                    <th rowspan="2" style="vertical-align:middle;">Nilai Target / Kinerja</th>
                    <th rowspan="2" style="vertical-align:middle;">Nilai Perilaku</th>
                    <th rowspan="3" width="70" style="vertical-align:middle;">Total (A)</th>
                    <th colspan="5" style="vertical-align:middle;">Pengurangan Absensi</th>
                    <th rowspan="2" colspan="4" style="vertical-align:middle;">Pengurangan SP</th>
                    <th rowspan="2" width="120" style="vertical-align:middle;">Nilai Kinerja Akhir</th>
                    <th rowspan="3" width="50" style="vertical-align:middle;">Rangking</th>
                    <th rowspan="3" width="50" style="vertical-align:middle;">Yudisium</th>
                </tr>
                <tr>
                    <th width="50" style="vertical-align:middle;">T</th>
                    <th width="50" style="vertical-align:middle;">S1</th>
                    <th width="50" style="vertical-align:middle;">I</th>
                    <th width="50" style="vertical-align:middle;">A</th>
                    <th rowspan="2" width="70" style="vertical-align:middle;">Total (B)</th>
                </tr>
                <tr>
                    <th width="100" style="vertical-align:middle;">(1-40)</th>
                    <th width="100" style="vertical-align:middle;">(1-10)</th>
                    <th width="50" style="vertical-align:middle;">0.5/3 Hari</th>
                    <th width="50" style="vertical-align:middle;">0.5/Hari</th>
                    <th width="50" style="vertical-align:middle;">1/Hari</th>
                    <th width="50" style="vertical-align:middle;">3/Hari</th>
                    <th width="50" style="vertical-align:middle;">Teguran/SP1</th>
                    <th width="50" style="vertical-align:middle;">SP2</th>
                    <th width="50" style="vertical-align:middle;">SP3</th>
                    <th width="70" style="vertical-align:middle;">Total (C)</th>
                    <th width="50" style="vertical-align:middle;">(A) - (B) - (C)</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

    <?= table(18, range(3, 18), "datas", "true", "", "datatable") ?>

    <script>

        codes = <?= KPIMasterLokasiCabang::where('statusKode', 't')->orderBy('idKode')->get()->keyBy('idKode')->toJson(); ?>

        function refreshCode() {

            parent_id = jQuery("#combo1").val()

            jQuery("#combo2").empty()

            for (index in codes) {

                if (codes[index].kodeTipe != parent_id)
                    continue

                jQuery("#combo2").append(`<option value="${index}">${codes[index].subKode}</option>`)
            }

        }

        function exports() {

            search = jQuery('#search').val()
            combo1 = jQuery('#combo1').val()
            combo2 = jQuery('#combo2').val()
            combo3 = jQuery('#combo3').val()
            combo4 = jQuery('#combo4').val()

            jQuery("body").append(`<iframe src='ajax.php?<?= getPar($par, 'mode') ?>&par[mode]=export&search=${search}&combo1=${combo1}&combo2=${combo2}&combo3=${combo3}&combo4=${combo4}' style='display: none;'></iframe>`)
        }

    </script>
    <?php
}

function datas()
{
    global $par, $iSortCol_0, $sSortDir_0, $iDisplayStart, $iDisplayLength, $search, $combo1, $combo2, $combo3, $combo4;

    $no = 0;
    $datas = [];
    $aspects = KPIMasterAspek::where('idPeriode', $combo3)->orderBy('urutanAspek')->get()->keyBy('idAspek');

    $orders = ['t2.reg_no', 't2.name', 't2.reg_no'];

    $q_filter = "1 = 1 ";
    $q_filter .= $search ? "AND (t2.`name` LIKE '%$search%' OR t2.`reg_no` LIKE '%$search%') " : "";
    $q_filter .= " AND t1.`tipePenilaian` = '$combo1'";
    $q_filter .= " AND t1.`kodePenilaian` = '$combo2'";
    $q_filter .= " AND t1.`tahunPenilaian` = '$combo3'";

    $q_order = "ORDER BY {$orders[$iSortCol_0]} $sSortDir_0";
    $q_limit = "LIMIT $iDisplayStart, $iDisplayLength";

    $count = getField("SELECT COUNT(*) FROM `pen_pegawai` t1 JOIN `emp` t2 JOIN `emp_phist` t3 ON t2.`id` = t1.`idPegawai` AND t3.`parent_id` = t2.`id` WHERE $q_filter");
    $employees = getRows("SELECT * FROM `pen_pegawai` t1 JOIN `emp` t2 JOIN `emp_phist` t3 ON t2.`id` = t1.`idPegawai` AND t3.`parent_id` = t2.`id` WHERE $q_filter $q_order $q_limit");

    foreach ($employees as $employee) {

        $no++;

        $aspect_1 = 0;
        $aspect_2 = 0;

        foreach ($aspects as $aspect) {

            $perspectives = KPIMasterPrespektif::where('idPeriode', $combo3)->where('idTipe', $combo1)->where('idKode', $combo2)->where('idAspek', $aspect->idAspek)->get();

            $perspective_value = 0;

            foreach ($perspectives as $perspective) {

                $objectives = KPIMasterObyektif::where('idPeriode', $combo3)->where('idKode', $combo2)->where('idPrespektif', $perspective->idPrespektif)->get();

                $objective_value = 0;

                foreach ($objectives as $objective) {

                    $target = KPISetingIndividuObyektif::where('idPeriode', $combo3)->where('idSasaran', $objective->idSasaran)->where('idPegawai', $employee['idPegawai'])->first();

                    if (!$target)
                        continue;

                    $realization = KPIRealisasiIndividuDetil::where('id_tahun', $combo3)
                        ->where('id_bulan', $combo4)
                        ->where('id_pegawai', $employee['idPegawai'])
                        ->where('id_sasaran', $objective->idSasaran)
                        ->first();

                    $value = $realization['nilai'] ?? 0;
                    $result = $value * ($target->bobotIndividu / 100);

                    $objective_value += $result;
                }

                $perspective_value += $objective_value * ($perspective->bobot / 100);
            }

            $aspect_1 = $aspect->aspekKode == "1" ? $perspective_value : $aspect_1;
            $aspect_2 = $aspect->aspekKode == "2" ? $perspective_value : $aspect_2;
        }

        $result = getField("SELECT `nilai` FROM `pen_hasil` WHERE `id_pegawai` = '$employee[idPegawai]' AND `id_periode` = '$combo3' AND `id_bulan` = '$combo4'") ?: 0;
        $wom = getWOMWithColor($combo3, $result);

        $data = [
            "<div align='center'>$no</div>",
            "<div align='left'>{$employee['name']}</div>",
            "<div align='center'>{$employee['reg_no']}</div>",
            "<div align='center'>$aspect_1</div>",
            "<div align='center'>$aspect_2</div>",
            "<div align='center'>" . ($aspect_1 + $aspect_2) . "</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>-</div>",
            "<div align='center'>" . ($aspect_1 + $aspect_2) . "</div>",
            "<div align='center' style='background-color: $wom[color]; color: #fff'><b>$result</b></div>",
            "<div align='center'>{$wom['mention'][0]}</div>",
        ];

        $datas[] = $data;
    }

    echo json_encode([
        "iTotalRecords" => sizeof($employees),
        "iTotalDisplayRecords" => $count,
        "aaData" => $datas
    ]);
}

function export()
{
    global $s, $arrTitle, $par, $search, $combo1, $combo2, $combo3, $combo4;

    $path_export = "files/exports/";

    $title = $arrTitle[$s];
    $file_name = "$title.xls";
    $excel_cell_last = columnXLS(18);
    $excel_cell_last_minus_one = columnXLS(17);

    $php_excel = new PHPExcel();
    $php_excel->getProperties()->setCreator("")->setLastModifiedBy("")->setTitle($title);

    $php_excel->getActiveSheet()->setTitle($title);

    $php_excel->getActiveSheet()->getPageSetup()
        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
        ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
        ->setRowsToRepeatAtTopByStartAndEnd(6, 6)
        ->setScale(70);

    $php_excel->getActiveSheet()->getSheetView()->setZoomScale(85);

    $php_excel->getActiveSheet()->getPageMargins()
        ->setTop(0.325)
        ->setRight(0.325)
        ->setLeft(0.325)
        ->setBottom(0.325);


    $php_excel->getActiveSheet()->getStyle()->getFont()->setSize(16);
    $php_excel->getActiveSheet()->getStyle()->getFont()->setBold(true);
    $php_excel->getActiveSheet()->setCellValue("A1", $title);
    $php_excel->getActiveSheet()->mergeCells("A1:{$excel_cell_last}1");
    $php_excel->getActiveSheet()->getRowDimension("1")->setRowHeight(30);

    $location = KPIMasterLokasi::find($combo1);
    $branch = KPIMasterLokasiCabang::find($combo2);
    $year = Master::find($combo3);
    $month = Master::find($combo4);

    $php_excel->getActiveSheet()->setCellValue("A2", "{$location->namaTipe} - {$branch->subKode} @ $month->namaData $year->namaData");


    $php_excel->getActiveSheet()->getStyle("A4")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}4")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}6")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("A4:A6")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last}4:{$excel_cell_last}6")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}6")->getFont()->setBold(true);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}6")->getAlignment()->setWrapText(true);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}6")->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $php_excel->getActiveSheet()->getRowDimension("4")->setRowHeight(25);
    $php_excel->getActiveSheet()->getRowDimension("5")->setRowHeight(25);
    $php_excel->getActiveSheet()->getRowDimension("6")->setRowHeight(25);

    $php_excel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
    $php_excel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
    $php_excel->getActiveSheet()->getColumnDimension("C")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("D")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("E")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("F")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("G")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("K")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("L")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("M")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("N")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("O")->setWidth(15);
    $php_excel->getActiveSheet()->getColumnDimension("P")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("Q")->setWidth(14);
    $php_excel->getActiveSheet()->getColumnDimension("R")->setWidth(14);

    $php_excel->getActiveSheet()->mergeCells("G4:K4");
    $php_excel->getActiveSheet()->mergeCells("L4:O5");

    $php_excel->getActiveSheet()->mergeCells("K5:K6");

    $php_excel->getActiveSheet()->mergeCells("A4:A6");
    $php_excel->getActiveSheet()->mergeCells("B4:B6");
    $php_excel->getActiveSheet()->mergeCells("C4:C6");
    $php_excel->getActiveSheet()->mergeCells("D4:D5");
    $php_excel->getActiveSheet()->mergeCells("E4:E5");
    $php_excel->getActiveSheet()->mergeCells("F4:F6");
    $php_excel->getActiveSheet()->mergeCells("P4:P5");
    $php_excel->getActiveSheet()->mergeCells("Q4:Q6");
    $php_excel->getActiveSheet()->mergeCells("R4:R6");

    $php_excel->getActiveSheet()->setCellValue("A4", "NO");
    $php_excel->getActiveSheet()->setCellValue("B4", "NAMA");
    $php_excel->getActiveSheet()->setCellValue("C4", "NPP");
    $php_excel->getActiveSheet()->setCellValue("D4", "NILAI TARGET / KINERJA");
    $php_excel->getActiveSheet()->setCellValue("E4", "NILAI PERILAKU");
    $php_excel->getActiveSheet()->setCellValue("F4", "TOTAL (A)");
    $php_excel->getActiveSheet()->setCellValue("G4", "PENGURANGAN ABSENSI");
    $php_excel->getActiveSheet()->setCellValue("L4", "PENGURANGAN SP");
    $php_excel->getActiveSheet()->setCellValue("P4", "NILAI KINERJA AKHIR");
    $php_excel->getActiveSheet()->setCellValue("Q4", "RANGKING");
    $php_excel->getActiveSheet()->setCellValue("R4", "YUDISIUM");

    $php_excel->getActiveSheet()->setCellValue("G5", "T");
    $php_excel->getActiveSheet()->setCellValue("H5", "S1");
    $php_excel->getActiveSheet()->setCellValue("I5", "I");
    $php_excel->getActiveSheet()->setCellValue("J5", "A");
    $php_excel->getActiveSheet()->setCellValue("K5", "TOTAL (B)");

    $php_excel->getActiveSheet()->setCellValue("D6", "(1-40)");
    $php_excel->getActiveSheet()->setCellValue("E6", "(1-10)");
    $php_excel->getActiveSheet()->setCellValue("G6", "0.5/3 HARI");
    $php_excel->getActiveSheet()->setCellValue("H6", "0.5/HARI");
    $php_excel->getActiveSheet()->setCellValue("I6", "1/HARI");
    $php_excel->getActiveSheet()->setCellValue("J6", "3/HARI");
    $php_excel->getActiveSheet()->setCellValue("l6", "TEGURAN/SP1");
    $php_excel->getActiveSheet()->setCellValue("M6", "SP2");
    $php_excel->getActiveSheet()->setCellValue("N6", "SP3");
    $php_excel->getActiveSheet()->setCellValue("O6", "TOTAL (C)");
    $php_excel->getActiveSheet()->setCellValue("P6", "(A)-(B)-(C)");


    $aspects = KPIMasterAspek::where('idPeriode', $combo3)->orderBy('urutanAspek')->get()->keyBy('idAspek');

    $row = 6;

    $q_filter = "1 = 1 ";
    $q_filter .= $search ? "AND (t2.`name` LIKE '%$search%' OR t2.`reg_no` LIKE '%$search%') " : "";
    $q_filter .= " AND t1.`tipePenilaian` = '$combo1'";
    $q_filter .= " AND t1.`kodePenilaian` = '$combo2'";
    $q_filter .= " AND t1.`tahunPenilaian` = '$combo3'";

    $employees = getRows("SELECT * FROM `pen_pegawai` t1 JOIN `emp` t2 JOIN `emp_phist` t3 ON t2.`id` = t1.`idPegawai` AND t3.`parent_id` = t2.`id` WHERE $q_filter");

    foreach ($employees as $employee) {

        $row++;

        $aspect_1 = 0;
        $aspect_2 = 0;

        foreach ($aspects as $aspect) {

            $perspectives = KPIMasterPrespektif::where('idPeriode', $combo3)->where('idTipe', $combo1)->where('idKode', $combo2)->where('idAspek', $aspect->idAspek)->get();

            $perspective_value = 0;

            foreach ($perspectives as $perspective) {

                $objectives = KPIMasterObyektif::where('idPeriode', $combo3)->where('idKode', $combo2)->where('idPrespektif', $perspective->idPrespektif)->get();

                $objective_value = 0;

                foreach ($objectives as $objective) {

                    $target = KPISetingIndividuObyektif::where('idPeriode', $combo3)->where('idSasaran', $objective->idSasaran)->where('idPegawai', $employee['idPegawai'])->first();

                    if (!$target)
                        continue;

                    $realization = KPIRealisasiIndividuDetil::where('id_tahun', $combo3)
                        ->where('id_bulan', $combo4)
                        ->where('id_pegawai', $employee['idPegawai'])
                        ->where('id_sasaran', $objective->idSasaran)
                        ->first();

                    $value = $realization['nilai'] ?? 0;
                    $result = $value * ($target->bobotIndividu / 100);

                    $objective_value += $result;
                }

                $perspective_value += $objective_value * ($perspective->bobot / 100);
            }

            $aspect_1 = $aspect->aspekKode == "1" ? $perspective_value : $aspect_1;
            $aspect_2 = $aspect->aspekKode == "2" ? $perspective_value : $aspect_2;
        }

        $result = getField("SELECT `nilai` FROM `pen_hasil` WHERE `id_pegawai` = '$employee[idPegawai]' AND `id_periode` = '$combo3' AND `id_bulan` = '$combo4'") ?: 0;
        $wom = getWOMWithColor($combo3, $result);


        $php_excel->getActiveSheet()->getStyle("A$row")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $php_excel->getActiveSheet()->getStyle("{$excel_cell_last}{$row}")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $php_excel->getActiveSheet()->setCellValue("A$row",$row - 6);
        $php_excel->getActiveSheet()->setCellValue("B$row", $employee['name']);
        $php_excel->getActiveSheet()->setCellValue("C$row", "{$employee['reg_no']}");
        $php_excel->getActiveSheet()->setCellValue("D$row", $aspect_1);
        $php_excel->getActiveSheet()->setCellValue("E$row", $aspect_2);
        $php_excel->getActiveSheet()->setCellValue("F$row", $aspect_1 + $aspect_2);
        $php_excel->getActiveSheet()->setCellValue("G$row", "");
        $php_excel->getActiveSheet()->setCellValue("H$row", "");
        $php_excel->getActiveSheet()->setCellValue("I$row", "");
        $php_excel->getActiveSheet()->setCellValue("J$row", "");
        $php_excel->getActiveSheet()->setCellValue("K$row", "");
        $php_excel->getActiveSheet()->setCellValue("L$row", "");
        $php_excel->getActiveSheet()->setCellValue("M$row", "");
        $php_excel->getActiveSheet()->setCellValue("N$row", "");
        $php_excel->getActiveSheet()->setCellValue("O$row", "");
        $php_excel->getActiveSheet()->setCellValue("P$row", $aspect_1 + $aspect_2);
        $php_excel->getActiveSheet()->setCellValue("Q$row", $result);
        $php_excel->getActiveSheet()->setCellValue("R$row", $wom['mention'][0]);

        $php_excel->getActiveSheet()->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("D$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("E$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("F$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("P$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("Q$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("R$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    }

    $php_excel->getActiveSheet()->getStyle("A{$row}:{$excel_cell_last}{$row}")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $row += 3;
    $php_excel->getActiveSheet()->setCellValue("B{$row}", "RATING");
    $php_excel->getActiveSheet()->setCellValue("C{$row}", "TOTAL");
    $php_excel->getActiveSheet()->getStyle("B{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $php_excel->getActiveSheet()->getStyle("C{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


    $php_excel->getActiveSheet()->getStyle("B{$row}:C{$row}")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("B{$row}:C{$row}")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("B${row}")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("C{$row}")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    $php_excel->getActiveSheet()->setCellValue("{$excel_cell_last_minus_one}{$row}", "MENGETAHUI");
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last_minus_one}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $row++;
    $php_excel->getActiveSheet()->setCellValue("{$excel_cell_last_minus_one}{$row}", "Divisi Human Capital");
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last_minus_one}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


    $conversions = KPIMasterNilaiHasil::where('idPeriode', $combo3)->get();

    foreach ($conversions as $conversion) {

        $php_excel->getActiveSheet()->setCellValue("B{$row}", strtoupper($conversion->penjelasanKonversi));
        $php_excel->getActiveSheet()->setCellValue("C{$row}", "0");

        $php_excel->getActiveSheet()->getStyle("C{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $php_excel->getActiveSheet()->getStyle("B${row}")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $php_excel->getActiveSheet()->getStyle("C{$row}")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $row++;
    }

    $php_excel->getActiveSheet()->setCellValue("B{$row}", "TOTAL");
    $php_excel->getActiveSheet()->setCellValue("C{$row}", "0");

    $php_excel->getActiveSheet()->getStyle("B{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $php_excel->getActiveSheet()->getStyle("C{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $php_excel->getActiveSheet()->getStyle("B{$row}:C{$row}")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("B{$row}:C{$row}")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("B${row}")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("C{$row}")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    $row--;
    $php_excel->getActiveSheet()->setCellValue("{$excel_cell_last_minus_one}{$row}", "---------------");
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last_minus_one}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $row++;
    $php_excel->getActiveSheet()->setCellValue("{$excel_cell_last_minus_one}{$row}", "pemimpin");
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last_minus_one}{$row}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    mkdir($path_export, 0755, true);

    $php_excel = PHPExcel_IOFactory::createWriter($php_excel, 'Excel5');
    $php_excel->save($path_export . $file_name);

    echo "<script>window.location='download.php?d=exp&f=$file_name'</script>";
}
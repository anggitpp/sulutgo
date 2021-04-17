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
                    <th rowspan="2" style="vertical-align:middle;" width="30">No.</th>
                    <th rowspan="2" style="vertical-align:middle;">Nama</th>
                    <th rowspan="2" style="vertical-align:middle;" width="70">NPP</th>
                    <th rowspan="2" style="vertical-align:middle;" width="150"><?= $arrParameter[38] ?></th>
                    <th rowspan="2" style="vertical-align:middle;" width="150">Posisi</th>
                    <th colspan="4" style="vertical-align:middle;">Komponen Penilaian</th>
                    <th rowspan="2" style="vertical-align:middle;" width="80">Total</th>
                    <th rowspan="2" style="vertical-align:middle;" width="120">Yudisium</th>
                </tr>
                <tr>
                    <th width="150">Sasaran Hasil Perusahaan</th>
                    <th width="150">sasaran Unit Kerja</th>
                    <th width="150">sasaran Hasil Individu</th>
                    <th width="150">Kompetensi Perilaku</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
    </div>

    <?= table(11, range(3, 11), "datas", "true", "", "datatable") ?>

    <script>

        codes = <?= KPIMasterLokasiCabang::where('statusKode', 't')->orderBy('idKode')->get()->keyBy('idKode')->toJson(); ?>

        function refreshCode() {

            parent_id = jQuery("#combo1").val()

            jQuery("#combo2").empty()

            for (index in codes) {

                if (codes[index].kodeTipe != parent_id)
                    continue
1
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

    $master_ids = collect($employees)->pluck('unit_id');
    $masters = Master::whereIn('kodeData', $master_ids)->get()->keyBy('kodeData')->map(function ($master) {
        return $master->namaData;
    });

    foreach ($employees as $employee) {

        $no++;

        $perspective_value_1 = 0;
        $perspective_value_2 = 0;
        $perspective_value_3 = 0;
        $perspective_value_4 = 0;
        $position = 0;

        foreach ($aspects as $aspect) {

            $perspectives = KPIMasterPrespektif::where('idPeriode', $combo3)->where('idTipe', $combo1)->where('idKode', $combo2)->where('idAspek', $aspect->idAspek)->get();

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

                $perspective_value = $objective_value * ($perspective->bobot / 100);
                $position++;

                switch ($position) {

                    case 1:
                        $perspective_value_1 = $perspective_value;
                        break;

                    case 2:
                        $perspective_value_2 = $perspective_value;
                        break;

                    case 3:
                        $perspective_value_3 = $perspective_value;
                        break;

                    case 4:
                        $perspective_value_4 = $perspective_value;
                        break;

                }

            }

        }

        $result = getField("SELECT `nilai` FROM `pen_hasil` WHERE `id_pegawai` = '$employee[idPegawai]' AND `id_periode` = '$combo3' AND `id_bulan` = '$combo4'") ?: 0;
        $wom = getWOMWithColor($combo3, $result);

        $data = [
            "<div align='center'>$no</div>",
            "<div align='left'>{$employee['name']}</div>",
            "<div align='center'>{$employee['reg_no']}</div>",
            "<div>{$masters[$employee['unit_id']]}</div>",
            "<div>$employee[pos_name]</div>",
            "<div align='center'>$perspective_value_1</div>",
            "<div align='center'>$perspective_value_2</div>",
            "<div align='center'>$perspective_value_3</div>",
            "<div align='center'>$perspective_value_4</div>",
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
    $excel_cell_last = columnXLS(11);

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
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}5")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("A4:A5")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $php_excel->getActiveSheet()->getStyle("{$excel_cell_last}4:{$excel_cell_last}5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}5")->getFont()->setBold(true);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}5")->getAlignment()->setWrapText(true);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $php_excel->getActiveSheet()->getStyle("A4:{$excel_cell_last}5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $php_excel->getActiveSheet()->getRowDimension("4")->setRowHeight(25);
    $php_excel->getActiveSheet()->getRowDimension("5")->setRowHeight(32);

    $php_excel->getActiveSheet()->getColumnDimension("A")->setWidth(10);
    $php_excel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
    $php_excel->getActiveSheet()->getColumnDimension("C")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("D")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("E")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("F")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("G")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("H")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("I")->setWidth(18);
    $php_excel->getActiveSheet()->getColumnDimension("K")->setWidth(14);
    $php_excel->getActiveSheet()->getColumnDimension("J")->setWidth(14);

    $php_excel->getActiveSheet()->setCellValue("A4", "NO");
    $php_excel->getActiveSheet()->setCellValue("B4", "NAMA");
    $php_excel->getActiveSheet()->setCellValue("C4", "NPP");
    $php_excel->getActiveSheet()->setCellValue("D4", "UNIT KERJA");
    $php_excel->getActiveSheet()->setCellValue("E4", "POSISI");
    $php_excel->getActiveSheet()->setCellValue("F4", "KOMPONEN PENILAIAN");
    $php_excel->getActiveSheet()->setCellValue("J4", "TOTAL");
    $php_excel->getActiveSheet()->setCellValue("K4", "YUDISIUM");

    $php_excel->getActiveSheet()->setCellValue("F5", "SASARAN HASIL PERUSAHAAN");
    $php_excel->getActiveSheet()->setCellValue("G5", "SASARAN UNIT KERJA");
    $php_excel->getActiveSheet()->setCellValue("H5", "SASARAN HASIL INDIVIDU");
    $php_excel->getActiveSheet()->setCellValue("I5", "KOMPTENSI PERILAKU");

    $php_excel->getActiveSheet()->mergeCells("F4:I4");

    $php_excel->getActiveSheet()->mergeCells("A4:A5");
    $php_excel->getActiveSheet()->mergeCells("B4:B5");
    $php_excel->getActiveSheet()->mergeCells("C4:C5");
    $php_excel->getActiveSheet()->mergeCells("D4:D5");
    $php_excel->getActiveSheet()->mergeCells("E4:E5");
    $php_excel->getActiveSheet()->mergeCells("J4:J5");
    $php_excel->getActiveSheet()->mergeCells("K4:K5");


    $aspects = KPIMasterAspek::where('idPeriode', $combo3)->orderBy('urutanAspek')->get()->keyBy('idAspek');

    $q_filter = "1 = 1 ";
    $q_filter .= $search ? "AND (t2.`name` LIKE '%$search%' OR t2.`reg_no` LIKE '%$search%') " : "";
    $q_filter .= " AND t1.`tipePenilaian` = '$combo1'";
    $q_filter .= " AND t1.`kodePenilaian` = '$combo2'";
    $q_filter .= " AND t1.`tahunPenilaian` = '$combo3'";

    $employees = getRows("SELECT * FROM `pen_pegawai` t1 JOIN `emp` t2 JOIN `emp_phist` t3 ON t2.`id` = t1.`idPegawai` AND t3.`parent_id` = t2.`id` WHERE $q_filter");

    $master_ids = collect($employees)->pluck('unit_id');
    $masters = Master::whereIn('kodeData', $master_ids)->get()->keyBy('kodeData')->map(function ($master) {
        return $master->namaData;
    });

    $row = 5;

    foreach ($employees as $employee) {

        $row++;

        $perspective_value_1 = 0;
        $perspective_value_2 = 0;
        $perspective_value_3 = 0;
        $perspective_value_4 = 0;
        $position = 0;

        foreach ($aspects as $aspect) {

            $perspectives = KPIMasterPrespektif::where('idPeriode', $combo3)->where('idTipe', $combo1)->where('idKode', $combo2)->where('idAspek', $aspect->idAspek)->get();

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

                $perspective_value = $objective_value * ($perspective->bobot / 100);
                $position++;

                switch ($position) {

                    case 1:
                        $perspective_value_1 = $perspective_value;
                        break;

                    case 2:
                        $perspective_value_2 = $perspective_value;
                        break;

                    case 3:
                        $perspective_value_3 = $perspective_value;
                        break;

                    case 4:
                        $perspective_value_4 = $perspective_value;
                        break;

                }

            }

        }

        $result = getField("SELECT `nilai` FROM `pen_hasil` WHERE `id_pegawai` = '$employee[idPegawai]' AND `id_periode` = '$combo3' AND `id_bulan` = '$combo4'") ?: 0;
        $wom = getWOMWithColor($combo3, $result);

        $php_excel->getActiveSheet()->getStyle("A$row")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $php_excel->getActiveSheet()->getStyle("{$excel_cell_last}{$row}")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $php_excel->getActiveSheet()->setCellValue("A$row",$row - 5);
        $php_excel->getActiveSheet()->setCellValue("B$row", $employee['name']);
        $php_excel->getActiveSheet()->setCellValue("C$row", "{$employee['reg_no']}");
        $php_excel->getActiveSheet()->setCellValue("D$row", "{$masters[$employee['unit_id']]}");
        $php_excel->getActiveSheet()->setCellValue("E$row", "{$employee['pos_name']}");
        $php_excel->getActiveSheet()->setCellValue("F$row", $perspective_value_1);
        $php_excel->getActiveSheet()->setCellValue("G$row", $perspective_value_2);
        $php_excel->getActiveSheet()->setCellValue("H$row", $perspective_value_3);
        $php_excel->getActiveSheet()->setCellValue("I$row", $perspective_value_4);
        $php_excel->getActiveSheet()->setCellValue("J$row", $result);
        $php_excel->getActiveSheet()->setCellValue("K$row", $wom['mention'][0]);

        $php_excel->getActiveSheet()->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("C$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("F$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("G$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("H$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("I$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("J$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $php_excel->getActiveSheet()->getStyle("K$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    }

    $php_excel->getActiveSheet()->getStyle("A{$row}:{$excel_cell_last}{$row}")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    mkdir($path_export, 0755, true);

    $php_excel = PHPExcel_IOFactory::createWriter($php_excel, 'Excel5');
    $php_excel->save($path_export . $file_name);

    echo "<script>window.location='download.php?d=exp&f=$file_name'</script>";
}
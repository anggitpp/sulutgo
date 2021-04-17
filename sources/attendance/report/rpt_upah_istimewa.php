<?php
if(!isset($menuAccess[$s]['view']))
	echo "<script type=\"text\javascript\">logout();</script>";

if(!isset($par[tanggalMulai])) $par[tanggalMulai] = date("d/m/Y", strtotime("-1 MONTH"));
if(!isset($par[tanggalSelesai])) $par[tanggalSelesai] = date("d/m/Y");

$fExport = "files/export/";
switch($par[mode]){
	case "export":
	xls();
	break;
}

if($_GET['json'] == "1"){
	header("Content-type: application/json");

	$ret = getData();

	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}
?>
<div class="pageheader">
	<h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
	<div style="margin-top: 10px;">
		<?php echo getBread(); ?>
	</div>
	<span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
	<form id="form" action="" method="post" class="stdform">
        <div style="position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 575px;">
            <div style="position:absolute; right: 0px;">
              <table>
                <tr>
                  <td nowrap="nowrap">
                    Lokasi Kerja : <?php echo comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px") ?>
                  </td>
                  <td style="vertical-align:top;" id="bView">
                    <input type="button" value="+" style="font-size:26px; padding:0 6px;" class="btn btn_search btn-small" onclick="
                    document.getElementById('bView').style.display = 'none';
                    document.getElementById('bHide').style.display = 'table-cell';
                    document.getElementById('dFilter').style.visibility = 'visible';              
                    document.getElementById('fSet').style.height = 'auto';
                    document.getElementById('fSet').style.padding = '10px';
                    ">
                  </td>
                  <td style="vertical-align:top; display:none;" id="bHide">
                    <input type="button" value="-" style="font-size:26px; padding:0 9px;" class="btn btn_search btn-small" onclick="
                    document.getElementById('bView').style.display = 'table-cell';
                    document.getElementById('bHide').style.display = 'none';
                    document.getElementById('dFilter').style.visibility = 'collapse';             
                    document.getElementById('fSet').style.height = '0px';
                    document.getElementById('fSet').style.padding = '0px';
                    ">          
                  </td>
                  <td>
                  	&nbsp;
                    <input type="text" class="hasDatePicker" name="par[tanggalMulai]" value="<?php echo $par[tanggalMulai] ?>" onchange="document.getElementById('form').submit();">
					&nbsp;s/d&nbsp;
					<input type="text" class="hasDatePicker" name="par[tanggalSelesai]" value="<?php echo $par[tanggalSelesai] ?>" onchange="document.getElementById('form').submit();">
					&nbsp;					
                  </td>
				  <td>
					<input type="button" class="cancel radius2" value="Back" onclick="window.location = '?<?php echo preg_replace("/(&[ms]=\w+)/", "", getPar()); ?>';"/>
				  </td>
                </tr>
              </table>
            </div>
            <fieldset id="fSet" style="padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;">
              <div id="dFilter" style="visibility:collapse;">
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
              </div>
            </fieldset>
        </div>
      </form>
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20">NO.</th>
				<th>NAMA</th>
				<th width="100">NPP</th>
				<th width="180">JABATAN</th>
				<th width="120">JUMLAH HARI</th>
				<th width="120">JUMLAH HK</th>
				<th width="120">TOTAL HK</th>
			</tr>
		</thead>
		<tbody></tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;"><b>Total</b></td>
				<td style="text-align: center;">&nbsp;</td>
				<td style="text-align: center;">&nbsp;</td>
				<td style="text-align: center;">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
</div>
<?php
if($par[mode] == "export"){
	echo "<iframe src=\"download.php?d=exp&f=".$arrTitle[$s]."_".date('dmY').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}
?>
<script type="text/javascript">
	var jumlahHari = 0;
	var jumlahHK = 0;
	var totalHK = 0;
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "ajax.php?json=1<?php echo getPar($par); ?>",
			"aoColumns": [
			{"mData": null, "bSortable": false, "sClass": "alignRight"},
			{"mData": "name", "bSortable": true},
			{"mData": "reg_no", "bSortable": true},
			{"mData": "pos_name", "bSortable": true},
			{"mData": "jumlahHari", "bSortable": true, "sClass": "alignCenter"},
			{"mData": "jumlahHK", "bSortable": true, "sClass": "alignCenter"},
			{"mData": "totalHK", "bSortable": true, "sClass": "alignCenter"},	
			],
			"aaSorting": [[1, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jumlahHari += parseFloat(aData['jumlahHari']);
				jumlahHK += parseFloat(aData['jumlahHK']);
				totalHK += parseFloat(aData['totalHK']);
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			},
			"fnFooterCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:nth-child(2)", nRow).html("<b>" + jumlahHari + "</b>");
				jQuery("td:nth-child(3)", nRow).html("<b>" + jumlahHK + "</b>");
				jQuery("td:nth-child(4)", nRow).html("<b>" + totalHK + "</b>");
				return nRow;
			},"fnDrawCallback": function( oSettings ) {
				jumlahHari = jumlahHK = totalHK = 0;
			}
		});


		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "10px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 5px; right: 0px'>");
		jQuery("#right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");
		jQuery("#datatable_wrapper .top").append("</div>");

		jQuery("#btnExport").live("click", function(e){
			e.preventDefault();
			window.location = "?par[mode]=export<?= getPar($par); ?>";
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>

<?php
function getData(){
	global $par, $areaCheck, $arrParameter;
	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
	$filter = "WHERE t1.id IS NOT NULL AND t1.status='$status' AND t1.location IN ( $areaCheck )";
	if(!empty($par[idLokasi]))
		$filter .= " AND t1.location = '$par[idLokasi]'";
	if(!empty($par[divId]))
	    $filter.= " and t1.div_id='".$par[divId]."'";
	if(!empty($par[deptId]))
		$filter.= " and t1.dept_id='".$par[deptId]."'";
	if(!empty($par[unitId]))
	    $filter.= " and t1.unit_id='".$par[unitId]."'";
	
	$arrLibur = arrayQuery("SELECT mulaiLibur, selesaiLibur FROM dta_libur WHERE idKategori = '582' AND statusLibur = 't' AND (mulaiLibur BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."' OR selesaiLibur BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."')");
	
	$sql = "SELECT t1.id, t1.name, t1.reg_no, t1.pos_name FROM dta_pegawai t1 $filter";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_assoc($res)){		
		$r['jumlahHari'] = 0;
		if (is_array($arrLibur)) {		  
			reset($arrLibur);
			while(list($tanggalMulai, $tanggalSelesai) = each($arrLibur)){
				$r['jumlahHari'] += getField("SELECT COUNT(*) FROM (
					select idPegawai, tanggalAbsen from att_absen WHERE idPegawai = '$r[id]' AND tanggalAbsen BETWEEN '$tanggalMulai' AND '$tanggalSelesai' group by 1, 2
					) as t
					");
			}
		}
		$r['jumlahHK'] = 2;
		$r['totalHK'] = $r['jumlahHari'] * $r['jumlahHK'];

		$ret[] = $r;
	}

	return $ret;
}

function xls(){		
	global $s,$par,$fExport, $cNama, $arrTitle;
	require_once 'plugins/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]."_".date("dmY"));
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', $arrTitle[$s]);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->setCellValue('A4', "NO");
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "Nama");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "NPP");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "JABATAN");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "Jumlah Hari");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "Jumlah HK");
	$objPHPExcel->getActiveSheet()->setCellValue('G4', "Total HK");

	$no = 0;
	$currentRow = 5;
	$jumlahHari = 0;
	$jumlahHK = 0;
	$totalHK = 0;
	foreach(getData() as $r){
		$no++;
		$jumlahHari += intval($r[jumlahHari]);
		$jumlahHK += intval($r[jumlahHK]);
		$totalHK += intval($r[totalHK]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$currentRow, $no.".",PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$currentRow, $r[name]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$currentRow, $r[reg_no],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$currentRow, $r[pos_name],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$currentRow, $r[jumlahHari],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$currentRow, $r[jumlahHK],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$currentRow, $r[totalHK],PHPExcel_Cell_DataType::TYPE_STRING);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':G'.$currentRow)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':G'.$currentRow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':G'.$currentRow)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':G'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$currentRow++;
	}

	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':D'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow.':G'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$currentRow.':D'.$currentRow);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$currentRow, "Total");
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$currentRow, getAngka($jumlahHari, 0),PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$currentRow, getAngka($jumlahHK, 0),PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$currentRow, getAngka($totalHK, 0),PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':G'.$currentRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('B3B8BA');
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
	$objPHPExcel->setActiveSheetIndex(0);
		// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport.$arrTitle[$s]."_".date('dmY').".xls");
}
?>
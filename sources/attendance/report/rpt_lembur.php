<?php
if(!isset($menuAccess[$s]['view']))
	echo "<script type=\"text\javascript\">logout();</script>";

if(empty($par[tanggalMulai])){
	$m = date("m") == 1 ? 12 : date("m")-1;
	$Y = date("m") == 1 ? date("Y")-1 : date("Y");
	$par[tanggalMulai] = "21/".str_pad($m, 2, "0", STR_PAD_LEFT)."/".$Y;
}				
$par[tanggalSelesai] = empty($par[tanggalSelesai]) ? date('20/m/Y') : $par[tanggalSelesai];

$arrHari = array("Mon" => "Senin", "Tue" => "Selasa", "Wed" => "Rabu", "Thu" => "Kamis", "Fri" => "Jumat", "Sat" => "Sabtu", "Sun" => "Minggu");
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
        <div style="position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 675px;">
            <div style="position:absolute; right: 0px;">
              <table>
                <tr>
                  <td>
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
					<input type="text" class="smallinput hasDatePicker" name="par[tanggalMulai]" value="<?php echo $par[tanggalMulai] ?>" onchange="document.getElementById('form').submit();">
					&nbsp;s/d&nbsp;
					<input type="text" class="smallinput hasDatePicker" name="par[tanggalSelesai]" value="<?php echo $par[tanggalSelesai] ?>" onchange="document.getElementById('form').submit();">
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
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       where t3.kodeCategory='X03' order by t1.urutanData", "id", "description", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       where t4.kodeCategory='X03' AND t1.kodeInduk = '$par[divId]' order by t1.urutanData", "id", "description", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
                <p>
                  <label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
                  <div class="field" style="margin-left:150px;">
                      <?php echo comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk

                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk

                       JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk

                       where t5.kodeCategory='X03' AND t1.kodeInduk = '$par[deptId]' order by t1.urutanData", "id", "description", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")  ?>
                  </div>
                </p>
              </div>
            </fieldset>
        </div>
      </form>	
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20" rowspan="2" style="vertical-align: middle;">NO.</th>
				<th rowspan="2" style="vertical-align: middle;"><u>NAMA</u><br>EMPLOYEES NAME</th>
				<th width="100" rowspan="2" style="vertical-align: middle;"><u>JABATAN</u><br>POSITION</th>
				<th width="100" rowspan="2" style="vertical-align: middle;">DEPARTEMEN</th>
				<th width="180" rowspan="2" style="vertical-align: middle;"><u>UNTUK KEPERLUAN TUGAS</u><br>FOR DUTY</th>
				<th colspan="4" style="vertical-align: middle;"><u>PELAKSANAAN LEMBUR</u><br>OVERTIME EXECUTION</th>
				<th width="80" style="vertical-align: middle" rowspan="2"><u>JML JAM</u><br>NUMBER OF <br> HOURS</th>
				<!-- <th width="180" style="vertical-align: middle" rowspan="2"><u>KETERANGAN</u><br>BOLDNESS</th> -->
			</tr>
			<tr>
				<th><u>HARI</u><br>DAY</th>
				<th><u>TGL</u><br>DATE</th>
				<th><u>BULAN</u><br>MONTH</th>
				<th><u>TAHUN</u><br>YEAR</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
<?php
if($par[mode] == "export"){
	echo "<iframe src=\"download.php?d=exp&f=".$arrTitle[$s]."_".date('dmY').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}
?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"aLengthMenu": [[-1], ["All"]],
			"bSort": false,
			"bFilter": false,
			"bPaginate": false,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "ajax.php?json=1<?php echo getPar($par); ?>",
			"aoColumns": [
			{"mData": null, "bSortable": false, "sClass": "alignRight"},
			{"mData": null, "bSortable": false, "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['name'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['pos_name'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['deptName'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['keteranganLembur'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['hariLembur'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['tglLembur'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['bulanLembur'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['tahunLembur'] : '&nbsp;';
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return o.aData['viewType'] == 'row' ? o.aData['intervalLembur'] : "&nbsp;";
			}},
			// {"mData": null, "bSortable": true},	
			],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
				jQuery("tr[id^='footer_']").each(function () {
					jQuery(this).after("<tr><td colspan=\"10\">&nbsp;</td></tr>");
				});
			}, "sDom": "<'top'f>rt<'bottom'lp><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				if(aData['viewType'] == 'row'){
					jQuery("td:first", nRow).html(aData['no'] + ".");
				}else{
					jQuery("td", nRow).remove();
					jQuery(nRow).attr("id", "footer_" + iDisplayIndexFull);
					jQuery(nRow).append("<td colspan=\"9\" class=\"alignRight\"><b>TOTAL JAM LEMBUR</b></td>");
					jQuery(nRow).append("<td class=\"alignCenter\"><b>" + aData['totalInterval'] + "</b></td>");
					jQuery(nRow).after("<tr class=\"odd\"><td colspan=\"10\">&nbsp;</td></tr>");
					
				}
				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});


		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "10px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 5px; right: 0px'>");
		jQuery("#right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");
		jQuery("#datatable_wrapper .top").append("</div>");

		jQuery("#btnExport").live("click", function(e){
			e.preventDefault();
			window.location = "?par[mode]=export<?= getPar($par, "mode"); ?>";
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
			jQuery("tr[id^='footer_']").each(function () {
				jQuery(this).after("<tr><td colspan=\"10\">&nbsp;</td></tr>");
			});
		});

		jQuery(".togglemenu").click();
	});
</script>
<?php
function getData(){
	global $par, $arrHari, $areaCheck;
	
	$filter .= "WHERE t1.persetujuanLembur = 't' AND t1.sdmLembur = 't' AND t2.location IN ( $areaCheck )";
	if(!empty($par[tanggalMulai]) && !empty($par[tanggalSelesai]))
		$filter .= " AND (date(t1.mulaiLembur) BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."')";
	if(!empty($par[idLokasi]))
		$filter .= " AND t2.location = '$par[idLokasi]'";
	if(!empty($par[divId]))
	    $filter.= " and t2.div_id='".$par[divId]."'";
	if(!empty($par[deptId]))
		$filter.= " and t2.dept_id='".$par[deptId]."'";
	if(!empty($par[unitId]))
	    $filter.= " and t2.unit_id='".$par[unitId]."'";
	$sql = "
	SELECT 
	t1.idPegawai, t2.name, t2.pos_name, t3.namaData deptName, t1.keteranganLembur, t1.tanggalLembur, 
	MOD( TIMESTAMPDIFF(hour, t1.mulaiLembur, t1.selesaiLembur), 24) intervalHours,
	MOD( TIMESTAMPDIFF(minute, t1.mulaiLembur, t1.selesaiLembur), 60) intervalMinutes,
	t1.mulaiLembur, t1.selesaiLembur
	FROM att_lembur t1 
	JOIN dta_pegawai t2 
	ON t2.id = t1.idPegawai
	LEFT JOIN mst_data t3 
	ON t3.kodeData = t2.div_id
	$filter
	ORDER BY t2.name, t1.mulaiLembur
	";
	$res = db($sql);
	$ret = array();
	$lastIdPegawai = 0;
	$ctd = 0;
	$arrInterval = array();
	while($r = mysql_fetch_assoc($res)){
		if($lastIdPegawai != $r[idPegawai]){
			if(($ctd - 1) != 0 && $lastIdPegawai != 0)
				$ret[] = array("viewType" => "footer", "totalInterval" => sum_the_time($arrInterval));
			$lastIdPegawai = $r[idPegawai];
			$arrInterval = array();
		}

		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);					
		list($mulaiLembur_jam, $mulaiLembur_menit, $mulaiLembur_detik) = explode(":", $mulaiLembur);
		
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);
		list($selesaiLembur_tahun, $selesaiLembur_bulan, $selesaiLembur_hari) = explode("-", $selesaiLembur_tanggal);
		list($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik) = explode(":", $selesaiLembur);
		
		$durasiLembur = $mulaiLembur > $selesaiLembur_jam ?
		selisihJam($r[mulaiLembur], date('Y-m-d H:i:s', dateAdd("d", 1, mktime($selesaiLembur_jam, $selesaiLembur_menit, $selesaiLembur_detik, $selesaiLembur_bulan, $selesaiLembur_hari, $selesaiLembur_tahun)))):
		selisihJam($r[mulaiLembur], $r[selesaiLembur]);
		
		$r['viewType'] = 'row';
		//$r['intervalLembur'] = sum_the_time(array($r['intervalHours'] . "," . $r['intervalMinutes']));
		$r['intervalLembur'] = $durasiLembur;
		unset($r['intervalHours'], $r['intervalMinutes']);

		$r['hariLembur'] = $arrHari[date('D', strtotime($r['mulaiLembur']))];
		$r['tglLembur'] = date('d', strtotime($r['mulaiLembur']));
		$r['bulanLembur'] = date('m', strtotime($r['mulaiLembur']));
		$r['tahunLembur'] = date('Y', strtotime($r['mulaiLembur']));
		unset($r['mulaiLembur']);

		array_push($arrInterval, $r['intervalLembur']);

		$r['no'] = $ctd+1;
		$ret[] = $r;

		$ctd++;
	}

	#if(($ctd - 1) != 0 && $idPegawai != 0)
		$ret[] = array("viewType" => "footer", "totalInterval" => sum_the_time($arrInterval));

	return $ret;
}
function sum_the_time($times) {
	foreach ($times as $time) {
		list($hour, $minute) = explode(',', $time);
		$minutes += $hour * 60;
		$minutes += $minute;
	}

	$hours = floor($minutes / 60);
	$minutes -= $hours * 60;

	return sprintf('%2d,%02d', $hours, $minutes);
}
function xls(){    
	global $s,$par,$fExport, $cNama, $arrTitle;

	require_once 'plugins/PHPExcel.php';

	$objPHPExcel = new PHPExcel();             
	$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAPITULASI LEMBUR PERIODE ". $par[tanggalMulai]." - ".$par[tanggalSelesai]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('I4:I5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('J4:J5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('H5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
	$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
	$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
	$objPHPExcel->getActiveSheet()->mergeCells('F4:I4');
	$objPHPExcel->getActiveSheet()->mergeCells('J4:J5');

	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "NAMA KARYAWAN");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "JABATAN");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "DEPARTEMEN");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "UNTUK KEPERLUAN TUGAS");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "PELAKSANAAN LEMBUR");
	$objPHPExcel->getActiveSheet()->setCellValue('J4', "JML JAM");

	$objPHPExcel->getActiveSheet()->setCellValue('F5', "HARI");
	$objPHPExcel->getActiveSheet()->setCellValue('G5', "TGL");
	$objPHPExcel->getActiveSheet()->setCellValue('H5', "BULAN");
	$objPHPExcel->getActiveSheet()->setCellValue('I5', "TAHUN");

	$currentRow = 6;
	
	$no = 0;
	foreach(getData() as $r){
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow.':J'.$currentRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		if($r['viewType'] == 'row'){
			$no++;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('I'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$currentRow, $no.".",PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$currentRow, $r[name]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$currentRow, $r[pos_name],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$currentRow, $r[deptName],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$currentRow, $r[keteranganLembur],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$currentRow, $r[hariLembur],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$currentRow, $r[tglLembur],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$currentRow, $r[bulanLembur],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$currentRow, $r[tahunLembur],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$currentRow, $r[intervalLembur],PHPExcel_Cell_DataType::TYPE_STRING);
		}else{
			$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':J'.$currentRow)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$currentRow)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$currentRow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$currentRow)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$currentRow.':I'.$currentRow);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$currentRow, "TOTAL JAM LEMBUR",PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$currentRow, $r[totalInterval],PHPExcel_Cell_DataType::TYPE_STRING);
			$currentRow++;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$currentRow.':J'.$currentRow);
		}

		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':J'.$currentRow)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':J'.$currentRow)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':J'.$currentRow)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$currentRow.':J'.$currentRow)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$currentRow++;
	}

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
	$objWriter->save($fExport.$arrTitle[$s] . "_" .date('dmY').".xls");
}



?>
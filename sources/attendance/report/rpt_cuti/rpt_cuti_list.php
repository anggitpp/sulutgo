<?php
if(!isset($par[tanggalMulai])) $par[tanggalMulai] = date("d/m/Y", strtotime("-1 MONTH"));
if(!isset($par[tanggalSelesai])) $par[tanggalSelesai] = date("d/m/Y");

$fExport = "files/export/";
switch($par[mode]){
	case "export":
	xls();
	break;
}
$cutil = new Common();

$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
$arrCuti = arrayQuery("SELECT idPegawai, MONTH(mulaiCuti), SUM(jumlahCuti) FROM att_cuti WHERE persetujuanCuti = 't' AND sdmCuti = 't' AND (mulaiCuti BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."' OR selesaiCuti BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."') group by 1,2");
//$jatahCuti = getField("SELECT SUM(jatahCuti) FROM dta_cuti WHERE (MONTH(mulaiCuti) BETWEEN MONTH('".setTanggal($par[tanggalMulai])."') AND MONTH('".setTanggal($par[tanggalSelesai])."') OR MONTH(selesaiCuti) BETWEEN MONTH('".setTanggal($par[tanggalMulai])."') AND MONTH('".setTanggal($par[tanggalSelesai])."')) AND statusCuti = 't'");
$jatahCuti = getField("SELECT SUM(jatahCuti) FROM dta_cuti WHERE (MONTH(".setTanggal($par[tanggalMulai]).") BETWEEN MONTH(mulaiCuti) AND MONTH(selesaiCuti) OR MONTH('".setTanggal($par[tanggalSelesai])."') BETWEEN MONTH(mulaiCuti) AND MONTH(selesaiCuti) ) AND statusCuti = 't'");
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
		<?php echo getBread() ?>
	</div>
	<span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
	<form id="form" action="" method="post" class="stdform">
		<div style="position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 800px;">
			<div style="position:absolute; right: 0px;">
				<table>
					<tr>
						<td>
							Lokasi Kerja : <?php echo comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px") ?>
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
							<input type="button" class="cancel radius2" value="Back" onclick="window.location = '?<?php echo preg_replace("/(&[ms]=\w+)/", "", getPar()); ?>';"/>
						</td>
					</tr>
				</table>
			</div>
			<fieldset id="fSet" style="padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 330px; top: 40px; z-index: 800;">
				<div id="dFilter" style="visibility:collapse;">
					<p>
						<label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[39]) ?></label>
						<div class="field" style="margin-left:150px;">
							<?php
							$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X04' order by t1.urutanData";
							echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "par[divId]", $par[divId], "", "class='chosen-select' style=\"width: 250px;\"");
							?>
						</div>
					</p>
					<p>
						<label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[40]) ?></label>
						<div class="field" style="margin-left:150px;">
							<?php
							$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk where t3.kodeCategory='X04' order by t1.urutanData";
							echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "par[deptId]", $par[deptId], "", "class='chosen-select' style=\"width: 250px;\"");
							?>
						</div>
					</p>
					<p>
						<label class="l-input-small" style="width:150px; text-align:left; padding-left:10px;"><?php echo strtoupper($arrParameter[41]) ?></label>
						<div class="field" style="margin-left:150px;">
							<?php
							$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t4.kodeCategory='X04' order by t1.urutanData";
							echo $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "par[unitId]", $par[unitId], "", "class='chosen-select' style=\"width: 250px;\"");
							?>
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
				<th width="200">NPP</th>
				<th width="200">DEPARTEMEN</th>
				<th width="120">JUMLAH CUTI</th>
				<th width="120">SISA CUTI</th>
				<th width="80">KONTROL</th>
			</tr>
		</thead>
	</table>
</div>
<?php
if($par[mode] == "export"){
	echo "<iframe src=\"download.php?d=exp&f=".$arrTitle[$s]."_".date('dmY').".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"bSort": false,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "ajax.php?json=1<?= getPar($par, 'mode,periodeAwal,periodeAkhir'); ?>",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight"},
			{"mData": "name"},
			{"mData": "reg_no"},
			{"mData": "deptName"},
			{"mData": "jmlCuti", "sClass": "alignCenter"},
			{"mData": "sisaCuti", "sClass": "alignCenter"},
			{"mData": null, "sClass": "alignCenter", "fnRender": function(o){
				var ret = "";

				ret += "<a href=\"?par[mode]=det&par[idPegawai]=" + o.aData['id'] + "<?php echo getPar($par, "mode,idPegawai") ?>\" class=\"detail\" title=\"Detail Data\"><span>Detail Data</span></a>";
				

				return ret;
			}}
			],
			"aaSorting": [[1, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery("#btnExport").live("click", function(e){
			e.preventDefault();
			window.location = "?par[mode]=export<?= getPar($par, "mode"); ?>";
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "9px").css("margin-top", "0px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		jQuery("#datatable_wrapper #right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");

		jQuery("#par\\[divId\\], #par\\[deptId\\], #par\\[unitId\\]").live("change", function (e) {
			e.preventDefault();
			divId = jQuery("#par\\[divId\\]").val();
			ot.fnReloadAjax("ajax.php?json=1&par[divId]=" + jQuery("#par\\[divId\\]").val() + "&par[deptId]=" + jQuery("#par\\[deptId\\]").val() + "&par[unitId]=" + jQuery("#par\\[unitId\\]").val() + '<?php echo getPar($par, "mode") ?>');
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});

		jQuery('.chosen-select').each(function(){
			var search = (jQuery(this).attr("data-nosearch") === "true") ? true : false,
			opt = {};
			if(search) opt.disable_search_threshold = 9999999;
			jQuery(this).chosen(opt);
		});

		jQuery("#par\\[deptId\\]").chained("#par\\[divId\\]");
		jQuery("#par\\[deptId\\]").trigger("chosen:updated");

		jQuery("#par\\[divId\\]").bind("change", function () {
			jQuery("#par\\[deptId\\]").trigger("chosen:updated");
		});

		jQuery("#par\\[unitId\\]").chained("#par\\[deptId\\]");
		jQuery("#par\\[unitId\\]").trigger("chosen:updated");

		jQuery("#par\\[deptId\\]").bind("change", function () {
			jQuery("#par\\[unitId\\]").trigger("chosen:updated");
		});
	});


</script>
<?php 
function getData(){
	global $par, $status, $arrCuti, $jatahCuti, $areaCheck;

	$filter = "where t1.status='".$status."' AND t2.location IN ( $areaCheck )";

	if(!empty($par[divId]))
		$filter .= " AND t2.div_id = '$par[divId]'";
	if(!empty($par[deptId]))
		$filter .= " AND t2.dept_id = '$par[deptId]'";
	if(!empty($par[unitId]))
		$filter .= " AND t2.unit_id = '$par[unitId]'";

	if(!empty($par[idLokasi]))
		$filter .= " AND t2.location = '$par[idLokasi]'";
	$sql = "
	SELECT
	t1.id, t1.reg_no, UPPER(t1.name) name, t3.namaData deptName
	FROM emp t1
	LEFT JOIN emp_phist t2
	ON (t1.id = t2.parent_id AND t2.status=1)
	LEFT JOIN mst_data t3 
	ON t3.kodeData = t2.div_id
	$filter
	ORDER BY name";
	$ret = array();
	$res = db($sql);
	while ($r = mysql_fetch_assoc($res)) {
		$r['jmlCuti'] = 0;
		for ($i = 1; $i <= 12; ++$i) {
			$r['jmlCuti'] += isset($arrCuti[$r[id]][$i]) ? $arrCuti[$r[id]][$i] : 0;
		}
  	// if($r['jmlCuti'] != 0){
		$r['sisaCuti'] = $jatahCuti - $r['jmlCuti'];
		$ret[] = $r;
  	// }
	}

	return isset($ret) ? $ret : array();
}

?>

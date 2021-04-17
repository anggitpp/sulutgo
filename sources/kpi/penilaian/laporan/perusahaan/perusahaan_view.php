<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json, $cNama;

if(!isset($par[idSetting]))
	$par[idSetting] = getField("SELECT idSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai LIMIT 1");

$infoSetting = getField("SELECT CONCAT(t1.namaSetting, '~', t1.pelaksanaanMulai, '~', t1.pelaksanaanSelesai, '~', t2.kodeKonversi) FROM pen_setting_penilaian t1 JOIN pen_setting_kode t2 ON t2.idKode = t1.idKode WHERE t1.idSetting = '$par[idSetting]'");
list($namaSetting, $pelaksanaanMulai, $pelaksanaanSelesai, $kodeKonversi) = explode("~", $infoSetting);

if($json == 2){
	echo json_encode(array("namaSetting" => $namaSetting, "tanggalPelaksanaan" => getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai)));
	exit();
}

if($json == 1){
	header("Content-type: application/json");

	$filter = "WHERE t3.idPenilai = '$cID'";
	if(!empty($par[idSetting]))
		$filter .= " AND t3.idSetting = '$par[idSetting]'";
	$sql = "
	SELECT 
	t1.namaAspek, t1.penilaianAspek, t1.targetAspek, t3.nilaiPenilaian,
	(
	SELECT 
	IFNULL(
	(
	SELECT 
	warnaKonversi 
	FROM pen_setting_konversi 
	WHERE 
	(
	t3.nilaiPenilaian BETWEEN nilaiMin AND nilaiMax) AND kodeKonversi = '$kodeKonversi'
	), '#FF0000')) as warnaKonversi
	FROM pen_setting_aspek t1
	JOIN pen_penilaian_detail t2 
	ON t2.idAspek = t1.idAspek 
	JOIN pen_penilaian t3
	ON t3.idPenilaian = t2.idPenilaian
	$filter 
	GROUP BY t1.idAspek
	ORDER BY t1.urutanAspek ASC
	";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_assoc($res)){
		$ret[] = $r;
	}

	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par, "mode,dlg") ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
		<p style="position: absolute; right: 20px; top: 127px;">
			<a href="#export" id="btnExport" class="btn btn1 btn_inboxo"><span>Export</span></a> &nbsp; <?= comboData("SELECT idSetting, namaSetting FROM pen_setting_penilaian WHERE statusSetting = 't' ORDER BY pelaksanaanMulai", "idSetting", "namaSetting", "par[idSetting]", "", $par[idSetting], "", "225px"); ?>
		</p>
		<p>
			<label class="l-input-small">Penilaian</label>
			<span class="field">
				<span id="displayNamaSetting"><?= $namaSetting ?></span>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">Pelaksanaan</label>
			<span class="field">
				<span id="displayTanggalPelaksanaan"><?= getTanggal($pelaksanaanMulai)."&nbsp; s/d &nbsp;".getTanggal($pelaksanaanSelesai) ?></span>&nbsp;
			</span>
		</p>
	</form>
	<br clear="all">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20">NO</th>
				<th width="250">ASPEK PENILAIAN</th>
				<th>KETERANGAN</th>
				<th width="80">TARGET</th>
				<th width="80">HASIL</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<?php
	if($par[mode] == "export"){			
		echo "<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]))."_".$cNama.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}
	?>	
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": sajax + "&json=1",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "namaAspek", "bSortable": false},
			{"mData": "penilaianAspek", "bSortable": false},
			{"mData": "targetAspek", "bSortable": false, "sClass": "alignCenter"},
			{"mData": "nilaiPenilaian", "bSortable": false, "sClass": "alignCenter"}
			],
			"aaSorting": [[0, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");

				jQuery("td:nth-child(5)", nRow).css('background', aData['warnaKonversi']).css("color", "#FFFFFF").css("font-weight", "bold").html(Number(aData['nilaiPenilaian']).toFixed(2));

				return nRow;
			},
			"bProcessing": true,
			"oLanguage": {
				"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

		jQuery("#par\\[idSetting\\]").live("change", function (e) {
			e.preventDefault();
			jQuery.ajax({
				url: sajax + "&json=2&par[idSetting]=" + jQuery("#par\\[idSetting\\]").val(),
				type: "GET",
				dataType: "json",
				success: function(data){
					jQuery("#displayNamaSetting").html(data['namaSetting']);
					jQuery("#displayTanggalPelaksanaan").html(data['tanggalPelaksanaan']);
				}
			});
			ot.fnReloadAjax(sajax + "&json=1&par[idSetting]=" + jQuery("#par\\[idSetting\\]").val());
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});

		jQuery("#btnExport").live("click", function(e){
			e.preventDefault();
			window.location = '?par[mode]=export&par[idSetting]=' + jQuery("#par\\[idSetting\\]").val() + '<?= getPar($par, "mode,dlg,idSetting") ?>';
		});
	});
</script>
<?php
global $s, $par, $menuAccess, $arrTitle, $json;

if(!isset($par[idKode]))
	$par[idKode] = getField("SELECT idKode FROM pen_setting_kode WHERE statusKode='t' LIMIT 1");

if($json==1){
	header("Content-type: application/json");

	$filter = "WHERE t1.statusTipe='t'";
	if(!empty($par[idKode])){
		$infoKode = getField("SELECT CONCAT(kodeAspek, '~', kodePrespektif) FROM pen_setting_kode WHERE idKode = '$par[idKode]'");
		list($kodeAspek, $kodePrespektif) = explode("~", $infoKode);
	}

	$sql = "
	SELECT 
	t1.kodeTipe, t1.namaTipe, 
	(SELECT AVG(nilaiDetail) FROM pen_setting_aspek_detail x1 JOIN pen_setting_aspek x2 ON x2.idAspek = x1.idAspek WHERE x2.kodeAspek = '$kodeAspek') targetAspek, 
	(SELECT AVG(nilaiDetail) FROM pen_setting_prespektif_detail x1 JOIN pen_setting_prespektif x2 ON x2.idPrespektif = x1.idPrespektif WHERE x2.kodePrespektif = '$kodePrespektif') targetPrespektif, 
	(SELECT COUNT(*) FROM pen_sasaran WHERE kodeTipe = t1.kodeTipe ) totalSubyektif
	FROM pen_tipe t1
	$filter
	GROUP BY 
	t1.kodeTipe
	";
	/*$sql = "
	SELECT 
	t1.kodeTipe, t1.namaTipe, 
	--(SUM(t2.nilaiDetail) / 2)-- 0 targetAspek, 
	--(SUM(t4.nilaiDetail) / 2)-- 0targetPrespektif, 
	(SELECT COUNT(*) FROM pen_sasaran WHERE kodeTipe = t1.kodeTipe AND idKode = '$par[idKode]') totalSubyektif
	FROM pen_tipe t1
	--LEFT JOIN pen_setting_aspek_detail t2 
	ON t2.kodeTipe = t1.kodeTipe
	LEFT JOIN pen_setting_aspek t3 
	ON t3.idAspek = t2.idAspek
	LEFT JOIN pen_setting_prespektif_detail t4
	ON t4.kodeTipe = t1.kodeTipe
	LEFT JOIN pen_setting_prespektif t5
	ON t5.idPrespektif = t4.idPrespektif--
	$filter
	GROUP BY 
	t1.kodeTipe
	";*/
	// echo $sql;
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){
		$r[targetKorporat] = getAngka(($r[targetAspek] + $r[targetPrespektif]) / 2, 2);
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
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20" style="vertical-align: middle">NO.</th>
				<th style="vertical-align: middle">KODE PENILAIAN</th>
				<th width="200" style="vertical-align: middle">TARGET</th>
				<th width="150" style="vertical-align: middle">OBYEKTIF</th>
				<th width="150" style="vertical-align: middle">INDIKATOR PENILAIAN</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var idKode = 0;
	jQuery(document).ready(function() {
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": sajax + "&json=1",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "namaTipe", "bSortable": true},
			{"mData": "targetKorporat", "sClass": "alignCenter", "bSortable": false},
			{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
				var ret = "";
				<?php
				if(isset($menuAccess[$s]['edit'])){
					?>
					ret += "<a href=\"#edit\" onclick=\"edit('" + o.aData['kodeTipe'] + "');\" title=\"Edit Data\">";
					<?php
				}
				?>
				if(parseFloat(o.aData['totalSubyektif']) > 0){
					ret += "<img src=\"styles/images/t.png\" />";
				}else{
					ret += "<img src=\"styles/images/f.png\" />";
				}

				<?php
				if(isset($menuAccess[$s]['edit'])){
					?>
					ret += "</a>";
					<?php
				}
				?>

				return ret;
			}},
			{"mData": "totalSubyektif", "bSortable": false, "sClass": "alignCenter"},
			],
			"aaSorting": [[0, "asc"]],
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

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label').empty();
		//jQuery('#datatable_wrapper #datatable_filter > label').append('Kode Penilaian <?= comboData("SELECT idKode, namaKode FROM pen_setting_kode WHERE statusKode='t'", "idKode", "namaKode", "par[idKode]", "", $par[idKode], "onchange=\"this.form.submit();\"", "180px"); ?>');

		idKode = jQuery("#par\\[idKode\\]").val();
		jQuery("#par\\[idKode\\]").live("change", function (e) {
			e.preventDefault();
			idKode = jQuery("#par\\[idKode\\]").val();
			ot.fnReloadAjax(sajax + "&json=1&par[idKode]=" + jQuery("#par\\[idKode\\]").val());
		});

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});

	function edit(kodeTipe){
		window.location = '?par[mode]=edit&par[idKode]=' + idKode + '&par[kodeTipe]=' + kodeTipe + '<?= getPar($par, "mode,idKode") ?>';
	}
</script>
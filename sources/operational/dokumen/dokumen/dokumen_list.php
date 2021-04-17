<?php

$arr_status_image 	= ["t" => "<img src=\"styles/images/t.png\">", "f" => "<img src=\"styles/images/f.png\">"];

if ($json == 1) {

	header("Content-type: application/json");

	$ret 		= [];
	$arr_master = arrayQuery("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = '$arrParam[$s]'");

	$filter = !empty($par['category_id']) ? "AND `kategoriDokumen` = '$par[category_id]'" : "";

	$res = db("SELECT * FROM `pen_dokumen` WHERE `menu_id` = '$s' $filter");
	
	while ($r = mysql_fetch_assoc($res)) {

		$r['kategori']		= $arr_master[$r['kategoriDokumen']];
		$r['fileDokumen'] 	= empty($r['fileDokumen']) ? "-" : " <a href=\"download.php?d=dokumen_penilaian&f=$r[kodeDokumen]\"><img src=\"" . getIcon($r['fileDokumen']) . "\" title=\"Download\"></a>";
		$r['status']		= $arr_status_image[$r['statusDokumen']];

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
				<th style="vertical-align: middle">JUDUL</th>
				<th width="150" style="vertical-align: middle">PENERBIT</th>
				<th width="150" style="vertical-align: middle">TUJUAN</th>
				<th width="120" style="vertical-align: middle">KATEGORI</th>
				<th width="20" style="vertical-align: middle">FILE</th>
				<th width="20" style="vertical-align: middle">STATUS</th>
				<?php
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
					?>
					<th width="80" style="vertical-align: middle">KONTROL</th>
					<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<script type="text/javascript">

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
			{"mData": "judulDokumen", "bSortable": true},
			{"mData": "penerbitDokumen", "sClass": "alignCenter", "bSortable": false},
			{"mData": "tujuanDokumen", "bSortable": false},
			{"mData": "kategori", "bSortable": false},
			{"mData": "fileDokumen", "bSortable": false, "sClass": "alignCenter"},
			{"mData": "status", "bSortable": false, "sClass": "alignCenter"},
			<?php
			if (isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
				?>
				{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
					var ret = "";
					<?php
					if (isset($menuAccess[$s]['edit'])){
						?>
						ret += "<a onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" + o.aData['kodeDokumen'] + "<?= getPar($par, "mode") ?>',  900, 600);\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";
						<?php 
					}
					?>
					<?php
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"?par[mode]=del&par[id]=" + o.aData['kodeDokumen'] + "<?= getPar($par, "mode") ?>\" onclick=\"return confirm('are you sure to delete data ?');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
						<?php 
					}
					?>
					return ret;
				}},
				<?php 
			}
			?>
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
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboData("SELECT `kodeData`, `namaData` FROM `mst_data` WHERE `kodeCategory` = '$arrParam[$s]' AND `statusData` = 't'", "kodeData", "namaData", "par[category_id]", "All", $par['category_id'], "", "110px"); ?>');

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		<?php
		if (isset($menuAccess[$s]['add'])) {
			?>
			jQuery("#datatable_wrapper #right_panel").append("<a onclick=\"openBox('popup.php?par[mode]=add<?= getPar($par, "mode"); ?>', 900, 600);\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});

		jQuery("#par\\[category_id\\]").live("change",function(e) {
			e.preventDefault();
			ot.fnReloadAjax(sajax + "&json=1&par[category_id]=" + jQuery("#par\\[category_id\\]").val());
		});
		

	});

</script>
<?php
global $s, $par, $menuAccess, $arrTitle, $json;
if($json == 1){
	header("Content-type: application/json");

	$filter = "WHERE idSetting IS NOT NULL";
	if(!empty($par[tipePenilaian]))
		$filter .= " AND t2.kodeTipe = '$par[tipePenilaian]'";
	$sql = "
	SELECT 
	t1.* 
	FROM pen_setting_penilaian t1 join pen_setting_kode t2 on t1.idKode = t2.idKode
	$filter
	ORDER BY 
	YEAR(pelaksanaanMulai) DESC
	";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){
		$r[skSetting] = empty($r[skSetting]) ? "-" : "
		<a href=\"download.php?d=pen_setting_penilaian&amp;f=$r[idSetting]\">
			<img src=\"".getIcon($r[skSetting])."\" title=\"Download\">
		</a>";
		$r[pelaksanaanMulai] = $r[pelaksanaanMulai] == "0000-00-00" ? "-" : getTanggal($r[pelaksanaanMulai]);
		$r[pelaksanaanSelesai] = $r[pelaksanaanSelesai] == "0000-00-00" ? "-" : getTanggal($r[pelaksanaanSelesai]);
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
				<th rowspan="2" width="20" style="vertical-align: middle">NO.</th>
				<th rowspan="2" style="vertical-align: middle">PENILAIAN</th>
				<th colspan="2" width="250" style="vertical-align: middle">PELAKSANAAN</th>
				<th rowspan="2" width="100" style="vertical-align: middle">SURAT <br> KEPUTUSAN</th>
				<th rowspan="2" width="80" style="vertical-align: middle">STATUS</th>
				<?php
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
					?>
					<th rowspan="2" width="80" style="vertical-align: middle">KONTROL</th>
					<?php
				}
				?>
			</tr>
			<tr>
				<th width="125">MULAI</th>
				<th width="125">SELESAI</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
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
			{"mData": "namaSetting", "bSortable": true},
			{"mData": "pelaksanaanMulai", "sClass": "alignCenter", "bSortable": false},
			{"mData": "pelaksanaanSelesai", "sClass": "alignCenter", "bSortable": false},
			{"mData": "skSetting", "sClass": "alignCenter", "bSortable": false},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				var ret = "";
				if(o.aData['statusSetting'] == "t")
					ret += "<img src=\"styles/images/t.png\" title=\"Aktif\" />";
				else
					ret += "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\" />";

				return ret;
			}},
			<?php
			if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
				?>
				{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
					var ret = "";
					<?php
					if(isset($menuAccess[$s]['edit'])){
						?>
						ret += "<a href=\"#edit\" onclick=\"openBox('popup.php?par[mode]=edit&par[idSetting]=" + o.aData['idSetting'] + "<?= getPar($par, "mode") ?>',  800, 485);\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";
						<?php 
					}
					?>

					<?php
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"?par[mode]=del&par[idSetting]=" + o.aData['idSetting'] + "<?= getPar($par, "mode") ?>\" onclick=\"return confirm('are you sure to delete data ?');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
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
		jQuery("#datatable_wrapper #datatable_filter").append('<?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "par[tipePenilaian]", "All", $par[tipePenilaian], "", "110px"); ?>');
		jQuery("#par\\[tipePenilaian\\]").live("change", function (e) {
			e.preventDefault();
			ot.fnReloadAjax("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
			
		});

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("<a href=\"#add\" onclick=\"openBox('popup.php?par[mode]=add<?= getPar($par, "mode"); ?>', 800, 485);\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});

		jQuery( ".hasDatePicker" ).datepicker({
			dateFormat: "dd/mm/yy"
		});
	});
</script>
<?php
/* End of file setting_penilaian_list.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/setting_penilaian/setting_penilaian_list.php */
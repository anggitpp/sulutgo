<?php
global $s, $par, $menuAccess, $arrTitle, $cID, $json, $type;
if($json == 1){
	header("Content-type: application/json");

	$filter = "WHERE idSetting IS NOT NULL";
	if(!empty($par[tipePenilaian]))
		$filter .= " AND x2.kodeTipe = '$par[tipePenilaian]'";
	

	$selectBawahan = "";
	if(empty($type))
		$selectBawahan = "
	(
	CASE 
	WHEN 
	(SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting) > 0 
	THEN
	(SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting)
	ELSE 
	(SELECT COUNT(*) FROM dta_pegawai t1 JOIN pen_pegawai t2 ON t2.idPegawai = t1.id WHERE t1.leader_id = '$cID')
	END
	) jumlahBawahan,
	";
	else
		$selectBawahan = "
	(SELECT COUNT(*) FROM dta_pegawai t1 JOIN pen_pegawai t2 ON t2.idPegawai = t1.id) jumlahBawahan,
	";

	$sql = "
	SELECT 
	t1.*,
	$selectBawahan
	(
	CASE 
	WHEN 
	((SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting) > 0) AND (SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting) = (SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting AND apprStatus = 't')
	THEN
	'green'
	WHEN 
	((SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting) > 0) AND (SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting) > (SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting AND apprStatus = 't')
	THEN
	'yellow'
	WHEN 
	(SELECT COUNT(*) FROM pen_penilaian WHERE idPenilai = '$cID' AND idSetting = t1.idSetting AND apprStatus = 't') = 0
	THEN
	'red'
	END
	) warnaPenilaian
	FROM pen_setting_penilaian t1 join pen_setting_kode x2 on t1.idKode = x2.idKode
	$filter
	AND t1.statusSetting = 't'
	ORDER BY 
	YEAR(t1.pelaksanaanMulai) DESC
	";
	// echo $sql;
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
				<th rowspan="2" width="100" style="vertical-align: middle">JUMLAH <br> BAWAHAN</th>
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
			{"mData": null, "bSortable": false, "sClass": "alignCenter"},
			],
			"aaSorting": [[0, "asc"]],
			"fnInitComplete": function (oSettings) {
				oSettings.oLanguage.sZeroRecords = "No data available";
			}, "sDom": "<'top'f>rt<'bottom'lip><'clear'>",
			"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				jQuery("td:first", nRow).html((iDisplayIndexFull + 1) + ".");
				jQuery("td:last", nRow).css('background', aData['warnaPenilaian']).html("<a href='?par[mode]=view&par[idSetting]=" + aData['idSetting'] + "<?= getPar($par, 'mode,pelaksanaanMulai,pelaksanaanSelesai') ?>' title='Lihat data' style='color: white; padding: 10px 40px; text-decoration: none;'><b>" + aData['jumlahBawahan'] + "</b></a>");
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
/* End of file penilaian_tahunan_list.php */
/* Location: .//var/www/html/bdp-outsourcing/intranet/sources/penilaian/penilaian/penilaian_tahunan/penilaian_tahunan_list.php */
<?php
global $s, $par, $arrTitle, $json, $arrParameter, $areaCheck, $cUsername;

if($json == 1){
	header("Content-type: application/json");

	$filter = "WHERE t1.id IS NOT NULL";
	if(!empty($par[dept_id])){
		$filter .= " AND t1.dept_id = '$par[dept_id]'";
	}

	if(!empty($par[existing]))
		$filter .= " AND t1.id NOT IN ($par[existing])";

	$sql = "
	SELECT 
	t1.id, 
	t1.name, t1.reg_no, t1.pos_name, t2.namaData posisi, t1.birth_date
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.dept_id
	$filter
	";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_assoc($res)){
		$r['birth_date'] = (getTanggal($r['birth_date']) ? getAngka(selisihTahun($r['birth_date'], date("Y-m-d"))) : "0");
		$ret[] = $r;
	}
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));
	exit();
}else if($json == 2){
	header("Content-type: application/json");

	$sql = "
	SELECT 
	t1.id, 
	t1.name, t1.reg_no, t1.pos_name, t2.namaData posisi, t1.birth_date
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 
	ON t2.kodeData = t1.dept_id
	WHERE id IN (".$_GET['ids'].")
	";
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_assoc($res)){

		$r['birth_date'] = (getTanggal($r['birth_date']) ? getAngka(selisihTahun($r['birth_date'], date("Y-m-d"))) : "0");

		$nextId = getField("select idPeserta from plt_pelatihan_peserta where idPelatihan='".$par[idPelatihan]."' order by idPeserta desc limit 1")+1;

		$position = getField("SELECT `namaData` FROM `mst_data` t1 JOIN `emp_phist` t2 ON t1.`kodeData` = t2.`dir_id` WHERE t2.`parent_id` = '$r[id]' AND t2.`status` = '1'");

		$sql="insert into plt_pelatihan_peserta (idPelatihan, idPeserta, idPegawai, posisiPeserta, jabatanPeserta, umurPeserta, createBy, createTime) values ('$par[idPelatihan]', '$nextId', '$r[id]', '$position', '$r[pos_name]', '".setAngka($r[birth_date])."', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
	}
	echo json_encode("DATA YANG ANDA INPUT BERHASIL");
	exit();
}
?>
<style type="text/css">
	.wait {
		cursor:wait;
		pointer-events: none;
	}
</style>
<script>
window.onload = function() {	
    if (window.location.hash != '#loaded') {
        window.location = window.location + '#loaded';
        window.location.reload();
    }
}
</script>
<div class="centercontent contentpopup">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<div style="margin-top: 10px;">
			<?= getBread("Tambah Peserta") ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<?= empLocHeader(); ?>
	<div class="contentwrapper" id="contentwrapper">
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
			<thead>
				<tr>
					<th width="20">NO</th>
					<th>NAMA</th>
					<th>JABATAN</th>
					<th>DEPARTEMEN</th>
					<th>UMUR</th>
					<th width="20">
						<input type="checkbox" id="chkPesertas" />
					</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"bSort": true,
			"bFilter": true,
			"aLengthMenu": [[10], [10]],
        	"iDisplayLength": 10,
			"sPaginationType": "full_numbers",
			"sAjaxSource":  "ajax.php?json=1<?= getPar($par); ?>",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "name", "bSortable": true},
			{"mData": "pos_name"},
			{"mData": "posisi"},
			{"mData": null, "sClass": 'alignRight', "fnRender": function(o){
				return o.aData['birth_date'] + " Tahun";
			}},
			{"mData": null, "sWidth": "20", "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				return "<input type=\"checkbox\" class=\"chk\" style=\"width:50px\" id=\"chkPeserta\" value=\"" + o.aData['id'] + "\"/>";
			}},
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
				"sProcessing": "<img id=\"loader\" src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
			}
		});

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "8px").css("font-size", "14px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE kodeCategory = 'X06' AND kodeInduk not in ('952') AND statusData = 't'", "kodeData", "namaData", "par[dept_id]", "All", $par[dept_id], "", "180px", "chosen-select"); ?>');

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#add\" id=\"btnAdd\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery("#par\\[dept_id\\]").live("change", function (e) {
			e.preventDefault();
			ot.fnReloadAjax("ajax.php?json=1<?= getPar($par); ?>" + "&par[dept_id]=" + jQuery("#par\\[dept_id\\]").val());
		});

		jQuery('#chkPesertas').click(function (e) {
			if (jQuery(this).is(':checked')) {
				jQuery(this).parents('tr').addClass('selected');
			} else {
				jQuery(this).parents('tr').removeClass('selected');
			}
			jQuery('#datatable tbody input[type=checkbox]').prop("checked", this.checked);
		});

		jQuery("#datatable_wrapper .top").append("</div>");		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});

		jQuery("#btnAdd").live("click", function (e) {
			if (jQuery('input[id=chkPeserta]:checked', ot.fnGetNodes()).length > 0) {
				jQuery(this).attr("disabled", true);
				var serviceListId = "";
				jQuery('input[id=chkPeserta]:checked', ot.fnGetNodes()).each(function () {
					serviceListId += "" + jQuery(this).val() + ",";
				});
				jQuery("#datatable_processing").css("visibility", "visible");
				jQuery(this).addClass("wait");
				vIds = serviceListId.substring(0, serviceListId.length - 1);
				jQuery.ajax({
					url: 'ajax.php?json=2&ids=' + vIds + '<?= getPar($par); ?>',
					type: 'GET',
					dataType: 'json',
				}).done(function(response) {
					jQuery("#datatable_processing").css("visibility", "hidden");
					jQuery("#btnAdd").removeClass("wait");
					alert(response);
					parent.reloadPage();
					closeBox();
				});
				
			} else {
				alert("Tidak ada Data yang dipilih!");
			}
		});

		jQuery('.chosen-select').each(function(){
			var search = (jQuery(this).attr("data-nosearch") === "true") ? true : false,
			opt = {};
			if(search) opt.disable_search_threshold = 9999999;
			jQuery(this).chosen(opt);
		});
	});
</script>
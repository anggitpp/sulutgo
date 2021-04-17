<?php
$par[filterBulan] = isset($par[filterBulan]) ? $par[filterBulan] : date('m');
$par[filterTahun] = isset($par[filterTahun]) ? $par[filterTahun] : date('Y');
if ($json == 1) {
    header('Content-type: application/json');

    $filter = 'WHERE t1.idKonseling IS NOT NULL';
    if (isset($par[filterBulan])) {
        $filter .= " AND MONTH(t1.tanggalKonseling) = '$par[filterBulan]'";
    }
    if (isset($par[filterTahun])) {
        $filter .= " AND YEAR(t1.tanggalKonseling) = '$par[filterTahun]'";
    }

    $sql = "
	SELECT
	t1.idKonseling, t1.idPegawai, t1.nomorKonseling, t2.name, t2.pos_name,
	t1.tanggalKonseling, t3.namaData namaKategori
	FROM sdm_konseling t1
	JOIN dta_pegawai t2 ON t2.id = t1.idPegawai
	JOIN mst_data t3 ON t3.kodeData = t1.idKategori
	$filter
	ORDER BY t1.tanggalKonseling DESC
	";

    $ret = array();
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $r[tanggalKonseling] = getTanggal($r[tanggalKonseling]);
        $ret[] = $r;
    }

    echo json_encode(array('sEcho' => 1, 'aaData' => $ret));
    exit();
}
?>

<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread(); ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
	<form action="" class="stdform">
		<p style="position: absolute; right: 20px; top:8px;">
			<b>Tahun</b>&nbsp;&nbsp;<?= comboYear('par[filterTahun]', $par[filterTahun], '2', '', '110px') ?>
		</p>
	</form>
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20" style="vertical-align: middle">NO</th>
				<th width="200" style="vertical-align: middle">NOMOR</th>
				<th style="vertical-align: middle">NAMA</th>
				<th width="200" style="vertical-align: middle">JABATAN</th>
				<th width="120" style="vertical-align: middle">TANGGAL</th>
				<th width="100" style="vertical-align: middle">KATEGORI</th>
				<th width="80" style="vertical-align: middle">KONTROL</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<script type="text/javascript">
var afterMigrasi = false;
jQuery(document).ready(function(){
	ot = jQuery('#datatable').dataTable({
		"sScrollY": "100%",
		"bSort": true,
		"bFilter": true,
		"iDisplayStart": 0,
		"sPaginationType": "full_numbers",
		"sAjaxSource":  "ajax.php?json=1<?= getPar(); ?>",
		"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": null, "sClass": "alignCenter", "bSortable": true, "fnRender": function(o){
				return "<a href=\"?par[mode]=det&par[idKonseling]=" + o.aData['idKonseling'] + "&par[idPegawai]=" + o.aData['idPegawai'] + "<?php echo getPar(); ?>\">" + o.aData['nomorKonseling'] + "</a>";
			}},
			{"mData": "name", "bSortable": true},
			{"mData": "pos_name"},
			{"mData": "tanggalKonseling", "sClass": "alignCenter", "bSortable": true},
			{"mData": "namaKategori"},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				var ret = "";
				if(hasEdit())
					ret += "<a href=\"?par[mode]=edit&par[idKonseling]=" + o.aData['idKonseling'] + "<?php echo getPar(); ?>\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";
				if(hasDelete())
					ret += "<a href=\"?par[mode]=del&par[idKonseling]=" + o.aData['idKonseling'] + "<?php echo getPar(); ?>\" onclick=\"return confirm('are you sure to delete data?');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
				return ret;
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
		"fnDrawCallback": function( settings ) {
			if(afterMigrasi){
				alert("Migrasi data berhasil.");
				afterMigrasi = false;
			}
		},
		"bProcessing": true,
		"oLanguage": {
			"sProcessing": "<img src=\"<?php echo APP_URL ?>/styles/images/loader.gif\" />"
		}
	});

	jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px");
	jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
	jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboMonth('par[filterBulan]', $par[filterBulan], '', '110px', 'ALL') ?>');

	jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
	jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"?par[mode]=export<?= getPar(); ?>\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>");
	<?php
    if (isset($menuAccess[$s]['add'])) {
        ?>
		jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"?par[mode]=add<?= getPar();
        ?>\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
		<?php

    }
    ?>

	jQuery("#par\\[filterBulan\\], #par\\[filterTahun\\]").live("change", function (e) {
		e.preventDefault();
		ot.fnReloadAjax("ajax.php?json=1<?= getPar(); ?>&par[filterTahun]=" + jQuery("#par\\[filterTahun\\]").val() + "&par[filterBulan]=" + jQuery("#par\\[filterBulan\\]").val());
	});

	jQuery("#datatable_wrapper .top").append("</div>");
	jQuery(window).bind('resize', function () {
		ot.fnAdjustColumnSizing();
	});
});

function hasEdit(){
	<?php
    if (isset($menuAccess[$s]['edit'])) {
        ?>
		return true;
		<?php

    }
    ?>

	return false;
}

function hasDelete(){
	<?php
    if (isset($menuAccess[$s]['delete'])) {
        ?>
		return true;
		<?php

    }
    ?>

	return false;
}
</script>

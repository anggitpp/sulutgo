<?php
global $s, $par, $menuAccess, $arrTitle, $json;

if($json==1){
	header("Content-type: application/json");

	$sql = "SELECT * FROM pen_pegawai t1 left join pen_tipe t2 on (t1.tipePenilaian=t2.kodeTipe) left join pen_setting_kode t3 on t1.kodePenilaian = t3.idKode where t1.idPegawai='".$par[idPegawai]."'
	ORDER BY t1.tahunPenilaian";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		$r[tahunPenilaian] = getField("select namaData from mst_data where kodeData = $r[tahunPenilaian]");
		$r[bulanPenilaian] = getField("select namaData from mst_data where kodeData = $r[bulanPenilaian]");
        $r[periode] = getTanggal($r['periodeStart'])." s/d ".getTanggal($r['periodeEnd']);
        $r[nilaiPenilaian] = "";
        $r[hasilPenilaian] = "";
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
<div style="padding:20px; margin-top:-50px;">
<fieldset style="padding:10px; border-radius: 10px;">						
<legend style="padding:10px; margin-left:20px;"><h4>PEGAWAI</h4></legend>
<?php 
	$_SESSION["curr_emp_id"] = $par[idPegawai];	
	require_once "tmpl/__emp_header__penilaian.php";
?>
</fieldset>
</div>
<div class="contentwrapper">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20" style="vertical-align: middle">NO</th>
				<th width="100" style="vertical-align: middle">TAHUN</th>
                <th width="100" style="vertical-align: middle">REALISASI</th>
                <th width="120" style="vertical-align: middle">Periode</th>
				<th style="vertical-align: middle">TIPE PENILAIAN</th>
				<th style="vertical-align: middle">KODE PENILAIAN</th>
				<th style="vertical-align: middle">KETERANGAN</th>				
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
	jQuery(document).ready(function(){
		ot = jQuery('#datatable').dataTable({
			"sScrollY": "100%",
			"bSort": false,
			"bFilter": false,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			"sAjaxSource": sajax + "&json=1",
			"aoColumns": [
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "tahunPenilaian", "sClass": "alignCenter", "bSortable": false},
            {"mData": "bulanPenilaian", "sClass": "alignCenter", "bSortable": false},
            {"mData": "periode", "sClass": "alignCenter", "bSortable": false},
			{"mData": "namaTipe", "bSortable": false},
			{"mData": "subKode", "bSortable": false},
			{"mData": "keterangan", "bSortable": false},			
			<?php
			if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
				?>
				{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
					var ret = "";
					<?php
					if(isset($menuAccess[$s]['edit'])){
					?>
						ret += "<a href=\"#edit\" onclick=\"openBox('popup.php?par[mode]=edit&par[id]=" + o.aData['id'] + "<?= getPar($par, "mode, id") ?>', 700, 400);\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";
						<?php 
					}
					?>

					<?php
					if(isset($menuAccess[$s]['delete'])){
					?>
						ret += "<a href=\"?par[mode]=del&par[id]=" + o.aData['id'] + "<?= getPar($par, "mode, id") ?>\" onclick=\"return confirm('are you sure to delete data ?');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
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

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("<a href=\"#add\" onclick=\"openBox('popup.php?par[mode]=add<?= getPar($par, "mode"); ?>', 700, 400);\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>
		jQuery("#datatable_wrapper #right_panel").append(" <a href=\"?<?= getPar($par, "mode"); ?>\" class=\"btn btn1 btn_document\"><span>Kembali</span></a>");
		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>
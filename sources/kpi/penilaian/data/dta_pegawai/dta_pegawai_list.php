<?php
global $s, $par, $menuAccess, $arrTitle, $arrParameter, $cUsername, $json, $migrasi;
if($json==1){
	header("Content-type: application/json");

	$filter = "WHERE t1.id is not null";
    
	if(!empty($par[tipePenilaian]))
		$filter .= " AND t4.tipePenilaian = '$par[tipePenilaian]'";

	$sql = "SELECT 
	t1.id, 
	t1.name, t1.reg_no, t1.pos_name, t2.namaData as posisi, t3.namaData as lokasi, 
	t5.namaTipe tipePenilaian
	FROM dta_pegawai t1 
	LEFT JOIN mst_data t2 ON t2.kodeData = t1.div_id
	LEFT JOIN mst_data t3 ON t3.kodeData = t1.dir_id
	LEFT JOIN pen_pegawai t4 ON t4.idPegawai = t1.id
	LEFT JOIN pen_tipe t5 ON t5.kodeTipe = t4.tipePenilaian
	$filter";
	
	/*echo $sql;*/
	$res = db($sql);
	$ret = array();
	while($r = mysql_fetch_array($res)){
		if($migrasi == 1){
			$isExist = getField("SELECT id FROM pen_pegawai WHERE idPegawai = '$r[id]'");
			if(!$isExist){
				$nextId = getField("SELECT id FROM pen_pegawai ORDER BY id DESC LIMIT 1")+1;
				$sql = "INSERT INTO pen_pegawai (id, idPegawai, tipePenilaian, groupPenilaian, tanggalPenilaian, createBy, createDate) VALUES ('$nextId', '$r[id]', '0', '0', '0000-00-00', '$cUsername', '".date("Y-m-d H:i:s")."')";
				db($sql);
			}
		}
		$r[tipePenilaian] = empty($r[tipePenilaian]) ? "<img src=\"styles/images/f.png\" >" : $r[tipePenilaian];
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
				<th width="120" style="vertical-align: middle">NAMA</th>
				<th width="80" style="vertical-align: middle">NPP</th>
				<th width="100" style="vertical-align: middle">JABATAN</th>
				<th width="80" style="vertical-align: middle">UNIT KERJA</th>
				<th width="80" style="vertical-align: middle">LOKASI</th>
				<th width="80" style="vertical-align: middle">PENILAIAN</th>
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
			"sAjaxSource":  "ajax.php?json=1<?= getPar($par, "mode"); ?>",
			"aoColumns": [
			{"mData": null, "sClass": "alignCenter", "bSortable": false},
			{"mData": "name", "bSortable": true},
			{"mData": "reg_no", "sClass": "alignCenter", "bSortable": true},
			{"mData": "pos_name"},
			{"mData": "posisi"},
			{"mData": "lokasi"},
			{"mData": "tipePenilaian", "sClass": "alignCenter", "fnRender": function(o){
				var ret = "";
				<?php
				if(isset($menuAccess[$s]['edit'])){
					?>
					ret += "<a href=\"?par[mode]=det&par[idPegawai]=" + o.aData['id'] + "<?= getPar($par, "mode") ?>\" >" + o.aData['tipePenilaian'] + "</a>";
					<?php
				}else{
					?>
					ret += o.aData['tipePenilaian'];
					<?php
				}
				?>
				return ret;
			}},
			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
				var ret = "";
				<?php
				if(isset($menuAccess[$s]['delete'])){
					?>
					ret += "<a class=\"edit\" href=\"#\" onclick=\"openBox('popup.php?par[mode]=dataTambah&par[idPegawai]=" + o.aData['id'] + "<?= getPar($par, "mode"); ?>', 900, 500);\"></a> ";
                    ret += "<a class=\"delete\" href=\"?par[mode]=deletePeg&par[idPegawai]=" + o.aData['id'] + "<?= getPar($par, "mode") ?>\" onclick=\"return confirm('are you sure to delete data ?');\"></a>";
					<?php
				}else{
					?>
					ret += o.aData['tipePenilaian'];
					<?php
				}
				?>
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

		jQuery('#datatable_wrapper #datatable_filter').css("float", "left").css("position", "relative").css("margin-left", "14px").css("font-size", "14px").css("margin-bottom", "20px");
		jQuery('#datatable_wrapper #datatable_filter > label > img').css("margin-top", "8px");
		
		//jQuery("#datatable_wrapper #datatable_filter").append('&nbsp;&nbsp;<?= comboData("SELECT parameterMenu, namaMenu FROM app_menu WHERE kodeInduk = '79' AND statusMenu='t' and parameterMenu!=''", "parameterMenu", "namaMenu", "par[empType]", "All", $par[empType], "", "110px"); ?>');

		jQuery("#datatable_wrapper #datatable_filter").append('<?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "par[tipePenilaian]", "All", $par[tipePenilaian], "", "110px"); ?>');

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			/*jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#migrasi\" id=\"btnMigrasi\" class=\"btn btn1 btn_document\"><span>Migrasi Data</span></a>");

			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#tambah\" id=\"btnTambah\" class=\"btn btn1 btn_document\"><span>Sinkronisasi</span></a>");*/

			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#tambahData\" id=\"btnTambahData\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");

			jQuery("#btnTambah").live("click", function (e) {
				openBox('popup.php?par[mode]=data<?= getPar($par, "mode"); ?>', 900, 500);
			});

			jQuery("#btnTambahData").live("click", function (e) {
				openBox('popup.php?par[mode]=dataTambah<?= getPar($par, "mode"); ?>', 900, 500);
			});
			
			jQuery("#btnMigrasi").live("click", function (e) {
				e.preventDefault();
				afterMigrasi = true;
				jQuery('#datatable_processing', jQuery('#datatable_wrapper')).css("visibility", "visible");
				ot.fnReloadAjax("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&migrasi=1&par[empType]=" + jQuery("#par\\[empType\\]").val() + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
			});
			<?php
		}
		?>

		jQuery("#par\\[empType\\], #par\\[tipePenilaian\\]").live("change", function (e) {
			e.preventDefault();
			ot.fnReloadAjax("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val() + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
			// console.log("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val() + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
		});

		jQuery("#datatable_wrapper .top").append("</div>");		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>
<?php 
global $s, $par, $menuAccess, $arrTitle, $json;
if (empty($par['tipePenilaian'])) {
	$par['tipePenilaian'] = $_COOKIE['tipePenilaian'];
}
if($json==1){
	header("Content-type: application/json");

	$filter = "WHERE idKode IS NOT NULL";
	if(!empty($par[tipePenilaian]))
		$filter .= " AND kodeTipe = '$par[tipePenilaian]'";
	$sql = "
	SELECT 
	*
	FROM pen_setting_kode 
	$filter";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		list($r[tahunMulai]) = explode("-", $r[tanggalMulai]);
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
				<th width="150" style="vertical-align: middle">KODE</th>
				<th  style="vertical-align: middle">PENILAIAN</th>
				<th width="80" style="vertical-align: middle">STATUS</th>
				<?php 
				if(isset($menuAccess[$s]['edit'])){
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
	var afterMigrasi = false;
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
			{"mData": "namaKode", "bSortable": true},
			{"mData": "subKode", "bSortable": true},
			{"mData": "statusKode", "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
				var ret = "";
				if(o.aData['statusKode'] == "t")
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
						ret += "<a href=\"?par[mode]=edit&par[idKode]=" + o.aData['idKode'] + "<?= getPar($par, "mode") ?>\" class=\"edit\"><span>Edit Data</span></a>";
						<?php
					}
					?>
					<?php
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"?par[mode]=del&par[idKode]=" + o.aData['idKode'] + "<?= getPar($par, "mode") ?>\" onclick=\"return del('" + o.aData['idKode'] + "');\" class=\"delete\"><span>Delete Data</span></a>";
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
		jQuery("#datatable_wrapper #datatable_filter").append('<?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t' order by namaTipe asc", "kodeTipe", "namaTipe", "par[tipePenilaian]", "", $par[tipePenilaian], "", "250px"); ?>');

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");
		jQuery("#par\\[tipePenilaian\\]").live("change", function (e) {
			e.preventDefault();
			ot.fnReloadAjax("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
			// console.log("ajax.php?json=1<?= getPar($par, "mode"); ?>" + "&par[empType]=" + jQuery("#par\\[empType\\]").val() + "&par[tipePenilaian]=" + jQuery("#par\\[tipePenilaian\\]").val());
			setCookie(jQuery("#par\\[tipePenilaian\\]").val());
		});
		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"?par[mode]=add<?= getPar($par, "mode") ?>\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery("#datatable_wrapper .top").append("</div>");		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
		jQuery( ".hasDatePicker" ).datepicker({
			dateFormat: "dd/mm/yy"
		});
	});

	function del(idKode){
		jQuery.ajax({
			url: sajax + "&par[mode]=chk&par[idKode]=" + idKode,
			type: 'GET',
			dataType: 'text'
		}).done(function(response) {
			if(response){
				alert(response);
			}else{
				if(confirm('are you sure to delete data ?')){  
					window.location = "?par[mode]=del&par[idKode]="+ idKode + "<?= getPar($par, "mode") ?>";
				}
			}
		});
		return false;
	}
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}

	function setCookie(elem){
		createCookie("tipePenilaian",elem);
	}
</script>
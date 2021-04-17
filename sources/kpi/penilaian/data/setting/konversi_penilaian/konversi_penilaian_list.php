<?php 
global $s, $par, $menuAccess, $arrTitle, $json;

if (empty($par['idPeriode'])) $par['idPeriode'] = getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1");

if($json==1){
	header("Content-type: application/json");

	$filter = "WHERE idKonversi IS NOT NULL";
	if(!empty($par[idPeriode]))
		$filter .= " AND idPeriode = '$par[idPeriode]'";

	$sql = "
	SELECT 
	*
	FROM pen_setting_konversi 
	$filter";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
	    $r[nilaiMin] = (strlen($r[nilaiMin]) == 1) ? $r[nilaiMin].".00" : $r[nilaiMin];
        $r[nilaiMax] = (strlen($r[nilaiMax]) == 1) ? $r[nilaiMax].".00" : $r[nilaiMax];
		$ret[] = $r;
	}
	// echo $sql;

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
				<th width="120" style="vertical-align: middle">NILAI</th>
				<th width="120" style="vertical-align: middle">KODE</th>
				<th style="vertical-align: middle">PREDIKAT YUDICIUM</th>
                <th width="80" style="vertical-align: middle">WOM</th>
				<?php 
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) ){
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
	var kodeKonversi = 0;
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
			{"mData": null, "sClass": "alignCenter", "bSortable": true, "fnRender": function(o){
				var ret = "";
				ret += "<b>" + o.aData['nilaiMin'] + " - " + o.aData['nilaiMax'] + "</b>";
				return ret;
			}},
			{"mData": "uraianKonversi", "sClass": "alignCenter", "bSortable": false},
			{"mData": "penjelasanKonversi", "bSortable": false}
            
            ,
			{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
				var ret = "";
				ret += "<span style=\"background-color: " + o.aData['warnaKonversi'] + "\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
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
						ret += "<a href=\"#edit\" onclick=\"editBox('" + o.aData['idKonversi'] + "');\" class=\"edit\"><span>Edit Data</span></a>";
						<?php 
					}
					?>

					<?php
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"#del\" onclick=\"del('" + o.aData['idKonversi'] + "');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
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
		jQuery("#datatable_wrapper #datatable_filter").append('<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "", "110px"); ?>');
		

		jQuery("#datatable_wrapper .top").append("<div id=\"right_panel\" class='dataTables_filter' style='float:right; top: 0px; right: 0px'>");

		<?php
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#dlg\" id=\"btnAmbil\" class=\"btn btn1 btn_inboxi\"><span>Ambil Data</span></a>");
			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"#add\" id=\"btnAdd\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery("#par\\[idPeriode\\]").live("change", function (e) {
			ot.fnReloadAjax("ajax.php?<?= getPar($par, "mode,idPeriode") ?>&json=1&par[idPeriode]=" + jQuery("#par\\[idPeriode\\]").val());					
		});

		jQuery("#btnAmbil").live("click", function(e){
			e.preventDefault();
			openBox('popup.php?par[mode]=dlg&par[idPeriode]=' + jQuery("#par\\[idPeriode\\]").val() + '<?= getPar($par, "mode,idPeriode") ?>', 600,250);
		});

		jQuery("#btnAdd").live("click", function(e){
			e.preventDefault();
			openBox('popup.php?par[mode]=add&par[idPeriode]=' + jQuery("#par\\[idPeriode\\]").val() + '<?= getPar($par, "mode,idPeriode") ?>', 800,450);
		});

		jQuery("#datatable_wrapper .top").append("</div>");		
		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
		jQuery( ".hasDatePicker" ).datepicker({
			dateFormat: "dd/mm/yy"
		});
		if(jQuery('.chosen-select').length > 0)
		{
			jQuery('.chosen-select').each(function(){
				var search = (jQuery(this).attr("data-nosearch") === "true") ? true : false,
				opt = {};
				if(search) opt.disable_search_threshold = 9999999;
				jQuery(this).chosen(opt);
			});
		}
	});

function editBox(idKonversi){
	openBox('popup.php?par[mode]=edit&par[kodeKonversi]=' + kodeKonversi + '&par[idKonversi]=' + idKonversi + '<?= getPar($par, "mode,kodeKonversi") ?>', 800, 450);
}

function del(idKonversi){
	if(confirm('are you sure to delete data ?'))
		window.location='?par[mode]=del&par[kodeKonversi]=' + kodeKonversi + '&par[idKonversi]=' + idKonversi + '<?= getPar($par, "mode,kodeKonversi") ?>';
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
	function setCookieKode(elem){
		createCookie("kodePenilaian",elem);
	}
</script>
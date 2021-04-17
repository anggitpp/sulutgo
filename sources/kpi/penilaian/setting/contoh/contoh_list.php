<?php 
/**
* Global Definition
* $s = kodeMenu
* $par = Current Parameter (ex: ?c=7&p=27&m=287&s=287)
* $menuAccess = Array contains Menu Accesses (ex: $menuAccess[$s]['edit'])
* $arrTitle = Array contains Menu Titles
* getPar(currentPar, excludedPar) = Modify current parameter (ex: getPar($par, "mode") will remove 'par[mode]' from current parameter)
*/
global $s, $par, $menuAccess, $arrTitle;


// Required for dataTables 
if($_GET['json'] == 1){
	// Output header must be json
	header("Content-type: application/json");

	// SQL Query
	$sql = "
	SELECT 
	*
	FROM pen_contoh";

	// Create an empty array for query result
	$ret = array();
	// Database result, db($sql) will run any sql query
	$res = db($sql);
	// Common database result looping
	while($r = mysql_fetch_assoc($res)){
		// Pass current row result to the empty array
		$ret[] = $r;
	}

	// Required for dataTables, pass the query result array here
	echo json_encode(array("sEcho" => 1, "aaData" => $ret));

	// Required for dataTables
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
				<th width="20">NO</th>
				<th width="300">NAMA</th>
				<th>KETERANGAN</th>
				<th width="80">STATUS</th>
				<?php
				// If current user has access to edit or delete on the current menu, then append 'KONTROL' header
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
					?>
					<th width="80">KONTROL</th>
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
			"bSort": true,
			"bFilter": true,
			"iDisplayStart": 0,
			"sPaginationType": "full_numbers",
			// sajax means same as getPar() but it appends ajax.php at the begining
			// &json=1 means $_GET['json']
			"sAjaxSource": sajax + "&json=1",
			"aoColumns": [
			/** 
			* sClass = Extra class for <td></td>
			* bSortable = true means it can be sort, vice versa
			* mData = Array key from query result, remember the $ret variable above
			*/

			// We use 'null' here because this column is for row number
			{"mData": null, "sClass": "alignRight", "bSortable": false},
			{"mData": "namaContoh", "bSortable": false},
			{"mData": "keteranganContoh", "bSortable": false},
			/** 
			* fnRender = Ignores mData property, function(o) = o.aData if you want to access query result( ex: o.aData['namaContoh'] )
			*/
			{"mData": "statusContoh", "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
				var ret = "";

				if(o.aData['statusContoh'] == 't')
					ret = "<img src=\"styles/images/t.png\" title=\"Aktif\" />";
				else
					ret = "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\" />";

				return ret;
			}},
			<?php
				// If current user has access to edit or delete on the current menu, then append 'KONTROL' column
			if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
				?>
				// We use 'null' here because this column will created dynamically
				{"mData": null, "sClass": "alignCenter", "bSortable": false, "fnRender": function(o){
					var ret = "";
					<?php
					// Check if user has edit access
					if(isset($menuAccess[$s]['edit'])){
						?>
						ret += "<a href=\"?par[mode]=edit&par[kodeContoh]=" + o.aData['kodeContoh'] + "<?= getPar($par, "mode") ?>\" class=\"edit\"><span>Edit Data</span></a>";
						<?php
					}
					?>

					<?php
					// Check if user has delete access
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"?par[mode]=del&par[kodeContoh]=" + o.aData['kodeContoh'] + "<?= getPar($par, "mode") ?>\" class=\"delete\" onclick=\"return confirm('are you sure to delete data ?');\"><span>Delete Data</span></a>";
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
		// Check if user has add access, then append 'Tambah' button
		if(isset($menuAccess[$s]['add'])){
			?>
			jQuery("#datatable_wrapper #right_panel").append("&nbsp;&nbsp;<a href=\"?par[mode]=add<?= getPar($par, "mode") ?>\" id=\"btnAdd\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>");
			<?php
		}
		?>

		jQuery(window).bind('resize', function () {
			ot.fnAdjustColumnSizing();
		});
	});
</script>
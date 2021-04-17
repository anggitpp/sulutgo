<?php

global $p,$s,$m,$menuAccess,$arrTitle,$par;





if ($_GET["json"] == 1) {

	header("Content-type: application/json");





	$sql = "SELECT * FROM catatan_sistem t1 inner join app_user t2 on t1.createdBy = t2.username";



	$res = db($sql);

	$ret = array();

	while ($r = mysql_fetch_assoc($res)) {

		$r[Tanggal] = getTanggal($r[Tanggal]);



		$ret[] = $r;

	}



	echo json_encode(array("sEcho" => 1, "aaData" => $ret));

	exit();



}



?>



<script src="sources/js/default.js"></script>

<div class="pageheader">

	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>

	<?= getBread() ?>

	<span class="pagedesc">&nbsp;</span>

</div>



<div id="contentwrapper" class="contentwrapper">

	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">



		<thead>

			<tr>

				<th width="20px"; style="vertical-align:middle;">NO</th>

				<th width="180px"; style="vertical-align:middle;">TANGGAL</th>

				<th width="200px"; style="vertical-align:middle;">CATATAN</th>

				<th width="120px"; style="vertical-align:middle;">USER</th>

				<th width="50px"; style="vertical-align:middle;">PIC</th>
				<th width="50px"; style="vertical-align:middle;">RENCANA</th>
				<th width="50px"; style="vertical-align:middle;">SELESAI</th>
				<th width="50px"; style="vertical-align:middle;">STATUS</th>

				<?php

				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){

					?>

					<th width="20" style="vertical-align: middle">KONTROL</th>

					<?php 

				}

				?>				

			</tr>

		</thead>



	</table>

</div>



<style type="text/css">



	.alignRight{

		text-align: right;

	}	



	.alignCenter{

		text-align: center;

	}



</style>



<script type="text/javascript">

	jQuery(document).ready(function () {

		ot = jQuery('#datatable').dataTable({

			"sScrollY": "100%",

			"aLengthMenu": [[20, 35, 70, -1], [20, 35, 70, "All"]],

			"bSort": true,

			"bFilter": true,

			"iDisplayStart": 0,

			"iDisplayLength": 20,

			"sPaginationType": "full_numbers",

			"sAjaxSource": "ajax.php?json=1<?= getPar($par, "mode,filterGroup"); ?>",

			"aoColumns": [

			{"mData": null, "sWidth": "20px", "bSortable": false, "sClass": "alignRight"},			

			{"mData": "Tanggal", "bSortable": true},

			{"mData": "Temuan", "bSortable": true},

			{"mData": "namaUser", "bSortable": true},

			{"mData": "PIC", "bSortable": true},
			
			{"mData": "tanggalMulai", "bSortable": true},
			
			{"mData": "tanggalSelesai", "bSortable": true},

			

			{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){

				var ret ="";

				if(o.aData['Status'] == "1")

					ret = "<img src=\"styles/images/t.png\" alt=\"Enable\" title=\"Enable\" />";

				else if(o.aData['Status'] == "2")

					ret = "<img src=\"styles/images/p.png\" alt=\"Enable\" title=\"Enable\" />";
				else 

					ret = "<img src=\"styles/images/f.png\" alt=\"Disable\" title=\"Disable\" />";

				return ret;

			}},


			<?php
			if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
				?>
				{"mData": null, "bSortable": false, "sClass": "alignCenter", "fnRender": function(o){
					var ret = "";

					<?php
					if(isset($menuAccess[$s]['edit'])){
						?>
						ret += "<a href=\"?par[mode]=edit&par[idCatatan]=" + o.aData['idCatatan'] + "<?= getPar(); ?>\" class=\"edit\" title=\"Edit Data\"><span>Edit Data</span></a>";
						<?php 
					}
					?>

					<?php
					if(isset($menuAccess[$s]['delete'])){
						?>
						ret += "<a href=\"?par[mode]=del&par[idCatatan]=" + o.aData['idCatatan'] + "<?= getPar(); ?>\" onclick=\"return confirm('are you sure to delete data?');\" class=\"delete\" title=\"Delete Data\"><span>Delete Data</span></a>";
						<?php 
					}
					?>

					return ret;
				}}
				<?php 
			}
			?>

			],

			"aaSorting": [[1, "asc"]],

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



		jQuery("#datatable_wrapper #right_panel").append("<a href=\"#export\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>");



		jQuery("#datatable_wrapper #right_panel").append('&nbsp;&nbsp;<a href="?par[mode]=add<?= getPar($par, "mode") ?>" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>');





	});

</script>


<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$emp = new Emp();
$cutil = new Common();
$ui = new UIHelper();

if ($_GET["json"] == 'excel') {
	list($fSearch, $rank, $location, $div_id, $subDept, $unitId) = explode("~", $_GET["params"]);
	$res = $emp->exportToExcelForm($fSearch, $rank, $div_id, $location, $subDept, $unitId);
	$uie = new UiHelperExtra();
	$uie->exportXLS($res, array("Data Pegawai",
		"Jabatan : " . (empty($rank) ? "ALL" : $cutil->getMstDataDesc($rank)),
		"Lokasi : " . (empty($location) ? "ALL" : $cutil->getMstDataDesc($location)),
		"Departemen : " . (empty($div_id) ? "ALL" : $cutil->getMstDataDesc($div_id)),
		"Sub Departemen : " . (empty($subDept) ? "ALL" : $cutil->getMstDataDesc($subDept)),
		"Unit : " . (empty($unitId) ? "ALL" : $cutil->getMstDataDesc($unitId))
		), "laporan_data_pegawai_" . strtolower($cutil->getMstDataDesc($par[empType])) . "_" . uniqid() . ".xlsx");
}
function hapus(){
	global $s,$inp,$par,$cUsername;
	$sql="delete from emp where id='$par[id]'";
	db($sql);

	$sql="delete from emp_phist where parent_id='$par[id]'";
	db($sql);

	$sql="delete from emp_plafon where parent_id='$par[id]'";
	db($sql);

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}	

function lihat(){
	global $s,$inp,$par,$arrParameter,$arrParam,$arrTitle,$menuAccess,$arrColor, $areaCheck, $cutil;		

	$cols = 8;
	$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
	$text = table($cols, array($cols-1, $cols));

	$par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<table style=\"width:100%\">
			<tr>
			<td style=\"width:50%; text-align:left; vertical-align:top;\">
					<table>
					<tr>
					<td style=\"vertical-align:top; padding-top:2px;\"><input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/></td>
					<td style=\"vertical-align:top;\" id=\"bView\">
						<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
						document.getElementById('bView').style.display = 'none';
						document.getElementById('bHide').style.display = 'table-cell';
						document.getElementById('dFilter').style.visibility = 'visible';							
						document.getElementById('fSet').style.height = 'auto';
					\" />
					</td>
					<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
					<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 8px;\"_search btn-small\" style=\"display:none\" onclick=\"
						document.getElementById('bView').style.display = 'table-cell';
						document.getElementById('bHide').style.display = 'none';
						document.getElementById('dFilter').style.visibility = 'collapse';							
						document.getElementById('fSet').style.height = '0px';
					\" />					
					</td>		
					</tr>
					</table>					
					<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px;\">
					<div id=\"dFilter\" style=\"visibility:collapse;\">
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">JABATAN</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData", "kodeData", "namaData", "bSearch", "--JABATAN--", $_GET['bSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">LOKASI</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "tSearch", "--LOKASI--", $_GET['tSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "pSearch", "--".strtoupper($arrParameter[39])."--", $_GET['pSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">";
								$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       where t3.kodeCategory='X04' order by t1.urutanData";
                       $text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "mSearch", $_GET['mSearch'], "", "class='chosen-select' style=\"width: 250px\"");
								$text .= "
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">";
								$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
                       where t4.kodeCategory='X04' order by t1.urutanData";
                       $text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "aSearch", $_GET['aSearch'], "", "class='chosen-select' style=\"width: 250px\"");
								$text .= "
							</div>
						</p>
					</div>
					</fieldset>
			</div>				
			</td>
			<td style=\"width:50%; text-align:right; vertical-align:top;\">
				<a href=\"#\" id=\"btnExpExcel\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>&nbsp";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?mode=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
				$text.="</td>
			</tr>
			</table>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th>Nama</th>
						<th width=\"75\">NPP</th>
						<th>Jabatan</th>
						<th width=\"100\">Tgl. Lahir</th>
						<th width=\"125\">Masa Kerja</th>
						<th width=\"50\">Detail</th>";
						if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"75\">Control</th>";
						$text.="</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<script type=\"text/javascript\">
			jQuery(document).ready(function(){
				jQuery(\"#btnExpExcel\").click(function (e) {
					e.preventDefault();
					var fSearch = jQuery(\"#fSearch\").val();
					var bSearch = jQuery(\"#bSearch\").val();
					var tSearch = jQuery(\"#tSearch\").val();
					var pSearch = jQuery(\"#pSearch\").val();
					var mSearch = jQuery(\"#mSearch\").val();
					var aSearch = jQuery(\"#aSearch\").val();

					window.open(sajax + '&json=excel&params=' + fSearch + '~' + bSearch + '~' + tSearch + '~' + pSearch + '~' + mSearch + '~' + aSearch, '_blank');
				});

				jQuery(\"#mSearch\").chained(\"#pSearch\");
			    jQuery(\"#mSearch\").trigger(\"chosen:updated\");

			    jQuery(\"#pSearch\").bind(\"change\", function () {
			      jQuery(\"#mSearch\").trigger(\"chosen:updated\");
			    });

			    jQuery(\"#aSearch\").chained(\"#mSearch\");
			    jQuery(\"#aSearch\").trigger(\"chosen:updated\");

			    jQuery(\"#mSearch\").bind(\"change\", function () {
			      jQuery(\"#aSearch\").trigger(\"chosen:updated\");
			    });
			});
		</script>";
		return $text;
	}

	function lData(){
		global $s,$par,$menuAccess,$arrParameter,$arrParam, $areaCheck;
		if(!empty($arrParam[$s]))		
			$par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$sWhere= " where status='".$status."'";
		if(!empty($par[empType]))
			$sWhere .= " and cat='".$par[empType]."'";


		if (!empty($_GET['fSearch']))
				$sWhere.= " and (				
					lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					or lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					or lower(pos_name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					)";
					if(!empty($_GET['bSearch']))
						$sWhere.=" and rank = '".$_GET['bSearch']."'";
					if(!empty($_GET['pSearch']))
						$sWhere.=" and div_id = '".$_GET['pSearch']."'";
					if(!empty($_GET['tSearch']))
						$sWhere.=" and location = '".$_GET['tSearch']."'";
					if(!empty($_GET['mSearch']))
						$sWhere.=" and dept_id = '".$_GET['mSearch']."'";
					if(!empty($_GET['aSearch']))
						$sWhere.=" and unit_id = '".$_GET['aSearch']."'";

					if(!empty($par[empType]))
						$sWhere .= " AND location IN ($areaCheck)";
					else
						$sWhere .= " AND (location IS NULL OR location = 0)";

					$arrOrder = array(	
						"name",
						"name",
						"reg_no",
						"pos_name",	
						"birth_date",
						"join_date",
					);
					$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
			$sql="select *, replace(
				case when coalesce(leave_date,NULL) IS NULL THEN
				CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
				ELSE
				CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
				END,' 0 bln','') masaKerja from dta_pegawai $sWhere order by $orderBy $sLimit";
				$res=db($sql);

				$json = array(
					"iTotalRecords" => mysql_num_rows($res),
					"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai $sWhere"),
					"aaData" => array(),
				);

				$no=intval($_GET['iDisplayStart']);
				while($r=mysql_fetch_array($res)){
					$no++;
					$statusSite=$r[statusSite] == "t" ?
					"<img src=\"styles/images/t.png\" title=\"Active\">":
					"<img src=\"styles/images/f.png\" title=\"Not Active\">";

					$controlEmp="";

					if(isset($menuAccess[$s]["edit"]))
						$controlEmp.="<a href=\"?mode=edit&id=$r[id]".getPar($par,"mode")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
					if(isset($menuAccess[$s]["delete"]))
						$controlEmp.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\" ><span>Delete</span></a>";				

					$data=array(
						"<div align=\"center\">".$no.".</div>",				
						"<div align=\"left\">".strtoupper($r[name])."</div>",
						"<div align=\"center\">".$r[reg_no]."</div>",
						"<div align=\"left\">".$r[pos_name]."</div>",
						"<div align=\"center\">".getTanggal($r[birth_date])."</div>",
						"<div align=\"right\">".$r[masaKerja]."</div>",
						"<div align=\"center\"><a href=\"?c=3&p=8&m=282&s=292&empid=$r[id]\" title=\"Detail Data\" class=\"detail\" ><span>Detail</span></a></div>",
						"<div align=\"center\">".$controlEmp."</div>",
					);


					$json['aaData'][]=$data;
				}
				return json_encode($json);
			}

			function getContent($par){
				global $s,$_submit,$menuAccess;
				switch($par[mode]){
					case "lst":
					$text=lData();
					break;	
					case "del":
					$text=hapus();
					break;	
					default:
					$text = lihat();
					break;
				}
				return $text;
			}	
			?>
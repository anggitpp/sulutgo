<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

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

	$cols = 9;
	$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
	$text = table($cols, array($cols-1, $cols));

	$par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left\">
			<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/></td>
			</div>
			<div id=\"pos_r\">				
		";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
				$text.="
			</div>

			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th width=\"100\">Tanggal</th>
						<th width=\"*\">Catatan</th>
						<th width=\"100\">User</th>
						<th width=\"100\">PIC</th>
						<th width=\"100\">RENCANA</th>
						<th width=\"100\">SELESAI</th>
						<th width=\"50\">STATUS</th>";
						if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"75\">Control</th>";
						$text.="</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			";

		// jQuery(\"#mSearch\").chained(\"#pSearch\");
		// 	    jQuery(\"#mSearch\").trigger(\"chosen:updated\");

		// 	    jQuery(\"#pSearch\").bind(\"change\", function () {
		// 	      jQuery(\"#mSearch\").trigger(\"chosen:updated\");
		// 	    });

		// 	    jQuery(\"#aSearch\").chained(\"#mSearch\");
		// 	    jQuery(\"#aSearch\").trigger(\"chosen:updated\");

		// 	    jQuery(\"#mSearch\").bind(\"change\", function () {
		// 	      jQuery(\"#aSearch\").trigger(\"chosen:updated\");
		// 	    });
		return $text;
	}

	function lData(){
		global $s,$par,$menuAccess,$arrParameter,$arrParam, $areaCheck;
		// if(!empty($arrParam[$s]))		
		// 	$par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

		// $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$sWhere= " where idCatatan is not null";


		if (!empty($_GET['fSearch']))
				$sWhere.= " and (				
					lower(Temuan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					)";
					
		$arrOrder = array(	
			"Tanggal",
			"Tanggal",
			"Temuan",
			"namaUser",
			"PIC",	
			"tanggalMulai",
			"tanggalSelesai",
			);
					$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
			$sql="select * from catatan_sistem t1 inner join app_user t2 on t1.createdBy = t2.username $sWhere order by $orderBy $sLimit";
				$res=db($sql);

				$json = array(
					"iTotalRecords" => mysql_num_rows($res),
					"iTotalDisplayRecords" => getField("select count(*) from catatan_sistem $sWhere"),
					"aaData" => array(),
				);

				$arrDept = arrayQuery("select kodeData, namaData from mst_data");

				$no=intval($_GET['iDisplayStart']);
				while($r=mysql_fetch_array($res)){
					$no++;
					switch ($r[Status]) {
						case '1':
							$r[Status] = "<img src=\"styles/images/t.png\" title=\"Selesai\">";
						break;
						case '2':
							$r[Status] = "<img src=\"styles/images/o.png\" title=\"Pending\">";
						break;
						
						default:
							$r[Status] = "<img src=\"styles/images/f.png\" title=\"Belum\">";
							break;
					}

					$controlEmp="";

					if(isset($menuAccess[$s]["edit"]))
						$controlEmp.="<a href=\"?par[mode]=edit&par[idCatatan]=$r[idCatatan]".getPar($par,"mode,idCatatan")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
					if(isset($menuAccess[$s]["delete"]))
						$controlEmp.="<a href=\"?par[mode]=del&par[idCatatan]=$r[idCatatan]".getPar($par,"mode,idCatatan")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\" ><span>Delete</span></a>";				

					$data=array(
						"<div align=\"center\">".$no.".</div>",				
						"<div align=\"left\">".getTanggal($r[Tanggal])."</div>",
						"<div align=\"left\">".$r[Temuan]."</div>",
						"<div align=\"left\">".$r[namaUser]."</div>",
						"<div align=\"left\">".$r[PIC]."</div>",
						"<div align=\"center\">".getTanggal($r[tanggalMulai])."</div>",
						"<div align=\"center\">".getTanggal($r[tanggalSelesai])."</div>",
						"<div align=\"center\">".$r[Status]."</div>",
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